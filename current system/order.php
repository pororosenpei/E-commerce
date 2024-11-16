<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="order.css">
    <title>WasteWise Dashboard</title>
    </head>
<body>
    <div class="dashboard">
        <nav class="top-nav">
            <a href="/" class="logo">
                <i data-lucide="leaf"></i>
                <span>WasteWise</span>
            </a>
            <div class="nav-links">
                <a href="userdb.php" class="nav-item">Home</a>
                <a href="products.php" class="nav-item">Products</a>
                <a href="order.php" class="nav-item active">Orders</a>
                
            </div>
            <div class="user-actions">
                <button class="icon-button"><i data-lucide="bell"></i></button>
                <a href="cart.html">
                    <button class="icon-button">
                      <i data-lucide="shopping-cart"></i>
                    </button>
                  </a>
                <button class="icon-button"><img src="/placeholder-user.jpg" alt="User avatar"></button>
            </div>
        </nav>

        <main class="main-content">
            <form class="search-form">
                <input type="search" placeholder="Search orders...">
                <button type="submit" class="search-button">
                    <i data-lucide="search"></i>
                </button>
            </form>

            <section class="orders-section">
                <div class="orders-header">
                    <h2>Orders Management</h2>
                    <div class="filter-sort">
                        <select id="status-filter">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                        </select>
                        <select id="sort-options">
                            <option value="date-desc">Date: Newest First</option>
                            <option value="date-asc">Date: Oldest First</option>
                            <option value="total-desc">Total: High to Low</option>
                            <option value="total-asc">Total: Low to High</option>
                        </select>
                    </div>
                </div>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#WW1001</td>

                            <td>2023-10-15</td>
                            <td>₱125.99</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-button view-button">View</button>
                                    <button class="action-button edit-button">Edit</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#WW1002</td>
                  
                            <td>2023-10-14</td>
                            <td>₱89.50</td>
                            <td><span class="status-badge status-processing">Processing</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-button view-button">View</button>
                                    <button class="action-button edit-button">Edit</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#WW1003</td>
                            
                            <td>2023-10-13</td>
                            <td>₱210.75</td>
                            <td><span class="status-badge status-shipped">Shipped</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-button view-button">View</button>
                                    <button class="action-button edit-button">Edit</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#WW1004</td>
                          
                            <td>2023-10-12</td>
                            <td>₱45.00</td>
                            <td><span class="status-badge status-delivered">Delivered</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-button view-button">View</button>
                                    <button class="action-button  edit-button">Edit</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#WW1005</td>
                           
                            <td>2023-10-11</td>
                            <td>₱175.25</td>
                            <td><span class="status-badge status-processing">Processing</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-button view-button">View</button>
                                    <button class="action-button edit-button">Edit</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Navigation item active state
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                navItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Simple filter and sort functionality (for demonstration purposes)
        const statusFilter = document.getElementById('status-filter');
        const sortOptions = document.getElementById('sort-options');
        const ordersTable = document.querySelector('.orders-table tbody');

        function filterAndSortOrders() {
    const status = statusFilter.value;
    const sortBy = sortOptions.value;
    
    const orders = Array.from(ordersTable.children);
    
    // Filter orders by status
    orders.forEach(order => {
        const orderStatus = order.querySelector('.status-badge').classList[1].replace('status-', '');
        order.style.display = status ? (orderStatus === status ? 'table-row' : 'none') : 'table-row';
    });

    // Sort orders by total or date based on selected option
    orders.sort((a, b) => {
        const dateA = new Date(a.children[1].textContent);
        const dateB = new Date(b.children[1].textContent);
        const totalA = parseFloat(a.children[2].textContent.replace('₱', ''));
        const totalB = parseFloat(b.children[2].textContent.replace('₱', ''));
        
        if (sortBy === 'date-desc') return dateB - dateA;
        if (sortBy === 'date-asc') return dateA - dateB;
        if (sortBy === 'total-desc') return totalB - totalA; // High to Low
        if (sortBy === 'total-asc') return totalA - totalB;   // Low to High
    });

    // Clear and re-append sorted rows
    ordersTable.innerHTML = '';
    orders.forEach(order => ordersTable.appendChild(order));
}

// Add event listeners
statusFilter.addEventListener('change', filterAndSortOrders);
sortOptions.addEventListener('change', filterAndSortOrders);
</script>
</body>
</html>