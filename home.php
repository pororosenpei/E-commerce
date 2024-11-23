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
$products = getProducts($search, $category);
$wishlist = getWishlistItems();

$categories = ['Paper', 'Plastic', 'Metal', 'Glass', 'Electronics', 'Textiles'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wastewise E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="home.css">

</head>
<body class="bg-gray-100 font-sans">
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div>
            <a href="home.php" class="nav-item mb-4" onclick="showSection('home')">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="" class="nav-item mb-4" onclick="showSection('wishlist')">
                <i class="fas fa-heart"></i>
                <span>Wishlist</span>
            </a>
            <a href="cart.php" class="nav-item mb-4">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart</span>
            </a>
        </div>
        <div>
            <a href="#" class="nav-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content" id="content">
        <!-- Header -->
        <header class="header text-white py-6">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-center mb-2">Wastewise E-commerce</h1>
                <p class="text-lg text-center">Sustainable Shopping for a Better Tomorrow</p>
            </div>
        </header>

        <main class="container mx-auto px-4 py-12">
            <!-- Home Section -->
            <section id="home-section">
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

                                    <!-- Add to Cart Button and Heart Icon -->
                                    <div class="flex justify-between items-center">
                                        <button onclick="openCartModal(<?= htmlspecialchars(json_encode($product)); ?>)" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300 transform hover:scale-105">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                        <span class="heart <?= in_array($product['id'], $wishlist) ? 'filled' : '' ?>" onclick="toggleWishlist(<?= $product['id']; ?>, this)">
                                            <i class="fas fa-heart"></i>
                                        </span>
                                    </div>
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
                                    <div class="mt-4 flex justify-between items-center">
                                        <button onclick="openCartModal(<?= htmlspecialchars(json_encode($product)); ?>)" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                                        </button>
                                        <span class="heart <?= in_array($product['id'], $wishlist) ? 'filled' : '' ?>" onclick="toggleWishlist(<?= $product['id']; ?>, this)">
                                            <i class="fas fa-heart"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Wishlist Section -->
            <section id="wishlist-section" style="display: none;">
                <h2 class="text-2xl font-bold mb-4">Your Wishlist</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php
                    $wishlistProducts = array_filter($products, function($product) use ($wishlist) {
                        return in_array($product['id'], $wishlist);
                    });
                    if (empty($wishlistProducts)): ?>
                        <p class="col-span-full text-center text-gray-600">Your wishlist is empty.</p>
                    <?php else: ?>
                        <?php foreach ($wishlistProducts as $product): ?>
                            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md relative">
                                <div class="relative h-64">
                                    <img src="<?= htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/300x300.png?text=No+Image'); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-gray-600 mb-4">₱<?= number_format($product['price'], 2); ?></p>
                                    <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600" onclick="toggleWishlist(<?= $product['id']; ?>, this)">
                                        Remove from Wishlist
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <footer class="bg-gray-800 text-white py-8 mt-12">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; 2023 Wastewise E-commerce. All rights reserved.</p>
                <p class="mt-2">Committed to a sustainable future through recycling and eco-friendly shopping.</p>
            </div>
        </footer>
    </div>

    <!-- Cart Modal -->
    <div id="cart-modal" class="cart-modal">
        <div class="cart-modal-content fade-in">
            <h2 class="text-2xl font-bold mb-4">Add to Cart</h2>
            <div id="cart-product-details" class="mb-4"></div>
            <form id="add-to-cart-form" action="cart.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" id="cart-product-id">
                <label for="quantity" class="block mb-2">Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-4">
                <div class="flex justify-between">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Add to Cart</button>
                    <button type="button" onclick="closeCartModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="home.js"></script>
</body>
</html>
