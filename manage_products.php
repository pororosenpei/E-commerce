<?php
session_start();
$db = new mysqli('localhost', 'root', '', 'wastewise');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Function to get all products
function getProducts() {
    global $db;
    $result = $db->query("SELECT * FROM products ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new product
function addProduct($name, $description, $price, $stock, $category, $image_path) {
    global $db;

    // Check for existing product with the same name and category
    $check_stmt = $db->prepare("SELECT id FROM products WHERE name = ? AND category = ?");
    $check_stmt->bind_param("ss", $name, $category);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        return "Product already exists!";
    }

    // Proceed to add the product if no duplicate exists
    $stmt = $db->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $description, $price, $stock, $category, $image_path);
    return $stmt->execute();
}

// Function to update a product
function updateProduct($id, $name, $description, $price, $stock, $category, $image_path) {
    global $db;
    $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssdissi", $name, $description, $price, $stock, $category, $image_path, $id);
    return $stmt->execute();
}

// Function to delete a product
function deleteProduct($id) {
    global $db;
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $description = $_POST['description'];
        $wordCount = str_word_count($description);

        if ($wordCount < 5 || $wordCount > 20) {
            $error_message = "Description must be between 5 and 20 words.";
        } else {
            // Process image upload
            $target_dir = "uploads/";  // Relative path to uploads directory
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $image_file = $_FILES['image'];
            $image_name = basename($image_file["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($image_file["tmp_name"]);
            if($check !== false) {
                // Allow certain file formats
                if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                    if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
                        $result = addProduct($_POST['name'], $description, $_POST['price'], $_POST['stock'], $_POST['category'], $target_file);

                        if ($result === true) {
                            // Redirect to prevent resubmission
                            header("Location: " . $_SERVER['PHP_SELF']);
                            exit;
                        } else {
                            $error_message = $result; // Error from addProduct function
                        }
                    } else {
                        $error_message = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                $error_message = "File is not an image.";
            }
        }
    } elseif (isset($_POST['update_product'])) {
        // Check if a new image is uploaded
        $image_path = $_POST['existing_image']; // Default to existing image if none uploaded

        if ($_FILES['image']['name']) { // If a new image is uploaded
            $image_file = $_FILES['image'];
            $image_name = basename($image_file["name"]);
            $target_dir = "uploads/";
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($image_file["tmp_name"]);
            if ($check !== false) {
                // Allow certain file formats
                if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                    if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
                        $image_path = $target_file; // Update the image path if new image is uploaded
                    } else {
                        $error_message = "Sorry, there was an error uploading your image.";
                    }
                } else {
                    $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                $error_message = "File is not an image.";
            }
        }

        // Proceed to update the product with the existing or new image
        if (!$error_message) {
            $result = updateProduct($_POST['id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['category'], $image_path);

            if ($result === true) {
                // Redirect to prevent resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $error_message = "Failed to update the product.";
            }
        }
    } elseif (isset($_POST['delete_product'])) {
        deleteProduct($_POST['id']);
    }
}

$products = getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wastewise E-commerce Admin - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Wastewise E-commerce Admin - Manage Products</h1>
        
        <!-- Add Product Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">Add New Product</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="name" placeholder="Product Name" required class="border p-2 rounded">
                    <input type="number" name="price" placeholder="Price" step="0.01" required class="border p-2 rounded">
                    <input type="number" name="stock" placeholder="Stock" required class="border p-2 rounded">
                    <select name="category" class="border p-2 rounded">
                        <option value="Paper">Paper</option>
                        <option value="Plastic">Plastic</option>
                        <option value="Metal">Metal</option>
                        <option value="Glass">Glass</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Textiles">Textiles</option>
                    </select>
                    <textarea name="description" placeholder="Description (20-30 words)" class="border p-2 rounded col-span-2" required></textarea>
                    <input type="file" name="image" accept="image/*" required class="border p-2 rounded col-span-2">
                </div>
                <button type="submit" name="add_product" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Product</button>
            </form>
            <?php if (isset($error_message)): ?>
                <p class="text-red-500 mt-4"><?= htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Product List -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Product List</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">Image</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Price</th>
                            <th class="px-4 py-2">Stock</th>
                            <th class="px-4 py-2">Category</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="border px-4 py-2">
                                <img src="<?= $product['image']; ?>" alt="Product Image" class="w-20 h-20 object-cover">
                            </td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($product['name']); ?></td>
                            <td class="border px-4 py-2"><?= number_format($product['price'], 2); ?></td>
                            <td class="border px-4 py-2"><?= $product['stock']; ?></td>
                            <td class="border px-4 py-2"><?= $product['category']; ?></td>
                            <td class="border px-4 py-2">
                                <button onclick="editProduct(<?= $product['id']; ?>)" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</button>
                                <form action="" method="POST" class="inline-block">
                                    <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Product Form (Hidden by Default) -->
    <div id="editProductForm" class="hidden fixed inset-0 bg-gray-700 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
            <h2 class="text-xl font-semibold mb-4">Edit Product</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_existing_image" name="existing_image">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" id="edit_name" name="name" placeholder="Product Name" required class="border p-2 rounded">
                    <input type="number" id="edit_price" name="price" placeholder="Price" step="0.01" required class="border p-2 rounded">
                    <input type="number" id="edit_stock" name="stock" placeholder="Stock" required class="border p-2 rounded">
                    <select id="edit_category" name="category" class="border p-2 rounded">
                        <option value="Paper">Paper</option>
                        <option value="Plastic">Plastic</option>
                        <option value="Metal">Metal</option>
                        <option value="Glass">Glass</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Textiles">Textiles</option>
                    </select>
                    <textarea id="edit_description" name="description" placeholder="Description (20-30 words)" class="border p-2 rounded col-span-2" required></textarea>
                    <input type="file" name="image" accept="image/*" class="border p-2 rounded col-span-2">
                    <img id="edit_existing_image_preview" src="" alt="Existing Image" class="w-20 h-20 object-cover col-span-2">
                </div>
                <button type="submit" name="update_product" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Product</button>
                <button type="button" onclick="closeEditForm()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Populate the edit form with product details when "Edit" button is clicked
        function editProduct(id) {
            // Get product data (replace with real AJAX call to fetch data)
            var product = <?= json_encode($products); ?>.find(p => p.id == id);
            
            // Fill the edit form with existing product data
            document.getElementById('edit_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock').value = product.stock;
            document.getElementById('edit_category').value = product.category;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_existing_image').value = product.image;
            
            // Preview the existing image
            document.getElementById('edit_existing_image_preview').src = product.image;
            
            // Show the edit form
            document.getElementById('editProductForm').classList.remove('hidden');
        }

        // Close the edit form
        function closeEditForm() {
            document.getElementById('editProductForm').classList.add('hidden');
        }
    </script>
</body>
</html>
