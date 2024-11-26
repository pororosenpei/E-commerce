<?php
// home.php
session_start();
$db = new mysqli('localhost', 'root', '', 'wastewise');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Function to get all products
function getProducts($search = '', $category = '') {
    global $db;
    $query = "SELECT p.*, ec.name as event_category_name 
              FROM products p 
              LEFT JOIN event_categories ec ON p.event_category_id = ec.id 
              WHERE 1=1";
    if (!empty($search)) {
        $search = $db->real_escape_string($search);
        $query .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
    }
    if (!empty($category)) {
        $category = $db->real_escape_string($category);
        $query .= " AND p.category = '$category'";
    }
    $query .= " ORDER BY p.created_at DESC";
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get all event categories
function getEventCategories() {
    global $db;
    $query = "SELECT * FROM event_categories ORDER BY name ASC";
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get wishlist items
function getWishlistItems() {
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    return $_SESSION['wishlist'];
}

// Function to add item to wishlist
function addToWishlist($productId) {
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    if (!in_array($productId, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $productId;
    }
}

// Function to remove item from wishlist
function removeFromWishlist($productId) {
    if (isset($_SESSION['wishlist'])) {
        $key = array_search($productId, $_SESSION['wishlist']);
        if ($key !== false) {
            unset($_SESSION['wishlist'][$key]);
        }
    }
}

// Handle wishlist actions
if (isset($_POST['action']) && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    if ($_POST['action'] === 'add') {
        addToWishlist($productId);
    } elseif ($_POST['action'] === 'remove') {
        removeFromWishlist($productId);
    }
    exit; // Stop further execution for AJAX requests
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$all_products = getProducts($search, $category);

// Filter products for Christmas and regular sections
$christmas_products = array_filter($all_products, function($product) {
    return !is_null($product['event_category_id']) && strtolower($product['event_category_name']) === 'christmas';
});
$regular_products = array_filter($all_products, function($product) {
    return is_null($product['event_category_id']) || strtolower($product['event_category_name']) !== 'christmas';
});

$wishlist = getWishlistItems();

$categories = ['Paper', 'Plastic', 'Metal', 'Glass', 'Electronics', 'Textiles'];
$event_categories = getEventCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wastewise E-commerce - Christmas Special</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .christmas-theme {
            background: linear-gradient(to bottom, #1a472a, #2d724a);
        }
        .snow {
            position: absolute;
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            filter: blur(1px);
        }
        @keyframes snowfall {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Sidebar Toggle Button -->
    <button class="fixed top-4 left-4 z-50 bg-green-600 text-white p-2 rounded-full shadow-lg" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <nav class="fixed top-0 left-0 h-full w-64 bg-green-800 text-white p-5 transform -translate-x-full transition-transform duration-200 ease-in-out z-40" id="sidebar">
        <div class="flex flex-col h-full">
            <div class="flex-grow">
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded transition duration-200" onclick="showSection('home')">
                    <i class="fas fa-home mr-2"></i>
                    <span>Home</span>
                </a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded transition duration-200" onclick="showSection('wishlist')">
                    <i class="fas fa-heart mr-2"></i>
                    <span>Wishlist</span>
                </a>
                <a href="cart.php" class="block py-2 px-4 hover:bg-green-700 rounded transition duration-200">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    <span>Cart</span>
                </a>
            </div>
            <div>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded transition duration-200">
                    <i class="fas fa-user mr-2"></i>
                    <span>Profile</span>
                </a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded transition duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content" id="content">
        <!-- Header with Search -->
        <header class="bg-green-700 text-white py-6 sticky top-0 z-30">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <h1 class="text-3xl font-bold mb-4 md:mb-0">Wastewise E-commerce</h1>
                    <form action="" method="GET" class="flex items-center">
                        <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" class="px-4 py-2 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-800">
                        <select name="category" class="px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-800">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-r-lg hover:bg-green-500 transition duration-300">Search</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Christmas Banner -->
        <div class="christmas-theme text-white py-12 relative overflow-hidden">
            <div class="container mx-auto px-4 relative z-10">
                <h2 class="text-4xl md:text-6xl font-bold text-center mb-4">Christmas Special</h2>
                <p class="text-xl md:text-2xl text-center mb-8">Discover eco-friendly gifts for your loved ones!</p>
                <div class="text-center">
                    <a href="#christmas-products" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 inline-block">Shop Now</a>
                </div>
            </div>
            <?php for ($i = 0; $i < 50; $i++): ?>
                <div class="snow" style="left: <?= rand(0, 100); ?>vw; animation: snowfall <?= rand(5, 15); ?>s linear infinite;"></div>
            <?php endfor; ?>
        </div>

        <main class="container mx-auto px-4 py-12">
            <!-- Event Products Section -->
            <section id="christmas-products" class="mb-16">
                <h2 class="text-3xl font-bold mb-8 text-center text-green-800">Christmas Collection</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php if (empty($christmas_products)): ?>
                        <p class="col-span-full text-center text-gray-600">No Christmas products available at the moment.</p>
                    <?php else: ?>
                        <?php foreach ($christmas_products as $product): ?>
                            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300">
                                <img src="<?= htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/300x300.png?text=No+Image'); ?>" 
                                     alt="<?= htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-gray-600 mb-4"><?= htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-green-600">₱<?= number_format($product['price'], 2); ?></span>
                                        <button onclick="addToCart(<?= $product['id']; ?>)" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition duration-300">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Regular Products Section -->
            <section id="regular-products">
                <h2 class="text-3xl font-bold mb-8 text-center text-green-800">Our Products</h2>
                
                <!-- Product Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php if (empty($regular_products)): ?>
                        <p class="col-span-full text-center text-gray-600">No products found.</p>
                    <?php else: ?>
                        <?php foreach ($regular_products as $product): ?>
                            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300">
                                <img src="<?= htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/300x300.png?text=No+Image'); ?>" 
                                     alt="<?= htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-gray-600 mb-4"><?= htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-green-600">₱<?= number_format($product['price'], 2); ?></span>
                                        <button onclick="addToCart(<?= $product['id']; ?>)" class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition duration-300">
                                            Add to Cart
                                        </button>
                                    </div>
                                    <?php if (!is_null($product['event_category_id']) && strtolower($product['event_category_name']) !== 'christmas'): ?>
                                        <p class="mt-2 text-sm text-gray-500">Event: <?= htmlspecialchars($product['event_category_name']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Wishlist Section -->
            <section id="wishlist-section" class="hidden mt-16">
                <h2 class="text-3xl font-bold mb-8 text-center text-green-800">Your Wishlist</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php
                    $wishlistProducts = array_filter($all_products, function($product) use ($wishlist) {
                        return in_array($product['id'], $wishlist);
                    });
                    if (empty($wishlistProducts)): ?>
                        <p class="col-span-full text-center text-gray-600">Your wishlist is empty.</p>
                    <?php else: ?>
                        <?php foreach ($wishlistProducts as $product): ?>
                            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300">
                                <img src="<?= htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/300x300.png?text=No+Image'); ?>" 
                                     alt="<?= htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-gray-600 mb-4">₱<?= number_format($product['price'], 2); ?></p>
                                    <button class="w-full bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition duration-300" 
                                            onclick="removeFromWishlist(<?= $product['id']; ?>)">
                                        Remove from Wishlist
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <footer class="bg-green-800 text-white py-8">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; 2023 Wastewise E-commerce. All rights reserved.</p>
                <p class="mt-2">Committed to a sustainable future through recycling and eco-friendly shopping.</p>
            </div>
        </footer>
    </div>

    <!-- Quantity Modal -->
    <div id="quantity-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <button id="close-modal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Select Quantity</h3>
                <div class="mt-2 px-7 py-3">
                    <input type="number" id="quantity-input" min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="items-center px-4 py-3">
                    <button id="add-to-cart-btn" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <button id="close-success-modal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Successfully Added to Cart</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Your item has been added to the cart.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <a href="cart.php" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 inline-block">
                        View Cart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        function showSection(sectionId) {
            document.getElementById('regular-products').style.display = sectionId === 'home' ? 'block' : 'none';
            document.getElementById('wishlist-section').style.display = sectionId === 'wishlist' ? 'block' : 'none';
            document.getElementById('christmas-products').style.display = sectionId === 'home' ? 'block' : 'none';
        }

        let currentProductId = null;

        function addToCart(productId) {
            currentProductId = productId;
            document.getElementById('quantity-modal').classList.remove('hidden');
        }

        document.getElementById('add-to-cart-btn').addEventListener('click', function() {
            const quantity = document.getElementById('quantity-input').value;
            
            // AJAX request to add item to cart
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${currentProductId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('quantity-modal').classList.add('hidden');
                    document.getElementById('success-modal').classList.remove('hidden');
                } else {
                    alert('Failed to add item to cart. Please try again.');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('close-modal').addEventListener('click', function() {
            document.getElementById('quantity-modal').classList.add('hidden');
        });

        document.getElementById('close-success-modal').addEventListener('click', function() {
            document.getElementById('success-modal').classList.add('hidden');
        });

        function removeFromWishlist(productId) {
            fetch('home.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&product_id=${productId}`
            })
            .then(response => response.text())
            .then(() => {
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>

