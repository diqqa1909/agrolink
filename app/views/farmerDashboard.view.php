<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
</head>

<body>
    <!-- Include Navbar Component -->
    <?php 
        $username = $_SESSION['USER']->name ?? 'Farmer';
        $role = $_SESSION['USER']->role ?? 'farmer';
        include '../app/views/components/dashboardNavBar.view.php'; 
    ?>

    <!-- Dashboard Layout -->
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3 class="sidebar-title">Farmer Dashboard</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#dashboard" class="menu-link active" data-section="dashboard">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        Dashboard
                    </a></li>
                <li><a href="#products" class="menu-link" data-section="products">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                        </div>
                        Products
                    </a></li>
                <li><a href="#orders" class="menu-link" data-section="orders">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                            </svg>
                        </div>
                        Orders
                    </a></li>
                <li><a href="#earnings" class="menu-link" data-section="earnings">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="8" />
                                <line x1="12" y1="8" x2="12" y2="16" />
                                <line x1="8" y1="12" x2="16" y2="12" />
                            </svg>
                        </div>
                        Earnings
                    </a></li>
                <li><a href="#deliveries" class="menu-link" data-section="deliveries">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h13v10H3z" />
                                <path d="M16 10h4l3 3v4h-7z" />
                                <circle cx="7.5" cy="17.5" r="1.5" />
                                <circle cx="18.5" cy="17.5" r="1.5" />
                            </svg>
                        </div>
                        Deliveries
                    </a></li>
                <li><a href="#profile" class="menu-link" data-section="profile">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        Profile
                    </a></li>
                <li><a href="#analytics" class="menu-link" data-section="analytics">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="20" x2="12" y2="10" />
                                <line x1="18" y1="20" x2="18" y2="4" />
                                <line x1="6" y1="20" x2="6" y2="14" />
                            </svg>
                        </div>
                        Analytics
                    </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Overview -->
            <div id="dashboard-section" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">Dashboard Overview</h1>
                    <p class="content-subtitle">Welcome back! Here's what's happening with your farm.</p>
                </div>

                <!-- Statistics Cards -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalProducts">0</div>
                        <div class="stat-label">Active Products</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="pendingOrders">0</div>
                        <div class="stat-label">Pending Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="monthlyEarnings">Rs. 0</div>
                        <div class="stat-label">This Month</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalEarnings">Rs. 0</div>
                        <div class="stat-label">Total Earnings</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"> Recent Orders</h3>
                    </div>
                    <div class="card-content" id="recentOrders">
                        <!-- Recent orders will be populated by JavaScript -->
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"> Top Products</h3>
                    </div>
                    <div class="card-content" id="topProducts">
                        <!-- Top products will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Products Management -->
            <div id="products-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title"> My Products</h1>
                    <button class="btn btn-primary" data-modal="addProductModal">➕ Add New Product</button>
                </div>
                <div class="content-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <!-- Products will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Orders Management -->
            <div id="orders-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title"> Order Management</h1>
                </div>

                <!-- Orders Table -->
                <div class="content-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Buyer</th>
                                    <th>Products</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                <!-- Orders will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Earnings -->
            <div id="earnings-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title"> Earnings Overview</h1>
                </div>

                <!-- Earnings Stats -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="todayEarnings">Rs. 0</div>
                        <div class="stat-label">Today</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="weekEarnings">Rs. 0</div>
                        <div class="stat-label">This Week</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="monthEarningsDetail">Rs. 0</div>
                        <div class="stat-label">This Month</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="yearEarnings">Rs. 0</div>
                        <div class="stat-label">This Year</div>
                    </div>
                </div>
            </div>

            <!-- Deliveries -->
            <div id="deliveries-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title"> Delivery Coordination</h1>
                </div>

                <!-- Delivery Stats -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="pendingDeliveries">0</div>
                        <div class="stat-label">Pending Deliveries</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="inTransitDeliveries">0</div>
                        <div class="stat-label">In Transit</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="completedDeliveries">0</div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="avgDeliveryTime">0</div>
                        <div class="stat-label">Avg. Delivery Days</div>
                    </div>
                </div>
            </div>

            <!-- Profile Management -->
            <div id="profile-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title"> Farmer Profile</h1>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title"> Personal Information</h3>
                    </div>
                    <div class="card-content">
                        <form id="personalInfoForm">
                            <div class="form-group">
                                <label for="profileName">Full Name</label>
                                <input type="text" id="profileName" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="profileEmail">Email</label>
                                <input type="email" id="profileEmail" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="profilePhone">Phone</label>
                                <input type="tel" id="profilePhone" name="phone" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Personal Info</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div id="analytics-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title"> Analytics & Insights</h1>
                </div>

                <!-- Analytics Cards -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalSales">0</div>
                        <div class="stat-label">Total Sales (kg)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="avgRating">0.0</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="repeatCustomers">0</div>
                        <div class="stat-label">Repeat Customers</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="conversionRate">0%</div>
                        <div class="stat-label">Order Conversion</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"> Add New Product</h3>
                <button class="close" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="productName">Product Name *</label>
                        <input type="text" id="productName" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Price (Rs.) *</label>
                        <input type="number" id="productPrice" name="price" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="productQuantity">Quantity *</label>
                        <input type="number" id="productQuantity" name="quantity" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="productLocation">Location</label>
                        <input type="text" id="productLocation" name="location" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Description</label>
                        <textarea id="productDescription" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="productImage">Image</label>
                        <input type="file" id="productImage" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Product</button>
                        <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Make ROOT available to JS -->
    <script>
        window.APP_ROOT = "<?= ROOT ?>";
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/farmerDashboard.js"></script>
    <script src="<?= ROOT ?>/assets/js/dashboardNavBar.js"></script>
</body>

</html>