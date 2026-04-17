<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/components.css">
</head>

<body>
    <?php
    $isHomePage = false;
    include '../app/views/shared/topnavbar.view.php';
    ?>

    <!-- Dashboard Layout -->
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3 class="sidebar-title">Admin Dashboard</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#dashboard" class="menu-link active" data-section="dashboard">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <rect x="3" y="14" width="7" height="7" />
                            </svg>
                        </div>
                        Dashboard
                    </a></li>
                <li><a href="#users" class="menu-link" data-section="users">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        Users
                    </a></li>
                <li><a href="#verifications" class="menu-link" data-section="verifications">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4" />
                                <path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z" />
                            </svg>
                        </div>
                        Verifications
                        <span id="pending-badge"
                            style="background:#e74c3c;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:auto;display:none;">0</span>
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
                <li><a href="#products" class="menu-link" data-section="products">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                        </div>
                        Products
                    </a></li>
                <li><a href="#payments" class="menu-link" data-section="payments">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2" ry="2" />
                                <line x1="2" y1="10" x2="22" y2="10" />
                            </svg>
                        </div>
                        Payments
                    </a></li>
                <li><a href="#disputes" class="menu-link" data-section="disputes">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3v18" />
                                <path d="M5 12h14" />
                            </svg>
                        </div>
                        Disputes
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
                <li><a href="#notifications" class="menu-link" data-section="notifications">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                            </svg>
                        </div>
                        Notifications
                    </a></li>
                <li><a href="#settings" class="menu-link" data-section="settings">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3" />
                                <path
                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.26 1.3.73 1.77.47.47 1.11.73 1.77.73H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                            </svg>
                        </div>
                        Settings
                    </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Overview -->
            <div id="dashboard-section" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">System Overview</h1>
                    <p class="content-subtitle">Welcome back! Monitor your platform's performance and activities.</p>
                </div>

                <!-- Key Metrics -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalUsers"><?= ($farmers + $buyers + $transporters + $admins) ?>
                        </div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="activeOrders">0</div>
                        <div class="stat-label">Active Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalRevenue">Rs. 0</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>

                <!-- User Summary Card -->
                <div class="content-card" style="margin-top: var(--spacing-xl);">
                    <div class="card-header">
                        <h3 class="card-title">User Summary</h3>
                    </div>
                    <div class="card-content">
                        <div
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; padding: 20px;">
                            <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                                <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;"
                                    id="farmerCount"><?php show($farmers); ?></div>
                                <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Farmers</div>
                            </div>
                            <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                                <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;"
                                    id="buyerCount"><?php show($buyers); ?></div>
                                <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Buyers</div>
                            </div>
                            <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                                <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;"
                                    id="transporterCount"><?php show($transporters); ?></div>
                                <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Transporters</div>
                            </div>
                            <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                                <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;"
                                    id="adminCount"><?php show($admins); ?></div>
                                <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Admins</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-top: var(--spacing-xl);">
                    <div class="elegant-card">
                        <div class="card-header-elegant">
                            <div class="card-header-content">
                                <h3 class="card-title">Recent Orders</h3>
                                <p class="card-subtitle">Latest customer purchases</p>
                            </div>
                        </div>
                        <div class="card-body-elegant" id="recentOrders"></div>
                        <div class="card-footer-elegant">
                            <a href="#" class="card-link">View all orders →</a>
                        </div>
                    </div>

                    <div class="elegant-card">
                        <div class="card-header-elegant">
                            <div class="card-header-content">
                                <h3 class="card-title">New User Registrations</h3>
                                <p class="card-subtitle">Recently joined users</p>
                            </div>
                        </div>
                        <div class="card-body-elegant" id="newRegistrations"></div>
                        <div class="card-footer-elegant">
                            <a href="#" class="card-link">View all users →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management -->
            <div id="users-section" class="content-section" style="display: none;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h1>User Management</h1>
                    <div style="display: flex; gap: 15px;">
                        <!-- <button class="btn btn-secondary" onclick="exportUsers()">Export Users</button> -->
                        <button class="btn btn-primary" onclick="openAddUserModal()">➕ Add User</button>
                    </div>
                </div>

                <div class="filters">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="userSearch">Search Users</label>
                            <input type="text" id="userSearch" class="form-control"
                                placeholder="Search by name, email, or ID...">
                        </div>
                        <div class="filter-group">
                            <label for="roleFilter">Role</label>
                            <select id="roleFilter" class="form-control">
                                <option value="">All Roles</option>
                                <option value="farmer">Farmers</option>
                                <option value="buyer">Buyers</option>
                                <option value="transporter">Transporters</option>
                                <option value="admin">Admins</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th data-sort="id">User ID</th>
                                <th data-sort="name">Name</th>
                                <th data-sort="email">Email</th>
                                <th data-sort="role">Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <div class="message" id="message"></div>
                        <tbody id="usersTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Order Management -->
            <div id="orders-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Order Management</h1>
                </div>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="pendingOrdersCount">0</div>
                        <div class="stat-label">Pending Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="processingOrdersCount">0</div>
                        <div class="stat-label">Processing</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="completedOrdersCount">0</div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="averageOrderValue">Rs. 0</div>
                        <div class="stat-label">Avg Order Value</div>
                    </div>
                </div>

                <div class="filters" style="margin-top: var(--spacing-xl);">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="orderSearch">Search Orders</label>
                            <input type="text" id="orderSearch" class="form-control"
                                placeholder="Search by order ID, buyer, or farmer...">
                        </div>
                        <div class="filter-group">
                            <label for="orderStatusFilter">Status</label>
                            <select id="orderStatusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="orderDateFilter">Date Range</label>
                            <select id="orderDateFilter" class="form-control">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="paymentStatusFilter">Payment</label>
                            <select id="paymentStatusFilter" class="form-control">
                                <option value="">All Payments</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th data-sort="id">Order ID</th>
                                <th data-sort="buyer">Buyer</th>
                                <th data-sort="farmer">Farmer</th>
                                <th data-sort="total">Total</th>
                                <th data-sort="status">Status</th>
                                <th data-sort="payment">Payment</th>
                                <th data-sort="date">Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Product Management -->
            <!-- Product Management -->
            <div id="products-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Product Management</h1>
                    <p class="content-subtitle">Overview of all products listed on the platform</p>
                </div>

                <!-- Product Statistics -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalProducts">0</div>
                        <div class="stat-label">Total Products</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="activeProducts">0</div>
                        <div class="stat-label">Active Products</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="outOfStockProducts">0</div>
                        <div class="stat-label">Out of Stock</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="pendingApprovalProducts">0</div>
                        <div class="stat-label">Pending Approval</div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-container" style="margin-top: var(--spacing-xl);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Image</th>
                                <th>Product Name</th>
                                <th>Farmer</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <tr>
                                <td colspan="8" style="text-align:center;padding:2rem;">Loading products...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments & Finance -->
            <!-- Payments & Finance -->
            <div id="payments-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Payments & Finance</h1>
                    <p class="content-subtitle">Track and manage all platform payments</p>
                </div>

                <!-- Payment Statistics -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalRevenue">Rs. 0</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalTransactions">0</div>
                        <div class="stat-label">Total Transactions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="platformCommission">Rs. 0</div>
                        <div class="stat-label">Platform Commission</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="avgPaymentAmount">Rs. 0</div>
                        <div class="stat-label">Avg Payment Amount</div>
                    </div>
                </div>

                <!-- Payment Status Summary -->
                <div class="stats-container" style="margin-top: var(--spacing-xl); width: 100%;">
                    <div class="stats-grid-4">
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">✅</div>
                                <h4>Completed</h4>
                                <div class="stat-number" id="completedPayments">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">⏳</div>
                                <h4>Pending</h4>
                                <div class="stat-number" id="pendingPayments">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">❌</div>
                                <h4>Failed</h4>
                                <div class="stat-number" id="failedPayments">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">↩️</div>
                                <h4>Refunded</h4>
                                <div class="stat-number" id="refundedPayments">0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="stats-container" style="margin-top: var(--spacing-xl); width: 100%;">
                    <div class="stats-grid-4">
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">💵</div>
                                <h4>Cash on Delivery</h4>
                                <div class="stat-number" id="codRevenue">Rs. 0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">🏦</div>
                                <h4>Bank Transfer</h4>
                                <div class="stat-number" id="bankRevenue">Rs. 0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">💳</div>
                                <h4>Card Payment</h4>
                                <div class="stat-number" id="cardRevenue">Rs. 0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">📱</div>
                                <h4>Mobile Payment</h4>
                                <div class="stat-number" id="mobileRevenue">Rs. 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Filters -->
                <div class="filters" style="margin-top: var(--spacing-xl);">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="paymentSearch">Search Payments</label>
                            <input type="text" id="paymentSearch" class="form-control"
                                placeholder="Search by order ID, buyer, or transaction ID...">
                        </div>
                        <div class="filter-group">
                            <label for="paymentStatusFilter">Status</label>
                            <select id="paymentStatusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="paymentMethodFilter">Payment Method</label>
                            <select id="paymentMethodFilter" class="form-control">
                                <option value="">All Methods</option>
                                <option value="cash_on_delivery">Cash on Delivery</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="card">Card Payment</option>
                                <option value="mobile_payment">Mobile Payment</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="paymentDateFilter">Date Range</label>
                            <select id="paymentDateFilter" class="form-control">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="filters-row" style="margin-top: 10px;">
                        <div class="filter-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-secondary" onclick="resetPaymentFilters()">Reset Filters</button>
                        </div>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="content-card" style="margin-top: var(--spacing-xl);">
                    <div class="card-header">
                        <h3 class="card-title">Payment Transactions</h3>
                        <button class="btn btn-secondary" onclick="exportPayments()">Export CSV</button>
                    </div>
                    <div style="padding: var(--spacing-lg);">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Order ID</th>
                                        <th>Buyer</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
                                    <tr>
                                        <td colspan="8" style="text-align:center;padding:2rem;">Loading payments...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details Modal -->
            <div id="paymentDetailsModal" class="modal" style="display:none;">
                <div class="modal-content" style="max-width:600px;width:95%;">
                    <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;">
                        <h3>Payment Details</h3>
                        <button onclick="closeModal('paymentDetailsModal')"
                            style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
                    </div>
                    <div class="modal-body" id="paymentDetailsBody">
                        <!-- Payment details will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Disputes -->
            <!-- Disputes -->
            <div id="disputes-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Disputes Management</h1>
                    <p class="content-subtitle">Manage and resolve customer disputes</p>
                </div>

                <!-- Dispute Statistics -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalDisputes">0</div>
                        <div class="stat-label">Total Disputes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="openDisputes">0</div>
                        <div class="stat-label">Open Disputes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="resolvedDisputes">0</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="averageResolutionTime">0</div>
                        <div class="stat-label">Avg Resolution (hours)</div>
                    </div>
                </div>

                <!-- Dispute Priority Summary -->
                <div class="stats-container" style="margin-top: var(--spacing-xl); width: 100%;">
                    <div class="stats-grid-3">
                        <div class="stat-card card text-center" style="border-left: 4px solid #e74c3c;">
                            <div class="stat-content">
                                <div class="stat-icon">🔴</div>
                                <h4>High Priority</h4>
                                <div class="stat-number" id="highPriorityDisputes">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center" style="border-left: 4px solid #f39c12;">
                            <div class="stat-content">
                                <div class="stat-icon">🟡</div>
                                <h4>Medium Priority</h4>
                                <div class="stat-number" id="mediumPriorityDisputes">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center" style="border-left: 4px solid #3498db;">
                            <div class="stat-content">
                                <div class="stat-icon">🔵</div>
                                <h4>Low Priority</h4>
                                <div class="stat-number" id="lowPriorityDisputes">0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dispute Categories -->
                <div class="stats-container" style="margin-top: var(--spacing-xl); width: 100%;">
                    <div class="stats-grid-4">
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">📦</div>
                                <h4>Order Issues</h4>
                                <div class="stat-number" id="orderIssues">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">💰</div>
                                <h4>Payment Issues</h4>
                                <div class="stat-number" id="paymentIssues">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">🚚</div>
                                <h4>Delivery Issues</h4>
                                <div class="stat-number" id="deliveryIssues">0</div>
                            </div>
                        </div>
                        <div class="stat-card card text-center">
                            <div class="stat-content">
                                <div class="stat-icon">⭐</div>
                                <h4>Quality Issues</h4>
                                <div class="stat-number" id="qualityIssues">0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dispute Filters -->
                <div class="filters" style="margin-top: var(--spacing-xl);">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="disputeSearch">Search Disputes</label>
                            <input type="text" id="disputeSearch" class="form-control"
                                placeholder="Search by order ID, complainant, or respondent...">
                        </div>
                        <div class="filter-group">
                            <label for="disputeStatusFilter">Status</label>
                            <select id="disputeStatusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="disputeTypeFilter">Type</label>
                            <select id="disputeTypeFilter" class="form-control">
                                <option value="">All Types</option>
                                <option value="order_issue">Order Issue</option>
                                <option value="payment_issue">Payment Issue</option>
                                <option value="delivery_issue">Delivery Issue</option>
                                <option value="product_quality">Product Quality</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="disputePriorityFilter">Priority</label>
                            <select id="disputePriorityFilter" class="form-control">
                                <option value="">All Priorities</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div class="filters-row" style="margin-top: 10px;">
                        <div class="filter-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-secondary" onclick="resetDisputeFilters()">Reset Filters</button>
                        </div>
                    </div>
                </div>

                <!-- Disputes Table -->
                <div class="table-container" style="margin-top: var(--spacing-xl);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Dispute ID</th>
                                <th>Order ID</th>
                                <th>Complainant</th>
                                <th>Respondent</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="disputesTableBody">
                            <tr>
                                <td colspan="9" style="text-align:center;padding:2rem;">Loading disputes...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dispute Details Modal -->
            <div id="disputeDetailsModal" class="modal" style="display:none;">
                <div class="modal-content" style="max-width:800px;width:95%;max-height:80vh;overflow-y:auto;">
                    <div class="modal-header"
                        style="display:flex;justify-content:space-between;align-items:center;padding:15px;border-bottom:1px solid #eee;">
                        <h3>Dispute Details</h3>
                        <button onclick="closeModal('disputeDetailsModal')"
                            style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
                    </div>
                    <div class="modal-body" id="disputeDetailsBody" style="padding:20px;">
                        <!-- Dispute details will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <!-- Analytics -->
            <div id="analytics-section" class="content-section" style="display: none;">
                <div class="analytics-header">
                    <h1>Platform Analytics</h1>
                    <div class="period-selector">
                        <select id="analyticsPeriod" class="form-control" style="width: auto; display: inline-block;">
                            <option value="week">Last 7 Days</option>
                            <option value="month" selected>Last 30 Days</option>
                            <option value="year">Last 12 Months</option>
                        </select>
                        <button class="btn btn-primary" onclick="refreshAnalytics()">Refresh</button>
                    </div>
                </div>

                <!-- Key Performance Indicators -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalRevenue">Rs. 0</div>
                        <div class="stat-label">Total Revenue</div>
                        <div id="revenueGrowth" class="stat-trend">+0%</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalOrders">0</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalUsers">0</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="avgOrderValue">Rs. 0</div>
                        <div class="stat-label">Avg Order Value</div>
                    </div>
                </div>

                <!-- Analytics Grid -->
                <div class="analytics-grid">
                    <!-- Revenue Chart -->
                    <div class="analytics-card">
                        <div class="card-header">
                            <div class="card-title">
                                <div>
                                    <h3>Revenue Trends</h3>
                                    <p>Monthly revenue tracking</p>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart" style="max-height: 300px; width: 100%;"></canvas>
                        </div>
                    </div>

                    <!-- User Growth Chart -->
                    <div class="analytics-card">
                        <div class="card-header">
                            <div class="card-title">
                                <div>
                                    <h3>User Growth</h3>
                                    <p>New user registrations over time</p>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="userGrowthChart" style="max-height: 300px; width: 100%;"></canvas>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="analytics-card">
                        <div class="card-header">
                            <div class="card-title">
                                <div>
                                    <h3>Category Distribution</h3>
                                    <p>Products by category</p>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="categoryChart" style="max-height: 300px; width: 100%;"></canvas>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="analytics-card">
                        <div class="card-header">
                            <div class="card-title">
                                <div>
                                    <h3>Top Selling Products</h3>
                                    <p>Best performing products</p>
                                </div>
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody id="topProductsBody">
                                    <tr>
                                        <td colspan="3" style="text-align:center;">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div id="notifications-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">System Notifications</h1>
                    <button class="btn btn-primary" data-modal="sendNotificationModal">Send Notification</button>
                </div>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalNotifications">0</div>
                        <div class="stat-label">Total Sent</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="deliveredNotifications">0</div>
                        <div class="stat-label">Delivered</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="openRate">0%</div>
                        <div class="stat-label">Open Rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="clickRate">0%</div>
                        <div class="stat-label">Click Rate</div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div id="settings-section" class="content-section" style="display: none;">
                <div class="settings-header">
                    <h1>System Settings</h1>
                    <p>Manage platform configuration and system preferences</p>
                </div>
            </div>

            <!-- Verification Management -->
            <div id="verifications-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Account Verifications</h1>
                    <p class="content-subtitle">Review submitted documents for farmers and transporters.</p>
                </div>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="vPendingCount">0</div>
                        <div class="stat-label">Pending Review</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="vApprovedCount">0</div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="vRejectedCount">0</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                </div>

                <div style="display:flex;gap:8px;margin:1.5rem 0 1rem;">
                    <button class="btn btn-primary v-tab-btn active" data-filter="pending"
                        onclick="setVerificationFilter('pending')">Pending</button>
                    <button class="btn btn-secondary v-tab-btn" data-filter="approved"
                        onclick="setVerificationFilter('approved')">Approved</button>
                    <button class="btn btn-secondary v-tab-btn" data-filter="rejected"
                        onclick="setVerificationFilter('rejected')">Rejected</button>
                    <button class="btn btn-secondary v-tab-btn" data-filter="all"
                        onclick="setVerificationFilter('all')">All</button>
                </div>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Documents</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="verificationsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Document Review Modal -->
            <div id="docReviewModal" class="modal" style="display:none;">
                <div class="modal-content" style="max-width:720px;width:95%;">
                    <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;">
                        <h3 id="docModalTitle">Review Documents</h3>
                        <button onclick="closeDocModal()"
                            style="background:none;border:none;font-size:20px;cursor:pointer;color:#666;">✕</button>
                    </div>
                    <div class="modal-body" id="docModalBody"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New User</h3>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="message" id="addUserMessage"></div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="userName">Full Name *</label>
                            <input type="text" id="userName" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="userEmail">Email *</label>
                            <input type="email" id="userEmail" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="userRole">Role *</label>
                            <select id="userRole" name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="farmer">Farmer</option>
                                <option value="buyer">Buyer</option>
                                <option value="transporter">Transporter</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="userPass">Password *</label>
                            <input type="password" id="userPass" name="password" class="form-control" required
                                minlength="8">
                            <small class="form-text">Minimum 8 characters</small>
                        </div>
                        <div class="form-group">
                            <label for="userConfirmPass">Confirm Password *</label>
                            <input type="password" id="userConfirmPass" name="confirmPassword" class="form-control"
                                required minlength="8">
                            <small class="form-text">Re-enter password to confirm</small>
                        </div>
                    </div>
                    <div id="addUserFormErrors"
                        style="display: none; color: red; margin-bottom: 15px; padding: 10px; background: #ffe6e6; border-radius: 4px;">
                    </div>
                    <div style="display: flex; gap: 15px; margin-top: var(--spacing-lg);">
                        <button type="submit" class="btn btn-primary">Add User</button>
                        <button type="button" class="btn btn-secondary" onclick="closeAddUserModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update User Modal -->
    <div id="updateUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update User</h3>
            </div>
            <div class="modal-body">
                <form id="updateUserForm">
                    <div class="message" id="updateUserMessage"></div>
                    <input type="hidden" id="updateUserId" name="id">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="updateName">Full Name *</label>
                            <input type="text" id="updateName" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="updateEmail">Email *</label>
                            <input type="email" id="updateEmail" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="updateRole">Role *</label>
                            <select id="updateRole" name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="farmer">Farmer</option>
                                <option value="buyer">Buyer</option>
                                <option value="transporter">Transporter</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="updatePass">New Password</label>
                            <input type="password" id="updatePass" name="password" class="form-control" minlength="8">
                            <small class="form-text">Leave empty to keep current password</small>
                        </div>
                    </div>
                    <div id="updateUserFormErrors"
                        style="display: none; color: red; margin-bottom: 15px; padding: 10px; background: #ffe6e6; border-radius: 4px;">
                    </div>
                    <div style="display: flex; gap: 15px; margin-top: var(--spacing-lg);">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <button type="button" class="btn btn-secondary" onclick="closeUpdateUserModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Send Notification Modal -->
    <div id="sendNotificationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Send Notification</h3>
            </div>
            <div class="modal-body">
                <form id="sendNotificationForm">
                    <div class="form-group">
                        <label for="notificationTitle">Title *</label>
                        <input type="text" id="notificationTitle" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="notificationMessage">Message *</label>
                        <textarea id="notificationMessage" name="message" class="form-control" rows="4"
                            required></textarea>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="notificationRecipient">Recipient *</label>
                            <select id="notificationRecipient" name="recipient" class="form-control" required>
                                <option value="">Select Recipients</option>
                                <option value="all">All Users</option>
                                <option value="farmers">All Farmers</option>
                                <option value="buyers">All Buyers</option>
                                <option value="transporters">All Transporters</option>
                                <option value="admins">All Admins</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notificationType">Type *</label>
                            <select id="notificationType" name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="system">System</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="promotion">Promotion</option>
                                <option value="alert">Alert</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; gap: var(--spacing-md); margin-top: var(--spacing-lg);">
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                        <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script>
        let currentReviewUserId = null;
        let _currentModalUserId = null;

        // Initialize admin dashboard
        function initAdminDashboard() {
            loadDashboardData();
            loadUsers();
            loadVerifications();
            loadOrders();
            loadProducts();
            loadAnalytics();
            loadPayments();
            loadDisputes();
            setupOrderFilters();
            setupProductFilters();
            setupPaymentFilters();
            setupDisputeFilters(); 
            setupNavigation();
            setupForms();
            showSection('dashboard');
            if (typeof loadAnalytics === 'function') loadAnalytics();
        }

        // Navigation setup
        function setupNavigation() {
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');
                    showSection(section);
                    menuLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        // Show specific section
        function showSection(sectionName) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === sectionName) {
                    link.classList.add('active');
                }
            });
            if (sectionName === 'analytics') {
                loadAnalytics();
            }
        }

        // Load dashboard data
        function loadDashboardData() {
            document.getElementById('recentOrders').innerHTML = `
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-007</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Buyer → Ranjith Farmer - Rs. 2,450</div>
                    <span class="badge badge">Completed</span>
                </div>
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-008</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Green Valley Restaurant → Multiple Farmers - Rs. 8,900</div>
                    <span class="badge badge">Processing</span>
                </div>
            `;
            document.getElementById('newRegistrations').innerHTML = `
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">Saman Perera</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Farmer - Kandy</div>
                    <span class="badge badge">Pending Approval</span>
                </div>
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">Fresh Mart Ltd</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Buyer - Colombo</div>
                    <span class="badge badge">Approved</span>
                </div>
            `;
        }

        // Load users data
        function loadUsers() {
            const tbody = document.getElementById('usersTableBody');
            let users = <?= json_encode($users) ?>;
            const signedInUser = '<?= $role ?>';

            if (signedInUser === 'admin') {
                users = users.filter(user => user.role !== 'admin' && user.role !== 'superadmin');
            } else if (signedInUser === 'superadmin') {
                users = users.filter(user => user.role !== 'superadmin');
            }

            // Filter by verification status
            users = users.filter(user => {
                return user.verification_status === 'approved' ||
                    user.verification_status === 'not_required';
            });

            // Store all users for filtering
            allUsers = users;

            // Display all users initially
            displayUsers(users);
        }

        function displayUsers(users) {
            const tbody = document.getElementById('usersTableBody');

            if (!users || users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:2rem;color:#aaa;">No users found.</td></tr>';
                return;
            }

            let html = '';
            users.forEach(user => {
                // Get role badge class
                let roleBadgeClass = '';
                switch (user.role) {
                    case 'farmer': roleBadgeClass = 'success'; break;
                    case 'buyer': roleBadgeClass = 'info'; break;
                    case 'transporter': roleBadgeClass = 'warning'; break;
                    default: roleBadgeClass = 'danger';
                }

                // Get verification status badge
                let verificationBadge = '';
                if (user.verification_status === 'approved') {
                    verificationBadge = '<span class="badge badge-success" style="margin-left: 8px;">✓ Approved</span>';
                } else if (user.verification_status === 'not_required') {
                    verificationBadge = '<span class="badge badge-info" style="margin-left: 8px;">Not Required</span>';
                } else if (user.verification_status === 'pending') {
                    verificationBadge = '<span class="badge badge-warning" style="margin-left: 8px;">⏳ Pending</span>';
                } else if (user.verification_status === 'rejected') {
                    verificationBadge = '<span class="badge badge-danger" style="margin-left: 8px;">❌ Rejected</span>';
                }

                html += `
            <tr>
                <td>${user.id || 'N/A'}</td>
                <td>${user.name || 'N/A'}</td>
                <td>${user.email || 'N/A'}</td>
                <td><span class="badge badge-${roleBadgeClass}">${user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'User'}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="openUpdateUserModal('${user.id}')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser('${user.id}', '${user.role}')">Delete</button>
                </td>
            </tr>
        `;
            });
            tbody.innerHTML = html;
        }

        function filterUsers() {
            const searchTerm = document.getElementById('userSearch').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;

            let filteredUsers = [...allUsers];

            // Apply role filter
            if (roleFilter !== '') {
                filteredUsers = filteredUsers.filter(user => user.role === roleFilter);
            }

            // Apply search filter (search by name, email, or ID)
            if (searchTerm !== '') {
                filteredUsers = filteredUsers.filter(user => {
                    return (user.name && user.name.toLowerCase().includes(searchTerm)) ||
                        (user.email && user.email.toLowerCase().includes(searchTerm)) ||
                        (user.id && user.id.toString().includes(searchTerm));
                });
            }

            // Display filtered users
            displayUsers(filteredUsers);

            // Show message if no results
            if (filteredUsers.length === 0) {
                const tbody = document.getElementById('usersTableBody');
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:2rem;color:#aaa;">No users match your search criteria.</td></tr>';
            }
        }

        function debounce(func, delay) {
            let timeoutId;
            return function (...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function setupLiveSearch() {
            const searchInput = document.getElementById('userSearch');
            const roleSelect = document.getElementById('roleFilter');

            if (searchInput) {
                // Use debounce to avoid filtering on every keystroke
                const debouncedFilter = debounce(filterUsers, 300);
                searchInput.addEventListener('input', debouncedFilter);
            }

            if (roleSelect) {
                roleSelect.addEventListener('change', filterUsers);
            }
        }

        // Add clear filters button functionality
        /* function addClearFiltersButton() {
            const filtersDiv = document.querySelector('#users-section .filters');
            if (filtersDiv && !document.getElementById('clearFiltersBtn')) {
                const clearBtn = document.createElement('button');
                clearBtn.id = 'clearFiltersBtn';
                clearBtn.className = 'btn btn-secondary';
                clearBtn.textContent = 'Clear Filters';
                clearBtn.style.marginLeft = '10px';
                clearBtn.onclick = function() {
                    document.getElementById('userSearch').value = '';
                    document.getElementById('roleFilter').value = '';
                    filterUsers();
                };
                
                const filterRow = filtersDiv.querySelector('.filters-row');
                if (filterRow) {
                    const buttonDiv = document.createElement('div');
                    buttonDiv.className = 'filter-group';
                    buttonDiv.appendChild(clearBtn);
                    filterRow.appendChild(buttonDiv);
                }
            }
        } */

        // Enhanced initialization
        document.addEventListener('DOMContentLoaded', function () {
            initAdminDashboard();
            updateUserCount();
            setInterval(updateUserCount, 30000);

            // Setup live search after users are loaded
            setTimeout(() => {
                setupLiveSearch();
                addClearFiltersButton();
            }, 500);
        });

        // Load orders data
        async function loadOrders() {
            const tbody = document.getElementById('ordersTableBody');

            // Show loading state
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;">Loading orders...</td></tr>';

            try {
                // Get filter values
                const status = document.getElementById('orderStatusFilter')?.value || '';
                const paymentStatus = document.getElementById('paymentStatusFilter')?.value || '';
                const dateRange = document.getElementById('orderDateFilter')?.value || '';
                const search = document.getElementById('orderSearch')?.value || '';

                const response = await fetch('<?= ROOT ?>/adminDashboard/getOrders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        status: status,
                        payment_status: paymentStatus,
                        date_range: dateRange,
                        search: search
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:2rem;color:red;">Error: ${result.message}</td></tr>`;
                    return;
                }

                // Update statistics
                if (result.stats) {
                    document.getElementById('pendingOrdersCount').textContent = result.stats.pending || 0;
                    document.getElementById('processingOrdersCount').textContent = result.stats.processing || 0;
                    document.getElementById('completedOrdersCount').textContent = result.stats.completed || 0;
                    document.getElementById('averageOrderValue').textContent = `Rs. ${result.stats.avg_order_value || 0}`;
                }

                allOrders = result.data;
                displayOrders(allOrders);

            } catch (error) {
                console.error('Error loading orders:', error);
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;color:red;">Failed to load orders. Please try again.</td></tr>';
            }
        }

        function displayOrders(orders) {
            const tbody = document.getElementById('ordersTableBody');

            if (!orders || orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;color:#aaa;">No orders found.</td></tr>';
                return;
            }

            let html = '';
            orders.forEach(order => {
                // Status badge class
                let statusClass = '';
                switch (order.order_status) {
                    case 'pending': statusClass = 'badge-warning'; break;
                    case 'processing': statusClass = 'badge-info'; break;
                    case 'shipped': statusClass = 'badge-primary'; break;
                    case 'delivered': statusClass = 'badge-success'; break;
                    case 'completed': statusClass = 'badge-success'; break;
                    case 'cancelled': statusClass = 'badge-danger'; break;
                    default: statusClass = 'badge-secondary';
                }

                // Payment status badge
                let paymentClass = '';
                switch (order.payment_status) {
                    case 'paid': paymentClass = 'badge-success'; break;
                    case 'pending': paymentClass = 'badge-warning'; break;
                    case 'failed': paymentClass = 'badge-danger'; break;
                    case 'refunded': paymentClass = 'badge-info'; break;
                    default: paymentClass = 'badge-secondary';
                }

                html += `
            <tr>
                <td><strong>#${order.order_number || order.order_id}</strong></td>
                <td>${escapeHtml(order.buyer_name || 'N/A')}</td>
                <td>${escapeHtml(order.farmer_name || 'Multiple')}</td>
                <td><strong>Rs. ${parseFloat(order.total_amount).toLocaleString()}</strong></td>
                <td><span class="badge ${statusClass}">${order.order_status ? order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1) : 'N/A'}</span></td>
                <td><span class="badge ${paymentClass}">${order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : 'N/A'}</span></td>
                <td>${formatDate(order.order_date)}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewOrderDetails(${order.order_id})">View</button>
                    <button class="btn btn-sm btn-secondary" onclick="updateOrderStatus(${order.order_id})">Update Status</button>
                </td>
            </tr>
        `;
            });
            tbody.innerHTML = html;
        }

        // Format date for display
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        async function viewOrderDetails(orderId) {
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/getOrderDetails', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order_id: orderId })
                });

                const result = await response.json();

                if (!result.success) {
                    alert('Failed to load order details');
                    return;
                }

                // Create modal for order details
                let modalHtml = `
            <div id="orderDetailsModal" class="modal" style="display:flex;">
                <div class="modal-content" style="max-width:800px;width:95%;">
                    <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;">
                        <h3>Order Details - #${result.order.order_number}</h3>
                        <button onclick="closeModal('orderDetailsModal')" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
                    </div>
                    <div class="modal-body">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                            <div>
                                <h4>Buyer Information</h4>
                                <p><strong>Name:</strong> ${escapeHtml(result.order.buyer_name)}</p>
                                <p><strong>Email:</strong> ${escapeHtml(result.order.buyer_email)}</p>
                                <p><strong>Phone:</strong> ${escapeHtml(result.order.buyer_phone || 'N/A')}</p>
                                <p><strong>Address:</strong> ${escapeHtml(result.order.buyer_address || 'N/A')}</p>
                            </div>
                            <div>
                                <h4>Order Information</h4>
                                <p><strong>Total Amount:</strong> Rs. ${parseFloat(result.order.total_amount).toLocaleString()}</p>
                                <p><strong>Status:</strong> ${result.order.order_status}</p>
                                <p><strong>Payment Status:</strong> ${result.order.payment_status}</p>
                                <p><strong>Date:</strong> ${formatDate(result.order.created_at)}</p>
                            </div>
                        </div>
                        <h4>Order Items</h4>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
        `;

                result.items.forEach(item => {
                    modalHtml += `
                <tr>
                    <td>${escapeHtml(item.product_name)}</td>
                    <td>${item.quantity}</td>
                    <td>Rs. ${parseFloat(item.price).toLocaleString()}</td>
                    <td>Rs. ${(parseFloat(item.price) * item.quantity).toLocaleString()}</td>
                </tr>
            `;
                });

                modalHtml += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding:15px;text-align:right;">
                        <button class="btn btn-secondary" onclick="closeModal('orderDetailsModal')">Close</button>
                    </div>
                </div>
            </div>
        `;

                // Remove existing modal if any
                const existingModal = document.getElementById('orderDetailsModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Add modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                document.body.style.overflow = 'hidden';

            } catch (error) {
                console.error('Error loading order details:', error);
                alert('Failed to load order details');
            }
        }

        // Update order status
        async function updateOrderStatus(orderId) {
            const newStatus = prompt('Enter new status (pending, processing, shipped, delivered, completed, cancelled):');

            if (!newStatus) return;

            const validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];
            if (!validStatuses.includes(newStatus.toLowerCase())) {
                alert('Invalid status. Please use: pending, processing, shipped, delivered, completed, cancelled');
                return;
            }

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/updateOrderStatus', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: newStatus.toLowerCase()
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Order status updated successfully!');
                    loadOrders(); // Refresh the orders list
                } else {
                    alert('Failed to update order status: ' + result.message);
                }
            } catch (error) {
                console.error('Error updating order status:', error);
                alert('Network error. Please try again.');
            }
        }

        // Setup order filters with live filtering
        function setupOrderFilters() {
            const filters = ['orderSearch', 'orderStatusFilter', 'orderDateFilter', 'paymentStatusFilter'];

            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', () => loadOrders());
                    if (filterId === 'orderSearch') {
                        let timeout;
                        element.addEventListener('input', () => {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => loadOrders(), 500);
                        });
                    }
                }
            });
        }

        // Modify the initAdminDashboard function to include order filters setup
        // Add this line inside initAdminDashboard after loadOrders():
        // setupOrderFilters();

        // Setup forms
        function setupForms() {
            const sendNotificationForm = document.getElementById('sendNotificationForm');
            if (sendNotificationForm) {
                sendNotificationForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    showNotification('Notification sent successfully!', 'success');
                    closeModal('sendNotificationModal');
                    this.reset();
                });
            }
            const platformSettingsForm = document.getElementById('platformSettingsForm');
            if (platformSettingsForm) {
                platformSettingsForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    showNotification('Platform settings saved successfully!', 'success');
                });
            }
        }

        // Delete user
        async function deleteUser(userId, userRole) {
            if (userRole === 'admin') {
                showNotification('Cannot delete admin users. Admin accounts are protected.', 'error');
                return;
            }
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                return;
            }
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/deleteUser', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId })
                });
                const result = await response.json();
                if (result.success) {
                    showNotification('User deleted successfully', 'success');
                    updateUserCount();
                    window.location.reload();
                } else {
                    showNotification(result.message || 'Error deleting user', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
            }
        }

        function showNotification(message, type) {
            alert(message);
        }

        function viewOrder(orderId) {
            showNotification('Order details modal will be implemented', 'info');
        }

        function exportUsers() {
            showNotification('Exporting users data...', 'info');
        }

        function exportTransactions() {
            showNotification('Exporting transaction data...', 'info');
        }

        function loadAnalytics() {
            document.getElementById('monthlyActiveUsers').textContent = '189';
            document.getElementById('platformGrowth').textContent = '12.5%';
            document.getElementById('userRetention').textContent = '87%';
            document.getElementById('customerSatisfaction').textContent = '94%';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'none';
        }

        // Verification functions
        function loadVerifications() {
            const tbody = document.getElementById('verificationsTableBody');
            let verifications = <?= json_encode($verifications ?? []) ?>;
            const pending = verifications.filter(u => u.verification_status === 'pending').length;
            const approved = verifications.filter(u => u.verification_status === 'approved').length;
            const rejected = verifications.filter(u => u.verification_status === 'rejected').length;
            document.getElementById('vPendingCount').textContent = pending;
            document.getElementById('vApprovedCount').textContent = approved;
            document.getElementById('vRejectedCount').textContent = rejected;
            const badge = document.getElementById('pending-badge');
            if (pending > 0) {
                badge.textContent = pending;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
            if (!verifications || verifications.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:2rem;color:#aaa;">No verifications found.</td></tr>`;
                return;
            }
            let html = '';
            verifications.forEach(user => {
                let statusClass = '';
                switch (user.verification_status) {
                    case 'pending': statusClass = 'badge-warning'; break;
                    case 'approved': statusClass = 'badge-success'; break;
                    case 'rejected': statusClass = 'badge-danger'; break;
                    default: statusClass = '';
                }
                const expectedDocs = user.role === 'farmer' ? ['NIC', 'Bank Details'] : ['Driving License', 'Vehicle Insurance', 'Revenue License'];
                const docsHtml = `<ul style="margin:0;padding-left:18px;font-size:12px;">${expectedDocs.map(d => `<li>${d}</li>`).join('')}</ul><div style="font-size:11px;color:#888;margin-top:4px;">${user.approved_docs ?? 0} approved / ${user.doc_count ?? 0} total</div>`;
                html += `
                    <tr>
                        <td>${user.user_id}</td>
                        <td><strong>${escapeHtml(user.name)}</strong></td>
                        <td style="font-size:13px;">${escapeHtml(user.email)}</td>
                        <td><span class="badge badge-${user.role === 'farmer' ? 'success' : 'warning'}">${user.role}</span></td>
                        <td><span class="badge ${statusClass}" style="text-transform:capitalize;">${user.verification_status}</span></td>
                        <td>${docsHtml}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="openDocReview(${user.user_id})">Review</button>
                            ${user.verification_status === 'pending' ? `
                                <button class="btn btn-sm btn-success" onclick="bulkApprove(${user.user_id})" style="margin-left:4px;">Approve</button>
                                <button class="btn btn-sm btn-danger" onclick="bulkReject(${user.user_id})" style="margin-left:4px;">Reject</button>
                            ` : ''}
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Main openDocReview function - THE WORKING ONE
        async function openDocReview(userId) {
            _currentModalUserId = userId;
            const modal = document.getElementById('docReviewModal');
            const body = document.getElementById('docModalBody');

            if (!modal || !body) {
                console.error('Modal or body element not found');
                return;
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            body.innerHTML = '<p style="text-align:center;padding:2rem;color:#aaa;">Loading documents…</p>';

            try {
                // Use POST method with JSON body
                const res = await fetch(`<?= ROOT ?>/adminDashboard/getUserDocuments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await res.json();

                if (!data.success) {
                    body.innerHTML = `<p style="color:red;padding:1rem;">Error: ${data.message || 'Failed to load documents'}</p>`;
                    return;
                }

                if (!data.data || data.data.length === 0) {
                    body.innerHTML = '<p style="padding:1rem;color:#888;">No documents found for this user.</p>';
                    return;
                }

                const docs = data.data;
                const user = docs[0];
                document.getElementById('docModalTitle').textContent = `Documents — ${user.name} (${user.role})`;

                const docTypeLabels = {
                    nic: 'National Identity Card',
                    bank_details: 'Bank Account Details',
                    driving_license: 'Driving License',
                    vehicle_insurance: 'Vehicle Insurance Card',
                    vehicle_revenue_license: 'Vehicle Revenue License'
                };

                const statusIcon = {
                    pending: '🕐',
                    approved: '✅',
                    rejected: '❌'
                };

                const html = docs.map(doc => {
                    const label = docTypeLabels[doc.doc_type] || doc.doc_type;
                    const isImg = /\.(jpg|jpeg|png|webp)$/i.test(doc.file_path);
                    const isPdf = /\.pdf$/i.test(doc.file_path);

                    const preview = isImg
                        ? `<img src="<?= ROOT ?>/${doc.file_path}" alt="${label}" style="max-width:100%;max-height:280px;border-radius:6px;border:1px solid #ddd;display:block;margin-bottom:10px;" />`
                        : isPdf
                            ? `<a href="<?= ROOT ?>/${doc.file_path}" target="_blank" class="btn btn-secondary btn-sm" style="margin-bottom:10px;">📄 Open PDF</a>`
                            : `<p style="color:#888;font-size:13px;">Preview not available</p>`;

                    const rejectionNote = doc.rejection_reason
                        ? `<div style="background:#fff0f0;border-left:3px solid #e74c3c;padding:8px 12px;border-radius:0 6px 6px 0;font-size:12px;color:#c0392b;margin-bottom:10px;">
                    <strong>Rejection reason:</strong> ${escapeHtml(doc.rejection_reason)}
                   </div>`
                        : '';

                    const statusBadge = doc.status === 'approved'
                        ? '<span style="background:#27ae60;color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;">Approved</span>'
                        : doc.status === 'rejected'
                            ? '<span style="background:#e74c3c;color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;">Rejected</span>'
                            : '<span style="background:#f39c12;color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;">Pending</span>';

                    return `
            <div style="border:1px solid #e8e8e8;border-radius:10px;padding:16px;margin-bottom:16px;background:#fff;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:10px;">
                    <div>
                        <span style="font-weight:600;font-size:14px;">${statusIcon[doc.status] || '📄'} ${label}</span>
                        <span style="margin-left:8px;">${statusBadge}</span>
                    </div>
                    <div style="display:flex;gap:6px;">
                        ${doc.status !== 'approved' ? `<button class="btn btn-sm btn-success" onclick="reviewDoc(${doc.id},'approve')">✓ Approve</button>` : ''}
                        ${doc.status !== 'rejected' ? `<button class="btn btn-sm btn-danger" onclick="promptReject(${doc.id})">✗ Reject</button>` : ''}
                    </div>
                </div>
                ${rejectionNote}
                <div style="margin-top:12px;">
                    ${preview}
                </div>
                <div style="font-size:11px;color:#bbb;margin-top:12px;">
                    Uploaded: ${doc.created_at ? doc.created_at.substring(0, 16) : '—'}
                </div>
            </div>`;
                }).join('');

                const overallStatus = user.verification_status;
                const overallBtns = `
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:1rem;padding-top:1rem;border-top:2px solid #eee;">
            ${overallStatus !== 'approved' ? `<button class="btn btn-success" onclick="bulkApprove(${userId})">✅ Approve All & Verify Account</button>` : ''}
            ${overallStatus !== 'rejected' ? `<button class="btn btn-danger" onclick="bulkReject(${userId})">❌ Reject Account</button>` : ''}
        </div>`;

                // Add status summary at the top
                const totalDocs = docs.length;
                const approvedDocs = docs.filter(d => d.status === 'approved').length;
                const pendingDocs = docs.filter(d => d.status === 'pending').length;
                const rejectedDocs = docs.filter(d => d.status === 'rejected').length;

                const summary = `
        <div style="background:#f8f9fa;padding:12px;border-radius:8px;margin-bottom:20px;display:flex;justify-content:space-around;text-align:center;">
            <div>
                <div style="font-size:20px;font-weight:bold;color:#27ae60;">${approvedDocs}</div>
                <div style="font-size:12px;color:#666;">Approved</div>
            </div>
            <div>
                <div style="font-size:20px;font-weight:bold;color:#f39c12;">${pendingDocs}</div>
                <div style="font-size:12px;color:#666;">Pending</div>
            </div>
            <div>
                <div style="font-size:20px;font-weight:bold;color:#e74c3c;">${rejectedDocs}</div>
                <div style="font-size:12px;color:#666;">Rejected</div>
            </div>
            <div>
                <div style="font-size:20px;font-weight:bold;color:#3498db;">${totalDocs}</div>
                <div style="font-size:12px;color:#666;">Total</div>
            </div>
        </div>`;

                body.innerHTML = summary + html + overallBtns;

            } catch (e) {
                console.error('Error in openDocReview:', e);
                body.innerHTML = '<p style="color:red;padding:1rem;">Failed to load documents. Please try again.</p>';
            }
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function closeDocModal() {
            document.getElementById('docReviewModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        async function reviewDoc(docId, action, reason = null) {
            try {
                const res = await fetch('<?= ROOT ?>/adminDashboard/reviewDocument', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ doc_id: docId, action, reason })
                });
                const data = await res.json();
                if (data.success) {
                    showNotification(data.message, 'success');
                    if (_currentModalUserId) openDocReview(_currentModalUserId);
                    loadVerifications();
                } else {
                    showNotification(data.message || 'Action failed', 'error');
                }
            } catch (e) {
                showNotification('Network error', 'error');
            }
        }

        function promptReject(docId) {
            const reason = prompt('Enter rejection reason (optional):');
            if (reason === null) return;
            reviewDoc(docId, 'reject', reason || null);
        }

        async function bulkApprove(userId) {
            if (!confirm('Approve this account? All pending documents will be marked approved.')) return;
            const res = await fetch(`<?= ROOT ?>/adminDashboard/getUserDocuments/${userId}`);
            const data = await res.json();
            if (data.success) {
                for (const doc of data.data) {
                    if (doc.status === 'pending') {
                        await fetch('<?= ROOT ?>/adminDashboard/reviewDocument', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ doc_id: doc.id, action: 'approve', reason: null })
                        });
                    }
                }
            }
            showNotification('Account approved', 'success');
            closeDocModal();
            window.location.reload();
        }

        async function bulkReject(userId) {
            const reason = prompt('Reason for rejecting this account (required):');
            if (!reason) {
                alert('A reason is required to reject an account.');
                return;
            }
            const res = await fetch('<?= ROOT ?>/adminDashboard/setUserVerification', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId, status: 'rejected' })
            });
            const data = await res.json();
            if (data.success) {
                showNotification('Account rejected', 'success');
                closeDocModal();
                window.location.reload();
            }
        }

        function setVerificationFilter(filter) {
            // Filter functionality - implement as needed
            loadVerifications();
        }

        // ============ PRODUCTS TAB FUNCTIONS ============

        // Global variables for products
        let allProducts = [];
        let productCategories = [];

        // Load products with filters
        async function loadProducts() {
            const tbody = document.getElementById('productsTableBody');

            // Show loading state
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;">Loading products...</td>' + '</tr>';

            try {
                // Get filter values
                const search = document.getElementById('productSearch')?.value || '';
                const category = document.getElementById('categoryFilter')?.value || '';
                const status = document.getElementById('productStatusFilter')?.value || '';
                const minPrice = document.getElementById('minPrice')?.value || '';
                const maxPrice = document.getElementById('maxPrice')?.value || '';

                const response = await fetch('<?= ROOT ?>/adminDashboard/getProducts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        search: search,
                        category: category,
                        status: status,
                        min_price: minPrice,
                        max_price: maxPrice
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:2rem;color:red;">Error: ${result.message}</td>' + '</tr>`;
                    return;
                }

                // Update statistics
                if (result.stats) {
                    document.getElementById('totalProducts').textContent = result.stats.total_products || 0;
                    document.getElementById('activeProducts').textContent = result.stats.active_products || 0;
                    document.getElementById('outOfStockProducts').textContent = result.stats.out_of_stock || 0;
                    document.getElementById('pendingApprovalProducts').textContent = result.stats.pending_approval || 0;
                }

                // Update category filter options if needed
                if (result.categories && result.categories.length > 0) {
                    const categorySelect = document.getElementById('categoryFilter');
                    const currentCategories = Array.from(categorySelect.options).map(opt => opt.value);

                    result.categories.forEach(cat => {
                        if (cat.category && !currentCategories.includes(cat.category)) {
                            const option = document.createElement('option');
                            option.value = cat.category;
                            option.textContent = cat.category.charAt(0).toUpperCase() + cat.category.slice(1);
                            categorySelect.appendChild(option);
                        }
                    });
                }

                allProducts = result.data;
                displayProducts(allProducts);

            } catch (error) {
                console.error('Error loading products:', error);
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:red;">Failed to load products. Please try again.</td>' + '</tr>';
            }
        }

        // Display products in the table
        function displayProducts(products) {
            const tbody = document.getElementById('productsTableBody');

            if (!products || products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:#aaa;">No products found.</td>' + '</tr>';
                return;
            }

            let html = '';
            products.forEach(product => {
                // Status badge class
                let statusClass = '';
                let statusText = '';
                switch (product.status) {
                    case 'active':
                        statusClass = 'badge-success';
                        statusText = 'Active';
                        break;
                    case 'inactive':
                        statusClass = 'badge-secondary';
                        statusText = 'Inactive';
                        break;
                    case 'pending':
                        statusClass = 'badge-warning';
                        statusText = 'Pending';
                        break;
                    case 'rejected':
                        statusClass = 'badge-danger';
                        statusText = 'Rejected';
                        break;
                    default:
                        statusClass = 'badge-secondary';
                        statusText = product.status || 'N/A';
                }

                // Stock badge
                let stockClass = product.stock > 0 ? 'badge-info' : 'badge-danger';
                let stockText = product.stock > 0 ? `In Stock (${product.stock})` : 'Out of Stock';

                // Product image
                const productImage = product.image
                    ? `<?= ROOT ?>/assets/uploads/products/${product.image}`
                    : `<?= ROOT ?>/assets/images/no-image.png`;

                html += `
            <tr>
                <td style="text-align:center;">
                    <img src="${productImage}" alt="${escapeHtml(product.name)}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" onerror="this.src='<?= ROOT ?>/assets/images/no-image.png'">
                </td>
                <td>
                    <strong>${escapeHtml(product.name)}</strong><br>
                    <small style="color:#888;">${escapeHtml(product.description?.substring(0, 50) || 'No description')}${product.description?.length > 50 ? '...' : ''}</small>
                </td>
                <td>${escapeHtml(product.farmer_name)}</td>
                <td><span class="badge badge-primary">${escapeHtml(product.category)}</span></td>
                <td><strong>Rs. ${parseFloat(product.price).toLocaleString()}</strong></td>
                <td><span class="badge ${stockClass}">${stockText}</span></td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewProductDetails(${product.id})">View</button>
                    <button class="btn btn-sm btn-secondary" onclick="updateProductStatus(${product.id}, '${product.status}')">Update Status</button>
                    ${product.total_orders === 0 ? `<button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Delete</button>` : ''}
                </td>
            </tr>
        `;
            });
            tbody.innerHTML = html;
        }

        // View product details
        async function viewProductDetails(productId) {
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/getProductDetails', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId })
                });

                const result = await response.json();

                if (!result.success) {
                    alert('Failed to load product details');
                    return;
                }

                const product = result.product;
                const orders = result.orders || [];

                let ordersHtml = '';
                if (orders.length > 0) {
                    ordersHtml = `
                <h4 style="margin-top:20px;">Recent Orders</h4>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Buyer</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${orders.map(order => `
                                <tr>
                                    <td>#${order.order_number || order.order_id}</td>
                                    <td>${escapeHtml(order.buyer_name)}</td>
                                    <td>${order.quantity}</td>
                                    <td>Rs. ${parseFloat(order.unit_price).toLocaleString()}</td>
                                    <td>Rs. ${(order.quantity * parseFloat(order.unit_price)).toLocaleString()}</td>
                                    <td>${order.order_status}</td>
                                    <td>${formatDate(order.order_date)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
                } else {
                    ordersHtml = '<p style="margin-top:20px;color:#888;">No orders found for this product.</p>';
                }

                const productImage = product.image
                    ? `<?= ROOT ?>/assets/uploads/products/${product.image}`
                    : `<?= ROOT ?>/assets/images/no-image.png`;

                const modalHtml = `
            <div id="productDetailsModal" class="modal" style="display:flex;">
                <div class="modal-content" style="max-width:900px;width:95%;max-height:80vh;overflow-y:auto;">
                    <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;padding:15px;border-bottom:1px solid #eee;">
                        <h3>Product Details - ${escapeHtml(product.name)}</h3>
                        <button onclick="closeModal('productDetailsModal')" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
                    </div>
                    <div class="modal-body" style="padding:20px;">
                        <div style="display:grid;grid-template-columns:auto 1fr;gap:20px;margin-bottom:20px;">
                            <div style="text-align:center;">
                                <img src="${productImage}" 
                                     alt="${escapeHtml(product.name)}" 
                                     style="width:150px;height:150px;object-fit:cover;border-radius:10px;"
                                     onerror="this.src='<?= ROOT ?>/assets/images/no-image.png'">
                            </div>
                            <div>
                                <h3>${escapeHtml(product.name)}</h3>
                                <p><strong>Farmer:</strong> ${escapeHtml(product.farmer_name)}</p>
                                <p><strong>Email:</strong> ${escapeHtml(product.farmer_email)}</p>
                                <p><strong>Phone:</strong> ${escapeHtml(product.farmer_phone || 'N/A')}</p>
                                <p><strong>Category:</strong> ${escapeHtml(product.category)}</p>
                                <p><strong>Price:</strong> Rs. ${parseFloat(product.price).toLocaleString()}</p>
                                <p><strong>Stock:</strong> ${product.quantity} units</p>
                                <p><strong>Status:</strong> ${product.status}</p>
                                <p><strong>Added on:</strong> ${formatDate(product.created_at)}</p>
                            </div>
                        </div>
                        <div>
                            <h4>Description</h4>
                            <p>${escapeHtml(product.description || 'No description available.')}</p>
                        </div>
                        ${ordersHtml}
                    </div>
                    <div class="modal-footer" style="padding:15px;text-align:right;border-top:1px solid #eee;">
                        <button class="btn btn-secondary" onclick="closeModal('productDetailsModal')">Close</button>
                    </div>
                </div>
            </div>
        `;

                // Remove existing modal if any
                const existingModal = document.getElementById('productDetailsModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Add modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                document.body.style.overflow = 'hidden';

            } catch (error) {
                console.error('Error loading product details:', error);
                alert('Failed to load product details');
            }
        }

        // Update product status
        async function updateProductStatus(productId, currentStatus) {
            const statuses = ['active', 'inactive', 'pending', 'rejected'];
            const newStatus = prompt(`Current status: ${currentStatus}\nEnter new status (${statuses.join(', ')}):`);

            if (!newStatus) return;

            if (!statuses.includes(newStatus.toLowerCase())) {
                alert(`Invalid status. Please use: ${statuses.join(', ')}`);
                return;
            }

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/updateProductStatus', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        status: newStatus.toLowerCase()
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Product status updated successfully!');
                    loadProducts(); // Refresh the products list
                } else {
                    alert('Failed to update product status: ' + result.message);
                }
            } catch (error) {
                console.error('Error updating product status:', error);
                alert('Network error. Please try again.');
            }
        }

        // Delete product
        async function deleteProduct(productId) {
            if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/deleteProduct', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Product deleted successfully!');
                    loadProducts(); // Refresh the products list
                } else {
                    alert('Failed to delete product: ' + result.message);
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('Network error. Please try again.');
            }
        }

        // Reset product filters
        function resetProductFilters() {
            document.getElementById('productSearch').value = '';
            document.getElementById('categoryFilter').value = '';
            document.getElementById('productStatusFilter').value = '';
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            loadProducts();
        }

        // Setup product filters with live filtering
        function setupProductFilters() {
            // Add filter container if not exists
            const productsSection = document.getElementById('products-section');
            if (productsSection && !document.querySelector('#products-section .product-filters')) {
                const filtersHtml = `
            <div class="filters product-filters" style="margin-top: var(--spacing-xl);">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="productSearch">Search Products</label>
                        <input type="text" id="productSearch" class="form-control" placeholder="Search by name, farmer, or description...">
                    </div>
                    <div class="filter-group">
                        <label for="categoryFilter">Category</label>
                        <select id="categoryFilter" class="form-control">
                            <option value="">All Categories</option>
                            <option value="vegetables">Vegetables</option>
                            <option value="fruits">Fruits</option>
                            <option value="grains">Grains</option>
                            <option value="dairy">Dairy</option>
                            <option value="meat">Meat</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="productStatusFilter">Status</label>
                        <select id="productStatusFilter" class="form-control">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="filters-row" style="margin-top: 10px;">
                    <div class="filter-group">
                        <label for="minPrice">Min Price (Rs.)</label>
                        <input type="number" id="minPrice" class="form-control" placeholder="Min price">
                    </div>
                    <div class="filter-group">
                        <label for="maxPrice">Max Price (Rs.)</label>
                        <input type="number" id="maxPrice" class="form-control" placeholder="Max price">
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-secondary" onclick="resetProductFilters()">Reset Filters</button>
                    </div>
                </div>
            </div>
        `;

                // Insert filters after the stats
                const statsDiv = productsSection.querySelector('.dashboard-stats');
                if (statsDiv && !document.getElementById('productSearch')) {
                    statsDiv.insertAdjacentHTML('afterend', filtersHtml);
                }
            }

            const searchInput = document.getElementById('productSearch');
            const categorySelect = document.getElementById('categoryFilter');
            const statusSelect = document.getElementById('productStatusFilter');
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');

            if (searchInput) {
                let timeout;
                searchInput.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => loadProducts(), 500);
                });
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', () => loadProducts());
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', () => loadProducts());
            }

            if (minPrice) {
                minPrice.addEventListener('change', () => loadProducts());
            }

            if (maxPrice) {
                maxPrice.addEventListener('change', () => loadProducts());
            }
        }

        // Update the products table headers to include image column
        function updateProductsTableHeaders() {
            const productsTable = document.querySelector('#products-section .table');
            if (productsTable) {
                const thead = productsTable.querySelector('thead');
                if (thead) {
                    thead.innerHTML = `
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Farmer</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            `;
                }
            }
        }

        // Add this to your initAdminDashboard function
        // Find the initAdminDashboard function and add loadProducts() and setupProductFilters()
        // The function should look like this (update it):
        /*
        function initAdminDashboard() {
            loadDashboardData();
            loadUsers();
            loadVerifications();
            loadOrders();
            loadProducts();  // Add this line
            setupOrderFilters();
            setupProductFilters();  // Add this line
            setupNavigation();
            setupForms();
            showSection('dashboard');
        }
        */

        // Analytics functions
        let revenueChart = null;
        let userGrowthChart = null;
        let categoryChart = null;

        async function loadAnalytics() {
            const period = document.getElementById('analyticsPeriod')?.value || 'month';

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/getAnalytics', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ period: period })
                });

                const result = await response.json();

                if (!result.success) {
                    console.error('Failed to load analytics:', result.message);
                    return;
                }

                const data = result.data;

                // Update KPIs
                document.getElementById('totalRevenue').textContent = `Rs. ${(data.order_stats?.total_revenue || 0).toLocaleString()}`;
                document.getElementById('totalOrders').textContent = data.order_stats?.total_orders || 0;
                document.getElementById('totalUsers').textContent = data.user_stats?.total_users || 0;
                document.getElementById('avgOrderValue').textContent = `Rs. ${Math.round(data.order_stats?.avg_order_value || 0).toLocaleString()}`;

                const revenueGrowth = data.growth_metrics?.revenue_growth || 0;
                const growthElement = document.getElementById('revenueGrowth');
                if (growthElement) {
                    growthElement.textContent = `${revenueGrowth >= 0 ? '+' : ''}${revenueGrowth}%`;
                    growthElement.style.color = revenueGrowth >= 0 ? '#27ae60' : '#e74c3c';
                }

                // Update charts
                updateRevenueChart(data.monthly_revenue);
                updateUserGrowthChart(data.user_growth);
                updateCategoryChart(data.category_distribution);
                updateTopProducts(data.top_products);

            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }

        function updateRevenueChart(monthlyRevenue) {
            const ctx = document.getElementById('revenueChart')?.getContext('2d');
            if (!ctx) return;

            const months = monthlyRevenue.map(m => {
                const date = new Date(m.month + '-01');
                return date.toLocaleString('default', { month: 'short' });
            });
            const revenues = monthlyRevenue.map(m => parseFloat(m.revenue || 0));

            if (revenueChart) {
                revenueChart.destroy();
            }

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Revenue (Rs.)',
                        data: revenues,
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `Rs. ${context.raw.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateUserGrowthChart(userGrowth) {
            const ctx = document.getElementById('userGrowthChart')?.getContext('2d');
            if (!ctx) return;

            const months = userGrowth.map(u => {
                const date = new Date(u.month + '-01');
                return date.toLocaleString('default', { month: 'short' });
            });
            const newUsers = userGrowth.map(u => parseInt(u.new_users || 0));
            const newFarmers = userGrowth.map(u => parseInt(u.new_farmers || 0));
            const newBuyers = userGrowth.map(u => parseInt(u.new_buyers || 0));

            if (userGrowthChart) {
                userGrowthChart.destroy();
            }

            userGrowthChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'New Farmers',
                            data: newFarmers,
                            backgroundColor: '#3498db',
                            borderRadius: 5
                        },
                        {
                            label: 'New Buyers',
                            data: newBuyers,
                            backgroundColor: '#e74c3c',
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        function updateCategoryChart(categories) {
            const ctx = document.getElementById('categoryChart')?.getContext('2d');
            if (!ctx) return;

            const categoryNames = categories.map(c => c.category || 'Other');
            const productCounts = categories.map(c => parseInt(c.product_count || 0));

            if (categoryChart) {
                categoryChart.destroy();
            }

            const colors = ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6', '#1abc9c', '#e67e22', '#34495e'];

            categoryChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: categoryNames,
                    datasets: [{
                        data: productCounts,
                        backgroundColor: colors.slice(0, categoryNames.length),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const total = productCounts.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.raw / total) * 100).toFixed(1);
                                    return `${context.label}: ${context.raw} products (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateTopProducts(products) {
            const tbody = document.getElementById('topProductsBody');
            if (!tbody) return;

            if (!products || products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">No products found</td></tr>';
                return;
            }

            let html = '';
            products.slice(0, 5).forEach(product => {
                html += `
            <tr>
                <td>${escapeHtml(product.name)}</td>
                <td>${product.total_orders || 0}</td>
                <td><strong>Rs. ${parseFloat(product.total_revenue || 0).toLocaleString()}</strong></td>
            </tr>
        `;
            });
            tbody.innerHTML = html;
        }

        function refreshAnalytics() {
            loadAnalytics();
        }

        // Update the initAdminDashboard function to include analytics loading
        // Add this line inside initAdminDashboard after showSection:
        // if (typeof loadAnalytics === 'function') loadAnalytics();

        // ============ PAYMENTS TAB FUNCTIONS ============

        let allPayments = [];

        // Load payments with filters
        async function loadPayments() {
            const tbody = document.getElementById('paymentsTableBody');

            // Show loading state
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;">Loading payments...</td></tr>';

            try {
                // Get filter values
                const search = document.getElementById('paymentSearch')?.value || '';
                const status = document.getElementById('paymentStatusFilter')?.value || '';
                const method = document.getElementById('paymentMethodFilter')?.value || '';
                const dateRange = document.getElementById('paymentDateFilter')?.value || '';

                const response = await fetch('<?= ROOT ?>/adminDashboard/getPayments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        search: search,
                        status: status,
                        method: method,
                        date_range: dateRange
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:2rem;color:red;">Error: ${result.message}</td></tr>`;
                    return;
                }

                // Update statistics
                if (result.stats) {
                    document.getElementById('totalRevenue').textContent = `Rs. ${(result.stats.total_revenue || 0).toLocaleString()}`;
                    document.getElementById('totalTransactions').textContent = result.stats.total_transactions || 0;
                    document.getElementById('platformCommission').textContent = `Rs. ${(result.stats.platform_commission || 0).toLocaleString()}`;
                    document.getElementById('avgPaymentAmount').textContent = `Rs. ${Math.round(result.stats.avg_payment_amount || 0).toLocaleString()}`;

                    document.getElementById('completedPayments').textContent = result.stats.completed_count || 0;
                    document.getElementById('pendingPayments').textContent = result.stats.pending_count || 0;
                    document.getElementById('failedPayments').textContent = result.stats.failed_count || 0;
                    document.getElementById('refundedPayments').textContent = result.stats.refunded_count || 0;

                    document.getElementById('codRevenue').textContent = `Rs. ${(result.stats.cod_revenue || 0).toLocaleString()}`;
                    document.getElementById('bankRevenue').textContent = `Rs. ${(result.stats.bank_revenue || 0).toLocaleString()}`;
                    document.getElementById('cardRevenue').textContent = `Rs. ${(result.stats.card_revenue || 0).toLocaleString()}`;
                    document.getElementById('mobileRevenue').textContent = `Rs. ${(result.stats.mobile_revenue || 0).toLocaleString()}`;
                }

                allPayments = result.data;
                displayPayments(allPayments);

            } catch (error) {
                console.error('Error loading payments:', error);
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;color:red;">Failed to load payments. Please try again.</td></tr>';
            }
        }

        // Display payments in the table
        function displayPayments(payments) {
            const tbody = document.getElementById('paymentsTableBody');

            if (!payments || payments.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;color:#aaa;">No payments found.</td></tr>';
                return;
            }

            let html = '';
            payments.forEach(payment => {
                // Status badge class
                let statusClass = '';
                let statusText = '';
                switch (payment.payment_status) {
                    case 'completed':
                        statusClass = 'badge-success';
                        statusText = 'Completed';
                        break;
                    case 'pending':
                        statusClass = 'badge-warning';
                        statusText = 'Pending';
                        break;
                    case 'failed':
                        statusClass = 'badge-danger';
                        statusText = 'Failed';
                        break;
                    case 'refunded':
                        statusClass = 'badge-info';
                        statusText = 'Refunded';
                        break;
                    default:
                        statusClass = 'badge-secondary';
                        statusText = payment.payment_status || 'N/A';
                }

                // Payment method display
                let methodText = '';
                switch (payment.payment_method) {
                    case 'cash_on_delivery': methodText = 'Cash on Delivery'; break;
                    case 'bank_transfer': methodText = 'Bank Transfer'; break;
                    case 'card': methodText = 'Card Payment'; break;
                    case 'mobile_payment': methodText = 'Mobile Payment'; break;
                    default: methodText = payment.payment_method || 'N/A';
                }

                html += `
            <tr>
                <td><strong>#${payment.transaction_id || payment.payment_id}</strong></td>
                <td>#${payment.order_number || payment.order_id}</td>
                <td>${escapeHtml(payment.buyer_name || 'N/A')}</td>
                <td><strong>Rs. ${parseFloat(payment.amount).toLocaleString()}</strong></td>
                <td>${methodText}</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>${formatDate(payment.payment_date || payment.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewPaymentDetails(${payment.payment_id})">View</button>
                    ${payment.payment_status === 'pending' ? `<button class="btn btn-sm btn-success" onclick="updatePaymentStatus(${payment.payment_id}, 'completed')">Complete</button>` : ''}
                    ${payment.payment_status === 'completed' ? `<button class="btn btn-sm btn-warning" onclick="refundPayment(${payment.payment_id})">Refund</button>` : ''}
                </td>
            </tr>
        `;
            });
            tbody.innerHTML = html;
        }

        // View payment details
        async function viewPaymentDetails(paymentId) {
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/getPaymentDetails', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ payment_id: paymentId })
                });

                const result = await response.json();

                if (!result.success) {
                    alert('Failed to load payment details');
                    return;
                }

                const payment = result.payment;

                const modalHtml = `
            <div id="paymentDetailsModalInner" class="modal" style="display:flex;">
                <div class="modal-content" style="max-width:600px;width:95%;">
                    <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;padding:15px;border-bottom:1px solid #eee;">
                        <h3>Payment Details - #${payment.transaction_id || payment.id}</h3>
                        <button onclick="closeModal('paymentDetailsModalInner')" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
                    </div>
                    <div class="modal-body" style="padding:20px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                            <div>
                                <p><strong>Transaction ID:</strong></p>
                                <p>${payment.transaction_id || 'N/A'}</p>
                                
                                <p><strong>Order ID:</strong></p>
                                <p>#${payment.order_number || payment.order_id}</p>
                                
                                <p><strong>Amount:</strong></p>
                                <p><strong>Rs. ${parseFloat(payment.amount).toLocaleString()}</strong></p>
                                
                                <p><strong>Payment Method:</strong></p>
                                <p>${payment.payment_method}</p>
                            </div>
                            <div>
                                <p><strong>Status:</strong></p>
                                <p><span class="badge badge-${payment.payment_status === 'completed' ? 'success' : payment.payment_status === 'pending' ? 'warning' : 'danger'}">${payment.payment_status}</span></p>
                                
                                <p><strong>Buyer Name:</strong></p>
                                <p>${escapeHtml(payment.buyer_name)}</p>
                                
                                <p><strong>Buyer Email:</strong></p>
                                <p>${escapeHtml(payment.buyer_email)}</p>
                                
                                <p><strong>Payment Date:</strong></p>
                                <p>${formatDate(payment.payment_date || payment.created_at)}</p>
                            </div>
                        </div>
                        ${payment.refund_reason ? `
                            <div style="margin-top:15px;padding:10px;background:#fff0f0;border-radius:5px;">
                                <p><strong>Refund Reason:</strong></p>
                                <p>${escapeHtml(payment.refund_reason)}</p>
                                <p><strong>Refund Date:</strong> ${formatDate(payment.refund_date)}</p>
                            </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer" style="padding:15px;text-align:right;border-top:1px solid #eee;">
                        <button class="btn btn-secondary" onclick="closeModal('paymentDetailsModalInner')">Close</button>
                    </div>
                </div>
            </div>
        `;

                // Remove existing modal if any
                const existingModal = document.getElementById('paymentDetailsModalInner');
                if (existingModal) {
                    existingModal.remove();
                }

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                document.body.style.overflow = 'hidden';

            } catch (error) {
                console.error('Error loading payment details:', error);
                alert('Failed to load payment details');
            }
        }

        // Update payment status
        async function updatePaymentStatus(paymentId, status) {
            if (!confirm(`Are you sure you want to mark this payment as ${status}?`)) {
                return;
            }

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/updatePaymentStatus', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        payment_id: paymentId,
                        status: status
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Payment status updated successfully!');
                    loadPayments(); // Refresh the payments list
                } else {
                    alert('Failed to update payment status: ' + result.message);
                }
            } catch (error) {
                console.error('Error updating payment status:', error);
                alert('Network error. Please try again.');
            }
        }

        // Refund payment
        async function refundPayment(paymentId) {
            const reason = prompt('Enter refund reason:');
            if (!reason) return;

            if (!confirm('Are you sure you want to refund this payment?')) {
                return;
            }

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/refundPayment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        payment_id: paymentId,
                        reason: reason
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Payment refunded successfully!');
                    loadPayments(); // Refresh the payments list
                } else {
                    alert('Failed to refund payment: ' + result.message);
                }
            } catch (error) {
                console.error('Error refunding payment:', error);
                alert('Network error. Please try again.');
            }
        }

        // Reset payment filters
        function resetPaymentFilters() {
            document.getElementById('paymentSearch').value = '';
            document.getElementById('paymentStatusFilter').value = '';
            document.getElementById('paymentMethodFilter').value = '';
            document.getElementById('paymentDateFilter').value = '';
            loadPayments();
        }

        // Export payments to CSV
        function exportPayments() {
            if (!allPayments || allPayments.length === 0) {
                alert('No data to export');
                return;
            }

            let csv = 'Transaction ID,Order ID,Buyer,Amount,Payment Method,Status,Date\n';

            allPayments.forEach(payment => {
                csv += `"${payment.transaction_id || payment.payment_id}","${payment.order_number || payment.order_id}","${payment.buyer_name}",${payment.amount},"${payment.payment_method}","${payment.payment_status}","${payment.payment_date || payment.created_at}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `payments_export_${new Date().toISOString().slice(0, 19)}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Setup payment filters
        function setupPaymentFilters() {
            const filters = ['paymentSearch', 'paymentStatusFilter', 'paymentMethodFilter', 'paymentDateFilter'];

            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    if (filterId === 'paymentSearch') {
                        let timeout;
                        element.addEventListener('input', () => {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => loadPayments(), 500);
                        });
                    } else {
                        element.addEventListener('change', () => loadPayments());
                    }
                }
            });
        }

        // ============ DISPUTES TAB FUNCTIONS ============

        let allDisputes = [];

        // Load disputes with filters
        async function loadDisputes() {
            const tbody = document.getElementById('disputesTableBody');

            // Show loading state
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:2rem;">Loading disputes...</td>' + '</tr>';

            try {
                // Get filter values
                const search = document.getElementById('disputeSearch')?.value || '';
                const status = document.getElementById('disputeStatusFilter')?.value || '';
                const type = document.getElementById('disputeTypeFilter')?.value || '';
                const priority = document.getElementById('disputePriorityFilter')?.value || '';

                const response = await fetch('<?= ROOT ?>/adminDashboard/getDisputes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        search: search,
                        status: status,
                        type: type,
                        priority: priority
                    })
                });

                const result = await response.json();

                if (!result.success) {
                    tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:2rem;color:red;">Error: ${result.message}</td>' + '</tr>`;
                    return;
                }

                // Update statistics
                if (result.stats) {
                    document.getElementById('totalDisputes').textContent = result.stats.total_disputes || 0;
                    document.getElementById('openDisputes').textContent = result.stats.open_disputes || 0;
                    document.getElementById('resolvedDisputes').textContent = result.stats.resolved_disputes || 0;
                    document.getElementById('averageResolutionTime').textContent = result.stats.avg_resolution_hours || 0;

                    document.getElementById('highPriorityDisputes').textContent = result.stats.high_priority || 0;
                    document.getElementById('mediumPriorityDisputes').textContent = result.stats.medium_priority || 0;
                    document.getElementById('lowPriorityDisputes').textContent = result.stats.low_priority || 0;

                    document.getElementById('orderIssues').textContent = result.stats.order_issues || 0;
                    document.getElementById('paymentIssues').textContent = result.stats.payment_issues || 0;
                    document.getElementById('deliveryIssues').textContent = result.stats.delivery_issues || 0;
                    document.getElementById('qualityIssues').textContent = result.stats.quality_issues || 0;
                }

                allDisputes = result.data;
                displayDisputes(allDisputes);

            } catch (error) {
                console.error('Error loading disputes:', error);
                tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:2rem;color:red;">Failed to load disputes. Please try again.</td>' + '</tr>';
            }
        }

        // Display disputes in the table
        function displayDisputes(disputes) {
            const tbody = document.getElementById('disputesTableBody');

            if (!disputes || disputes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:2rem;color:#aaa;">No disputes found.</td>' + '</tr>';
                return;
            }

            let html = '';
            disputes.forEach(dispute => {
                // Priority badge
                let priorityClass = '';
                let priorityIcon = '';
                switch (dispute.priority) {
                    case 'high':
                        priorityClass = 'badge-danger';
                        priorityIcon = '🔴';
                        break;
                    case 'medium':
                        priorityClass = 'badge-warning';
                        priorityIcon = '🟡';
                        break;
                    case 'low':
                        priorityClass = 'badge-info';
                        priorityIcon = '🔵';
                        break;
                    default:
                        priorityClass = 'badge-secondary';
                        priorityIcon = '⚪';
                }

                // Status badge
                let statusClass = '';
                let statusText = '';
                switch (dispute.status) {
                    case 'open':
                        statusClass = 'badge-danger';
                        statusText = 'Open';
                        break;
                    case 'in_progress':
                        statusClass = 'badge-warning';
                        statusText = 'In Progress';
                        break;
                    case 'resolved':
                        statusClass = 'badge-success';
                        statusText = 'Resolved';
                        break;
                    case 'closed':
                        statusClass = 'badge-secondary';
                        statusText = 'Closed';
                        break;
                    default:
                        statusClass = 'badge-secondary';
                        statusText = dispute.status || 'N/A';
                }

                // Type display
                let typeText = '';
                switch (dispute.type) {
                    case 'order_issue': typeText = 'Order Issue'; break;
                    case 'payment_issue': typeText = 'Payment Issue'; break;
                    case 'delivery_issue': typeText = 'Delivery Issue'; break;
                    case 'product_quality': typeText = 'Product Quality'; break;
                    default: typeText = dispute.type || 'N/A';
                }

                html += `
            <tr>
                <td><strong>#${dispute.dispute_id}</strong></td>
                <td>#${dispute.order_number}</td>
                <td>${escapeHtml(dispute.complainant_name)}<br><small>(${dispute.complainant_role})</small></td>
                <td>${escapeHtml(dispute.respondent_name)}<br><small>(${dispute.respondent_role})</small></td>
                <td>${typeText}</td>
                <td><span class="badge ${priorityClass}">${priorityIcon} ${dispute.priority ? dispute.priority.charAt(0).toUpperCase() + dispute.priority.slice(1) : 'N/A'}</span></td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>${formatDate(dispute.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewDisputeDetails(${dispute.dispute_id})">View</button>
                    ${dispute.status !== 'resolved' && dispute.status !== 'closed' ? `<button class="btn btn-sm btn-success" onclick="resolveDispute(${dispute.dispute_id})">Resolve</button>` : ''}
                </td>
            </tr>
        `;
            });
            tbody.innerHTML = html;
        }

        // View dispute details
        async function viewDisputeDetails(disputeId) {
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/getDisputeDetails', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ dispute_id: disputeId })
                });

                const result = await response.json();

                if (!result.success) {
                    alert('Failed to load dispute details');
                    return;
                }

                const dispute = result.dispute;
                const messages = result.messages || [];

                // Create messages HTML
                let messagesHtml = '';
                if (messages.length > 0) {
                    messagesHtml = '<h4>Conversation History</h4><div style="max-height:300px;overflow-y:auto;margin-top:10px;">';
                    messages.forEach(msg => {
                        const isAdmin = msg.sender_role === 'admin';
                        messagesHtml += `
                    <div style="margin-bottom:15px;padding:10px;background:${isAdmin ? '#e3f2fd' : '#f5f5f5'};border-radius:8px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                            <strong>${escapeHtml(msg.sender_name)} (${msg.sender_role})</strong>
                            <small style="color:#888;">${formatDate(msg.created_at)}</small>
                        </div>
                        <p style="margin:0;">${escapeHtml(msg.message)}</p>
                    </div>
                `;
                    });
                    messagesHtml += '</div>';
                }

                // Type display
                let typeText = '';
                switch (dispute.type) {
                    case 'order_issue': typeText = 'Order Issue'; break;
                    case 'payment_issue': typeText = 'Payment Issue'; break;
                    case 'delivery_issue': typeText = 'Delivery Issue'; break;
                    case 'product_quality': typeText = 'Product Quality'; break;
                    default: typeText = dispute.type || 'N/A';
                }

                const modalHtml = `
            <div id="disputeDetailsModalInner" class="modal" style="display:flex;">
                <div class="modal-content" style="max-width:800px;width:95%;max-height:80vh;overflow-y:auto;">
                    <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;padding:15px;border-bottom:1px solid #eee;">
                        <h3>Dispute Details - #${dispute.dispute_id}</h3>
                        <button onclick="closeModal('disputeDetailsModalInner')" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
                    </div>
                    <div class="modal-body" style="padding:20px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px;">
                            <div>
                                <p><strong>Order ID:</strong> #${dispute.order_number}</p>
                                <p><strong>Order Amount:</strong> Rs. ${parseFloat(dispute.order_amount).toLocaleString()}</p>
                                <p><strong>Order Status:</strong> ${dispute.order_status}</p>
                                <p><strong>Dispute Type:</strong> ${typeText}</p>
                                <p><strong>Priority:</strong> ${dispute.priority}</p>
                                <p><strong>Status:</strong> ${dispute.status}</p>
                            </div>
                            <div>
                                <p><strong>Complainant:</strong> ${escapeHtml(dispute.complainant_name)}</p>
                                <p><strong>Complainant Email:</strong> ${escapeHtml(dispute.complainant_email)}</p>
                                <p><strong>Complainant Phone:</strong> ${escapeHtml(dispute.complainant_phone || 'N/A')}</p>
                                <p><strong>Respondent:</strong> ${escapeHtml(dispute.respondent_name)}</p>
                                <p><strong>Respondent Email:</strong> ${escapeHtml(dispute.respondent_email)}</p>
                                <p><strong>Created:</strong> ${formatDate(dispute.created_at)}</p>
                            </div>
                        </div>
                        
                        <div style="margin-bottom:20px;">
                            <h4>Dispute Reason</h4>
                            <p style="padding:10px;background:#f8f9fa;border-radius:8px;">${escapeHtml(dispute.reason)}</p>
                        </div>
                        
                        ${dispute.resolution_notes ? `
                            <div style="margin-bottom:20px;">
                                <h4>Resolution Notes</h4>
                                <p style="padding:10px;background:#d4edda;border-radius:8px;">${escapeHtml(dispute.resolution_notes)}</p>
                                ${dispute.resolved_at ? `<p><small>Resolved on: ${formatDate(dispute.resolved_at)}</small></p>` : ''}
                            </div>
                        ` : ''}
                        
                        ${messagesHtml}
                        
                        ${dispute.status !== 'resolved' && dispute.status !== 'closed' ? `
                            <div style="margin-top:20px;">
                                <h4>Add Message</h4>
                                <textarea id="disputeMessage" class="form-control" rows="3" placeholder="Type your message here..."></textarea>
                                <button class="btn btn-primary" style="margin-top:10px;" onclick="addDisputeMessage(${dispute.dispute_id})">Send Message</button>
                            </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer" style="padding:15px;text-align:right;border-top:1px solid #eee;">
                        ${dispute.status !== 'resolved' && dispute.status !== 'closed' ? `
                            <button class="btn btn-success" onclick="resolveDispute(${dispute.dispute_id})">Mark as Resolved</button>
                        ` : ''}
                        <button class="btn btn-secondary" onclick="closeModal('disputeDetailsModalInner')">Close</button>
                    </div>
                </div>
            </div>
        `;

                // Remove existing modal if any
                const existingModal = document.getElementById('disputeDetailsModalInner');
                if (existingModal) {
                    existingModal.remove();
                }

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                document.body.style.overflow = 'hidden';

            } catch (error) {
                console.error('Error loading dispute details:', error);
                alert('Failed to load dispute details');
            }
        }

        // Add dispute message
        async function addDisputeMessage(disputeId) {
            const message = document.getElementById('disputeMessage')?.value;
            if (!message) {
                alert('Please enter a message');
                return;
            }

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/addDisputeMessage', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        dispute_id: disputeId,
                        message: message,
                        sender_id: <?= $_SESSION['USER']->id ?? 'null' ?>
            })
                });

                const result = await response.json();

                if (result.success) {
                    // Refresh the dispute details
                    viewDisputeDetails(disputeId);
                } else {
                    alert('Failed to send message: ' + result.message);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Network error. Please try again.');
            }
        }

        // Update dispute status
        async function updateDisputeStatus(disputeId, status, resolutionNotes = null) {
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/updateDisputeStatus', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        dispute_id: disputeId,
                        status: status,
                        resolution_notes: resolutionNotes
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Dispute status updated successfully!');
                    closeModal('disputeDetailsModalInner');
                    loadDisputes(); // Refresh the disputes list
                } else {
                    alert('Failed to update dispute status: ' + result.message);
                }
            } catch (error) {
                console.error('Error updating dispute status:', error);
                alert('Network error. Please try again.');
            }
        }

        // Resolve dispute
        async function resolveDispute(disputeId) {
            const resolution = prompt('Enter resolution notes:');
            if (!resolution) return;

            if (!confirm('Mark this dispute as resolved?')) {
                return;
            }

            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/resolveDispute', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        dispute_id: disputeId,
                        resolution: resolution
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Dispute resolved successfully!');
                    closeModal('disputeDetailsModalInner');
                    loadDisputes(); // Refresh the disputes list
                } else {
                    alert('Failed to resolve dispute: ' + result.message);
                }
            } catch (error) {
                console.error('Error resolving dispute:', error);
                alert('Network error. Please try again.');
            }
        }

        // Reset dispute filters
        function resetDisputeFilters() {
            document.getElementById('disputeSearch').value = '';
            document.getElementById('disputeStatusFilter').value = '';
            document.getElementById('disputeTypeFilter').value = '';
            document.getElementById('disputePriorityFilter').value = '';
            loadDisputes();
        }

        // Setup dispute filters
        function setupDisputeFilters() {
            const filters = ['disputeSearch', 'disputeStatusFilter', 'disputeTypeFilter', 'disputePriorityFilter'];

            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    if (filterId === 'disputeSearch') {
                        let timeout;
                        element.addEventListener('input', () => {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => loadDisputes(), 500);
                        });
                    } else {
                        element.addEventListener('change', () => loadDisputes());
                    }
                }
            });
        }

        // User management functions
        async function updateUserCount() {
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/updateUserCount');
                const result = await response.json();
                if (result.success) {
                    document.getElementById('totalUsers').textContent = result.userCount;
                }
            } catch (error) {
                console.error('Error loading user content:', error);
            }
        }

        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.getElementById('addUserForm').reset();
            document.getElementById('addUserMessage').style.display = 'none';
            document.getElementById('addUserFormErrors').style.display = 'none';
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('addUserForm').reset();
        }

        async function openUpdateUserModal(userId) {
            try {
                const response = await fetch(`<?= ROOT ?>/adminDashboard/getUser/${userId}`);
                const result = await response.json();
                if (result.success) {
                    populateUpdateModal(result.data);
                    document.getElementById('updateUserModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    showMessage(result.message || 'Failed to load user details', 'error');
                }
            } catch (error) {
                console.error('Error loading user details:', error);
                showMessage('Network error occurred', 'error');
            }
        }

        function closeUpdateUserModal() {
            document.getElementById('updateUserModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('updateUserForm').reset();
        }

        function populateUpdateModal(user) {
            document.getElementById('updateUserId').value = user.id;
            document.getElementById('updateName').value = user.name;
            document.getElementById('updateEmail').value = user.email;
            document.getElementById('updateRole').value = user.role;
            document.getElementById('updatePass').value = '';
            document.getElementById('updateUserMessage').style.display = 'none';
            document.getElementById('updateUserFormErrors').style.display = 'none';
        }

        // Form submissions
        document.getElementById('addUserForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            document.getElementById('addUserFormErrors').style.display = 'none';
            document.getElementById('addUserFormErrors').innerHTML = '';
            const password = document.getElementById('userPass').value;
            const confirmPassword = document.getElementById('userConfirmPass').value;
            if (password !== confirmPassword) {
                document.getElementById('addUserFormErrors').innerHTML = '<strong>Error:</strong> Passwords do not match.';
                document.getElementById('addUserFormErrors').style.display = 'block';
                return;
            }
            const formData = new FormData(this);
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/register', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                showMessage(result.message, result.success ? 'success' : 'error', 'addUserMessage');
                if (result.success) {
                    showNotification('User added successfully!', 'success');
                    closeAddUserModal();
                    this.reset();
                    updateUserCount();
                    window.location.reload();
                } else if (result.errors) {
                    let errorHtml = '<strong>Please fix the following errors:</strong><ul>';
                    for (const error in result.errors) {
                        errorHtml += `<li>${result.errors[error]}</li>`;
                    }
                    errorHtml += '</ul>';
                    document.getElementById('addUserFormErrors').innerHTML = errorHtml;
                    document.getElementById('addUserFormErrors').style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error occurred. Please try again.', 'error', 'addUserMessage');
            }
        });

        document.getElementById('updateUserForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('<?= ROOT ?>/adminDashboard/updateUser', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showMessage('User updated successfully!', 'success', 'updateUserMessage');
                    closeUpdateUserModal();
                    loadUsers();
                    window.location.reload();
                } else {
                    showMessage(result.message || 'Failed to update user', 'error', 'updateUserMessage');
                    if (result.errors) {
                        let errorHtml = '<strong>Please fix the following errors:</strong><ul>';
                        for (const error in result.errors) {
                            errorHtml += `<li>${result.errors[error]}</li>`;
                        }
                        errorHtml += '</ul>';
                        document.getElementById('updateUserFormErrors').innerHTML = errorHtml;
                        document.getElementById('updateUserFormErrors').style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Error updating user:', error);
                showMessage('Network error while updating user', 'error', 'updateUserMessage');
            }
        });

        function showMessage(message, type, elementId = 'message') {
            const messageDiv = document.getElementById(elementId);
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function () {
            initAdminDashboard();
            updateUserCount();
            setInterval(updateUserCount, 30000);
        });
    </script>
    <script src="<?= ROOT ?>/assets/js/topnavbar.js"></script>
</body>

</html>