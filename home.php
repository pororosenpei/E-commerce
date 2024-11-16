<?php
// home.php
session_start();
$db = new mysqli('localhost', 'root', '', 'wastewise');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Function to get products with optional search and category filter
function getProducts($search = '', $category = '') {
    global $db;
    $query = "SELECT * FROM products WHERE 1=1";
    if (!empty($search)) {
        $search = $db->real_escape_string($search);
        $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
    }
    if (!empty($category)) {
        $category = $db->real_escape_string($category);
        $query .= " AND category = '$category'";
    }
    $query .= " ORDER BY created_at DESC";
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$products = getProducts($search, $category);

$categories = ['Paper', 'Plastic', 'Metal', 'Glass', 'Electronics', 'Textiles'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wastewise E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .eco-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(52, 211, 153, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .heart {
            cursor: pointer;
            font-size: 1.5rem;
            color: gray;
        }

        .heart.filled {
            color: red;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-green-600 text-white py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold text-center">Wastewise E-commerce</h1>
            <p class="text-lg text-center mt-2">Sustainable Shopping for a Better Tomorrow</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-12">
        <p class="text-xl text-center text-gray-600 mb-8">
            Explore our collection of eco-friendly products crafted from recycled materials.
        </p>

        <!-- Search and Filter Section -->
        <div class="mb-8">
            <form action="" method="GET" class="flex flex-wrap items-center justify-center gap-4">
                <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">Search</button>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php if (empty($products)): ?>
                <p class="col-span-full text-center text-gray-600">No products found.</p>
            <?php else: ?>
                <?php foreach ($products as $index => $product): ?>
                    <div class="product-card bg-white rounded-lg overflow-hidden shadow-md relative">
                        <!-- Image Section -->
                        <div class="relative h-64">
                            <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                                <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover cursor-pointer" onclick="openModal(<?= $index; ?>)">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x300.png?text=No+Image" alt="No Image Available" class="w-full h-full object-cover cursor-pointer" onclick="openModal(<?= $index; ?>)">
                            <?php endif; ?>
                            <span class="eco-badge">Eco-friendly</span>
                        </div>

                        <!-- Product Details -->
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-2 text-gray-800"><?= htmlspecialchars($product['name']); ?></h2>
                            <p class="text-gray-600 mb-4 text-sm h-12 overflow-hidden"><?= htmlspecialchars($product['description']); ?></p>
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-2xl font-bold text-green-600">₱<?= number_format($product['price'], 2); ?></p>
                                <p class="text-sm text-gray-500">Stock: <?= $product['stock']; ?></p>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">Category: <?= htmlspecialchars($product['category']); ?></p>

                            <!-- Add to Cart Button -->
                            <button class="bg-blue-500 text-white w-full py-2 rounded-lg hover:bg-blue-600 transition duration-300 transform hover:scale-105">
                                Add to Cart
                            </button>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div id="modal-<?= $index; ?>" class="modal">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-3/4 max-w-lg">
                            <div class="relative">
                                <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-800" onclick="closeModal(<?= $index; ?>)">
                                    &times;
                                </button>
                            </div>
                            <img src="<?= htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/300x300.png?text=No+Image'); ?>"
                                 alt="<?= htmlspecialchars($product['name']); ?>" class="w-full h-64 object-cover rounded-lg mb-4">
                            <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($product['name']); ?></h2>
                            <p class="text-gray-600 mb-4"><?= htmlspecialchars($product['description']); ?></p>
                            <p class="text-lg font-semibold">Price: ₱<?= number_format($product['price'], 2); ?></p>
                            <p class="text-sm text-gray-500">Stock: <?= $product['stock']; ?></p>
                            <p class="text-sm text-gray-500">Category: <?= htmlspecialchars($product['category']); ?></p>
                            <button class="mt-4 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                                Add to Cart
                            </button>

                            <!-- Heart Icon for Favorites -->
                            <div class="mt-4 flex justify-center">
                                <span class="heart" id="heart-<?= $index; ?>" onclick="toggleFavorite(<?= $index; ?>)">
                                    <i class="fas fa-heart"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2023 Wastewise E-commerce. All rights reserved.</p>
            <p class="mt-2">Committed to a sustainable future through recycling and eco-friendly shopping.</p>
        </div>
    </footer>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        // Modal functionality
        function openModal(index) {
            document.getElementById('modal-' + index).classList.add('active');
        }

        function closeModal(index) {
            document.getElementById('modal-' + index).classList.remove('active');
        }

        // Favorite heart toggle functionality
        function toggleFavorite(index) {
            const heart = document.getElementById('heart-' + index);
            heart.classList.toggle('filled');
        }
    </script>
</body>
</html>
