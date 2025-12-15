<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AgroLink</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/css/style2.css">
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="logo-section">
            <img src="<?=ROOT?>/assets/imgs/Logo.png" alt="AgroLink">
        </div>
        <div class="user-section">
            <!-- <div class="user-info"></div> --> <!--REMOVED THIS DIV! CHECK JS-->
                <div>
                    <div class="user-avatar" id="userAvatar">AD</div>

                </div>
                <div>
                    <div class="user-name" id="adminName"><?=$username?></div>
                    <div class="user-role">Admin</div>
                </div><!-- 
                <button class="logout-btn" onclick="logout()">Logout</button> -->
                <form method="POST" action="<?=ROOT?>/logout" style="display: inline;">
                        <button type="submit" class="logout-btn btn logout-link">Logout</button>
                    </form>
        </div>
    </nav>
    

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
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </div>
                    Dashboard
                </a></li>
                <li><a href="#users" class="menu-link" data-section="users">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    Users
                </a></li>
                <li><a href="#orders" class="menu-link" data-section="orders">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        </svg>
                    </div>
                    Orders
                </a></li>
                <li><a href="#products" class="menu-link" data-section="products">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>
                    Products
                </a></li>
                <li><a href="#payments" class="menu-link" data-section="payments">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                    </div>
                    Payments
                </a></li>
                <li><a href="#disputes" class="menu-link" data-section="disputes">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v18"/>
                            <path d="M5 12h14"/>
                        </svg>
                    </div>
                    Disputes
                </a></li>
                <li><a href="#analytics" class="menu-link" data-section="analytics">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="20" x2="12" y2="10"/>
                            <line x1="18" y1="20" x2="18" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    Analytics
                </a></li>
                <li><a href="#notifications" class="menu-link" data-section="notifications">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </div>
                    Notifications
                </a></li>
                <li><a href="#settings" class="menu-link" data-section="settings">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.26 1.3.73 1.77.47.47 1.11.73 1.77.73H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
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
            <div class="stat-number" id="totalUsers"><?=($farmers + $buyers + $transporters + $admins)?></div>
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
        <!-- <div class="stat-card">
            <div class="stat-number" id="systemHealth">98%</div>
            <div class="stat-label">System Health</div>
        </div> -->
    </div>

    <!-- User Summary Card -->
    <div class="content-card" style="margin-top: var(--spacing-xl);">
        <div class="card-header">
            <h3 class="card-title">User Summary</h3>
        </div>
        <div class="card-content">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; padding: 20px;">
                <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;" id="farmerCount"><?php show($farmers);?></div>
                    <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Farmers</div>
                </div>
                <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;" id="buyerCount"><?php show($buyers);?></div>
                    <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Buyers</div>
                </div>
                <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;" id="transporterCount"><?php show($transporters);?></div>
                    <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Transporters</div>
                </div>
                <div style="text-align: center; padding: 24px; background: #f5f5f5; border-radius: 12px;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;" id="adminCount"><?php show($admins);?></div>
                    <div style="font-size: 1rem; color: #2c3e50; font-weight: 600;">Admins</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-top: var(--spacing-xl);">
    <!-- Recent Orders Card -->
    <div class="elegant-card">
        <div class="card-header-elegant">
           <!-- <div class="card-icon"></div> -->
            <div class="card-header-content">
                <h3 class="card-title">Recent Orders</h3>
                <p class="card-subtitle">Latest customer purchases</p>
            </div>
        </div>
        <div class="card-body-elegant" id="recentOrders">
            <!-- Content will be populated here -->
        </div>
        <div class="card-footer-elegant">
            <a href="#" class="card-link">View all orders →</a>
        </div>
    </div>
    
    <!-- New Registrations Card -->
    <div class="elegant-card">
        <div class="card-header-elegant">
           <!-- <div class="card-icon"></div> -->
            <div class="card-header-content">
                <h3 class="card-title">New User Registrations</h3>
                <p class="card-subtitle">Recently joined users</p>
            </div>
        </div>
        <div class="card-body-elegant" id="newRegistrations">
            <!-- Content will be populated here -->
        </div>
        <div class="card-footer-elegant">
            <a href="#" class="card-link">View all users →</a>
        </div>
    </div>
</div>

    <!-- System Alerts -->
    <!-- <div class="card" style="margin-top: var(--spacing-xl);">
        <div style="padding: var(--spacing-lg); border-bottom: 1px solid var(--border-color);">
            <h3>🚨 System Alerts</h3>
        </div>
        <div id="systemAlerts" style="padding: var(--spacing-lg);">
            <div class="notification warning">
                <strong>Payment Gateway:</strong> Sandbox mode is active. Real payments are disabled.
            </div>
            <div class="notification success">
                <strong>Database:</strong> All systems operational.
            </div>
            <div class="notification info">
                <strong>Backup:</strong> Last backup completed 2 hours ago.
            </div>
        </div>
    </div> -->
</div>

            <!-- User Management -->
            <div id="users-section" class="content-section" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h1>User Management</h1>
                    <div style="display: flex; gap: 15px;">
                        <button class="btn btn-secondary" onclick="exportUsers()">Export Users</button>
                        <button class="btn btn-primary" onclick="openAddUserModal()">➕ Add User</button>
                    </div>
                </div>

                <!-- User Filters -->
                <div class="filters">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="userSearch">Search Users</label>
                            <input type="text" id="userSearch" class="form-control" placeholder="Search by name, email, or ID...">
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
                        <!-- <div class="filter-group">
                            <label for="statusFilter">Status</label>
                            <select id="statusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending Approval</option>
                                <option value="suspended">Suspended</option>
                                <option value="banned">Banned</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="locationUserFilter">Location</label>
                            <select id="locationUserFilter" class="form-control">
                                <option value="">All Locations</option>
                                <option value="Colombo">Colombo</option>
                                <option value="Kandy">Kandy</option>
                                <option value="Galle">Galle</option>
                                <option value="Matale">Matale</option>
                            </select>
                        </div> -->
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th data-sort="id">User ID</th>
                                <th data-sort="name">Name</th>
                                <th data-sort="email">Email</th>
                                <th data-sort="role">Role</th>
                                <!-- <th data-sort="location">Location</th>
                                <th data-sort="status">Status</th> -->
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <div class="message" id="message"></div>
                        <tbody id="usersTableBody">
                            <!-- Users will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Management -->
            <div id="orders-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Order Management</h1>
                </div>

                <!-- Order Statistics -->
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

                <!-- Order Filters -->
                <div class="filters" style="margin-top: var(--spacing-xl);">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="orderSearch">Search Orders</label>
                            <input type="text" id="orderSearch" class="form-control" placeholder="Search by order ID, buyer, or farmer...">
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

                <!-- Orders Table -->
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
                        <tbody id="ordersTableBody">
                            <!-- Orders will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Product Management -->
            <div id="products-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Product Management</h1>
                    <p class="content-subtitle">Overview of all products listed on the platform</p>
                </div>

                <!-- Product Statistics -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number">248</div>
                        <div class="stat-label">Total Products</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">189</div>
                        <div class="stat-label">Active Products</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">42</div>
                        <div class="stat-label">Out of Stock</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">17</div>
                        <div class="stat-label">Pending Approval</div>
                    </div>
                </div>

                <!-- Product Categories -->
                <div class="stats-container" style="margin-top: var(--spacing-xl); width: 100%;">
    <div class="stats-grid">
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Vegetables</h4>
                <div class="stat-number" id="vegetableCount">0</div>
            </div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Fruits</h4>
                <div class="stat-number" id="fruitCount">0</div>
            </div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Grains</h4>
                <div class="stat-number" id="grainCount">0</div>
            </div>
        </div>
    </div>
</div>

                <!-- Products Table -->
                <div class="table-container" style="margin-top: var(--spacing-xl);">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Farmer</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Products will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments & Finance -->
            <div id="payments-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Payments & Finance</h1>
                </div>

                <!-- Financial Overview -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalTransactions">0</div>
                        <div class="stat-label">Total Transactions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="successfulPayments">0</div>
                        <div class="stat-label">Successful Payments</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="pendingPayments">0</div>
                        <div class="stat-label">Pending Payments</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="platformCommission">Rs. 0</div>
                        <div class="stat-label">Platform Commission</div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="stats-container" style="margin-top: var(--spacing-xl); width: 100%;">
    <div class="stats-grid-4">
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Cash on Delivery</h4>
                <div class="stat-number">65%</div>
            </div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Bank Transfer</h4>
                <div class="stat-number">20%</div>
            </div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Mobile Payment</h4>
                <div class="stat-number">10%</div>
            </div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Credit/Debit</h4>
                <div class="stat-number">5%</div>
            </div>
        </div>
    </div>
</div>

                <!-- Recent Transactions -->
                <div class="content-card" style="margin-top: var(--spacing-xl);">
                    <div class="card-header">
                        <h3 class="card-title">Recent Transactions</h3>
                        <button class="btn btn-secondary" onclick="exportTransactions()">Export</button>
                    </div>
                    <div style="padding: var(--spacing-lg);">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Order ID</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsTableBody">
                                    <tr>
                                        <td><strong>#TXN-2025-001</strong></td>
                                        <td>#ORD-2025-001</td>
                                        <td><strong>Rs. 2,450</strong></td>
                                        <td>Cash on Delivery</td>
                                        <td><span class="badge">Completed</span></td>
                                        <td>2025-01-05</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="viewTransaction('TXN-2025-001')">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#TXN-2025-002</strong></td>
                                        <td>#ORD-2025-002</td>
                                        <td><strong>Rs. 8,900</strong></td>
                                        <td>Bank Transfer</td>
                                        <td><span class="badge">Pending</span></td>
                                        <td>2025-01-07</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="viewTransaction('TXN-2025-002')">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#TXN-2025-003</strong></td>
                                        <td>#ORD-2025-003</td>
                                        <td><strong>Rs. 6,200</strong></td>
                                        <td>Mobile Payment</td>
                                        <td><span class="badge">Completed</span></td>
                                        <td>2025-01-06</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="viewTransaction('TXN-2025-003')">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#TXN-2025-004</strong></td>
                                        <td>#ORD-2025-004</td>
                                        <td><strong>Rs. 950</strong></td>
                                        <td>Cash on Delivery</td>
                                        <td><span class="badge">Completed</span></td>
                                        <td>2025-01-04</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="viewTransaction('TXN-2025-004')">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#TXN-2025-005</strong></td>
                                        <td>#ORD-2025-006</td>
                                        <td><strong>Rs. 1,440</strong></td>
                                        <td>Bank Transfer</td>
                                        <td><span class="badge">Failed</span></td>
                                        <td>2025-01-03</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="viewTransaction('TXN-2025-005')">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disputes -->
            <div id="disputes-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Disputes Management</h1>
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
                        <div class="stat-label">Avg Resolution (days)</div>
                    </div>
                </div>

                <!-- Dispute Categories -->
                <div class="stats-container" style="margin-top: var(--spacing-xl); width: 100%;">
    <div class="stats-grid-3">
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Order Issues</h4>
                <div class="stat-number">15</div>
                <div class="stat-description">Wrong items, missing orders</div>
            </div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Payment Issues</h4>
                <div class="stat-number">8</div>
                <div class="stat-description">Payment failures, refunds</div>
            </div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-content">
                <div class="stat-icon"></div>
                <h4>Delivery Issues</h4>
                <div class="stat-number">12</div>
                <div class="stat-description">Late delivery, damaged goods</div>
            </div>
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
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="disputesTableBody">
                            <tr>
                                <td><strong>#DIS-001</strong></td>
                                <td>#ORD-2025-001</td>
                                <td>John Buyer</td>
                                <td>Order Issue</td>
                                <td><span class="badge badge">High</span></td>
                                <td><span class="badge badge">Open</span></td>
                                <td>2025-01-07</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewDispute('DIS-001')">View</button>
                                    <button class="btn btn-sm btn-success" onclick="resolveDispute('DIS-001')">Resolve</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#DIS-002</strong></td>
                                <td>#ORD-2025-003</td>
                                <td>Fresh Market Ltd</td>
                                <td>Payment Issue</td>
                                <td><span class="badge badge">Medium</span></td>
                                <td><span class="badge badge">In Progress</span></td>
                                <td>2025-01-06</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewDispute('DIS-002')">View</button>
                                    <button class="btn btn-sm btn-success" onclick="resolveDispute('DIS-002')">Resolve</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#DIS-003</strong></td>
                                <td>#ORD-2024-989</td>
                                <td>Sarah Williams</td>
                                <td>Delivery Issue</td>
                                <td><span class="badge badge">Low</span></td>
                                <td><span class="badge badge">Resolved</span></td>
                                <td>2025-01-02</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewDispute('DIS-003')">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#DIS-004</strong></td>
                                <td>#ORD-2025-005</td>
                                <td>Michael Brown</td>
                                <td>Order Issue</td>
                                <td><span class="badge badge">High</span></td>
                                <td><span class="badge badge">Open</span></td>
                                <td>2025-01-08</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewDispute('DIS-004')">View</button>
                                    <button class="btn btn-sm btn-success" onclick="resolveDispute('DIS-004')">Resolve</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>#DIS-005</strong></td>
                                <td>#ORD-2024-978</td>
                                <td>Emma Davis</td>
                                <td>Product Quality</td>
                                <td><span class="badge badge">Medium</span></td>
                                <td><span class="badge badge">Resolved</span></td>
                                <td>2024-12-30</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewDispute('DIS-005')">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Analytics -->
            <div id="analytics-section" class="content-section" style="display: none;">
    <div class="analytics-header">
        <h1>Platform Analytics</h1>
    </div>

    <!-- Key Performance Indicators -->
    <div class="dashboard-stats">
        <div class="stat-card card text-left">
            <div class="stat-number" id="monthlyActiveUsers">2,847</div>
            <div class="stat-label">Monthly Active Users</div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-number" id="platformGrowth">24%</div>
            <div class="stat-label">Growth Rate</div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-number" id="userRetention">78%</div>
            <div class="stat-label">User Retention</div>
        </div>
        <div class="stat-card card text-center">
            <div class="stat-number" id="customerSatisfaction">4.6</div>
            <div class="stat-label">Satisfaction Score</div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="analytics-grid">
        <!-- User Growth Chart -->
        <div class="analytics-card">
            <div class="card-header">
                <div class="card-title">
                    <!--<div class="card-icon"></div> -->
                    <div>
                        <h3>User Growth</h3>
                        <p>Registration trends over time</p>
                    </div>
                </div>
                <div class="card-actions">
                    <select class="time-filter">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option selected>Last 90 days</option>
                    </select>
                </div>
            </div>
            <div class="chart-container">
                <div class="line-chart">
                    <svg viewBox="0 0 400 200" class="chart-svg">
                        <!-- Grid lines -->
                        <line x1="50" y1="50" x2="50" y2="150" stroke="#e0e0e0" stroke-width="1"/>
                        <line x1="50" y1="150" x2="350" y2="150" stroke="#e0e0e0" stroke-width="1"/>
                        
                        <!-- Data line -->
                        <path d="M50,120 L100,100 L150,80 L200,110 L250,70 L300,90 L350,60" 
                              fill="none" stroke="var(--primary-color)" stroke-width="3" stroke-linecap="round"/>
                        
                        <!-- Data points -->
                        <circle cx="50" cy="120" r="4" fill="var(--primary-color)"/>
                        <circle cx="100" cy="100" r="4" fill="var(--primary-color)"/>
                        <circle cx="150" cy="80" r="4" fill="var(--primary-color)"/>
                        <circle cx="200" cy="110" r="4" fill="var(--primary-color)"/>
                        <circle cx="250" cy="70" r="4" fill="var(--primary-color)"/>
                        <circle cx="300" cy="90" r="4" fill="var(--primary-color)"/>
                        <circle cx="350" cy="60" r="4" fill="var(--primary-color)"/>
                    </svg>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background: var(--primary-color)"></span>
                        <span>New Registrations</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Trends Chart -->
        <div class="analytics-card">
            <div class="card-header">
                <div class="card-title">
                   <!-- <div class="card-icon"></div> -->
                    <div>
                        <h3>Revenue Trends</h3>
                        <p>Monthly revenue tracking</p>
                    </div>
                </div>
                <div class="card-actions">
                    <select class="time-filter">
                        <option>Q1 2024</option>
                        <option selected>Q2 2024</option>
                        <option>Q3 2024</option>
                    </select>
                </div>
            </div>
            <div class="chart-container">
                <div class="bar-chart">
                    <div class="bars">
                        <div class="bar-container">
                            <div class="bar" style="height: 60%" data-value="Rs. 1.2M"></div>
                            <span class="bar-label">Jan</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar" style="height: 75%" data-value="Rs. 1.5M"></div>
                            <span class="bar-label">Feb</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar" style="height: 85%" data-value="Rs. 1.7M"></div>
                            <span class="bar-label">Mar</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar" style="height: 70%" data-value="Rs. 1.4M"></div>
                            <span class="bar-label">Apr</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar" style="height: 95%" data-value="Rs. 1.9M"></div>
                            <span class="bar-label">May</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar" style="height: 100%" data-value="Rs. 2.4M"></div>
                            <span class="bar-label">Jun</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Distribution -->
        <div class="analytics-card">
            <div class="card-header">
                <div class="card-title">
                   <!-- <div class="card-icon"></div> -->
                    <div>
                        <h3>Geographic Distribution</h3>
                        <p>Users across provinces</p>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <div class="map-distribution">
                    <div class="province-stats">
                        <div class="province-item">
                            <span class="province-name">Western</span>
                            <div class="province-bar">
                                <div class="province-fill" style="width: 85%"></div>
                            </div>
                            <span class="province-value">42%</span>
                        </div>
                        <div class="province-item">
                            <span class="province-name">Central</span>
                            <div class="province-bar">
                                <div class="province-fill" style="width: 65%"></div>
                            </div>
                            <span class="province-value">28%</span>
                        </div>
                        <div class="province-item">
                            <span class="province-name">Southern</span>
                            <div class="province-bar">
                                <div class="province-fill" style="width: 45%"></div>
                            </div>
                            <span class="province-value">18%</span>
                        </div>
                        <div class="province-item">
                            <span class="province-name">Other</span>
                            <div class="province-bar">
                                <div class="province-fill" style="width: 25%"></div>
                            </div>
                            <span class="province-value">12%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Usage -->
        <div class="analytics-card">
            <div class="card-header">
                <div class="card-title">
                   <!-- <div class="card-icon"></div> -->
                    <div>
                        <h3>Platform Usage</h3>
                        <p>Daily active users & sessions</p>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <div class="usage-metrics-grid">
                    <div class="usage-metric">
                        <div class="metric-value">1,247</div>
                        <div class="metric-label">Daily Active Users</div>
                        <div class="metric-trend positive">+8%</div>
                    </div>
                    <div class="usage-metric">
                        <div class="metric-value">4.2m</div>
                        <div class="metric-label">Avg. Session</div>
                        <div class="metric-trend positive">+12s</div>
                    </div>
                    <div class="usage-metric">
                        <div class="metric-value">3.8</div>
                        <div class="metric-label">Pages/Session</div>
                        <div class="metric-trend positive">+0.4</div>
                    </div>
                </div>
                <div class="session-chart">
                    <div class="session-bars">
                        <div class="session-bar" style="height: 40%"></div>
                        <div class="session-bar" style="height: 60%"></div>
                        <div class="session-bar" style="height: 75%"></div>
                        <div class="session-bar" style="height: 85%"></div>
                        <div class="session-bar" style="height: 65%"></div>
                        <div class="session-bar" style="height: 95%"></div>
                        <div class="session-bar" style="height: 100%"></div>
                    </div>
                    <div class="session-labels">
                        <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="performers-section">
        <h2 style="margin-bottom: var(--spacing-lg);"> Top Performers</h2>
        <div class="performers-grid">
            <!-- Top Farmers -->
            <div class="performer-card">
                <div class="performer-header">
                    <!--<div class="performer-icon"></div>-->
                    <h3>Top Farmers</h3>
                    <div class="performer-badge">Revenue</div>
                </div>
                <div class="performer-list">
                    <div class="performer-item featured">
                        <div class="rank">1</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #FFD700, #FFA500);">RF</div>
                        <div class="performer-info">
                            <div class="performer-name">Ranjith Fernando</div>
                            <div class="performer-detail">Colombo • Vegetables</div>
                        </div>
                        <div class="performer-value">Rs. 45,200</div>
                    </div>
                    <div class="performer-item">
                        <div class="rank">2</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #C0C0C0, #A0A0A0);">KS</div>
                        <div class="performer-info">
                            <div class="performer-name">Kumari Silva</div>
                            <div class="performer-detail">Kandy • Fruits</div>
                        </div>
                        <div class="performer-value">Rs. 38,500</div>
                    </div>
                    <div class="performer-item">
                        <div class="rank">3</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #CD7F32, #A56C28);">SP</div>
                        <div class="performer-info">
                            <div class="performer-name">Saman Perera</div>
                            <div class="performer-detail">Gampaha • Grains</div>
                        </div>
                        <div class="performer-value">Rs. 32,100</div>
                    </div>
                </div>
            </div>

            <!-- Top Buyers -->
            <div class="performer-card">
                <div class="performer-header">
                    <!--<div class="performer-icon"></div>-->
                    <h3>Top Buyers</h3>
                    <div class="performer-badge">Spending</div>
                </div>
                <div class="performer-list">
                    <div class="performer-item featured">
                        <div class="rank">1</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #FFD700, #FFA500);">GV</div>
                        <div class="performer-info">
                            <div class="performer-name">Green Valley Restaurant</div>
                            <div class="performer-detail">Colombo 03 • Premium</div>
                        </div>
                        <div class="performer-value">Rs. 28,900</div>
                    </div>
                    <div class="performer-item">
                        <div class="rank">2</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #C0C0C0, #A0A0A0);">FM</div>
                        <div class="performer-info">
                            <div class="performer-name">Fresh Market Ltd</div>
                            <div class="performer-detail">Negombo • Wholesale</div>
                        </div>
                        <div class="performer-value">Rs. 22,150</div>
                    </div>
                    <div class="performer-item">
                        <div class="rank">3</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #CD7F32, #A56C28);">OS</div>
                        <div class="performer-info">
                            <div class="performer-name">Organic Store</div>
                            <div class="performer-detail">Galle • Organic</div>
                        </div>
                        <div class="performer-value">Rs. 18,750</div>
                    </div>
                </div>
            </div>

            <!-- Top Transporters -->
            <div class="performer-card">
                <div class="performer-header">
                    <!--<div class="performer-icon"></div>-->
                    <h3>Top Transporters</h3>
                    <div class="performer-badge">Deliveries</div>
                </div>
                <div class="performer-list">
                    <div class="performer-item featured">
                        <div class="rank">1</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #FFD700, #FFA500);">ED</div>
                        <div class="performer-info">
                            <div class="performer-name">Express Delivery Co</div>
                            <div class="performer-detail">Islandwide • Express</div>
                        </div>
                        <div class="performer-value">127</div>
                    </div>
                    <div class="performer-item">
                        <div class="rank">2</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #C0C0C0, #A0A0A0);">IT</div>
                        <div class="performer-info">
                            <div class="performer-name">Island Transport</div>
                            <div class="performer-detail">Western Province</div>
                        </div>
                        <div class="performer-value">89</div>
                    </div>
                    <div class="performer-item">
                        <div class="rank">3</div>
                        <div class="performer-avatar" style="background: linear-gradient(135deg, #CD7F32, #A56C28);">QT</div>
                        <div class="performer-info">
                            <div class="performer-name">Quick Transports</div>
                            <div class="performer-detail">Central Province</div>
                        </div>
                        <div class="performer-value">76</div>
                    </div>
                </div>
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

                <!-- Notification Stats -->
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

                <!-- Recent Notifications -->
                <div class="content-card" style="margin-top: var(--spacing-xl);">
                    <div class="card-header">
                        <h3 class="card-title">Recent Notifications</h3>
                    </div>
                    <div style="padding: var(--spacing-lg);">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Recipient</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Sent Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="notificationsTableBody">
                                    <tr>
                                        <td>System Maintenance Notice</td>
                                        <td>All Users</td>
                                        <td><span class="badge badge">System</span></td>
                                        <td><span class="badge badge">Delivered</span></td>
                                        <td>2025-01-07</td>
                                        <td>
                                            <button class="btn btn-sm btn-secondary">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div id="settings-section" class="content-section" style="display: none;">
    <div class="settings-header">
        <h1>System Settings</h1>
        <p>Manage platform configuration and system preferences</p>
    </div>

    <div class="settings-grid">
        <!-- Platform Settings -->
        <div class="settings-card">
            <div class="settings-card-header">
                <!-- <div class="settings-icon"></div> -->
                <div>
                    <h3>Platform Settings</h3>
                    <p>Basic platform configuration</p>
                </div>
            </div>
            <div class="settings-card-body">
                <form id="platformSettingsForm" class="settings-form">
                    <div class="form-group">
                        <label for="platformName" class="form-label">Platform Name</label>
                        <input type="text" id="platformName" name="platformName" class="form-control" value="AgroLink" placeholder="Enter platform name">
                    </div>
                    <div class="form-group">
                        <label for="platformCommission" class="form-label">Platform Commission</label>
                        <div class="input-with-suffix">
                            <input type="number" id="platformCommission" name="commission" class="form-control" value="5" step="0.1" min="0" max="50">
                            <span class="input-suffix">%</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="minOrderValue" class="form-label">Minimum Order</label>
                            <div class="input-with-suffix">
                                <input type="number" id="minOrderValue" name="minOrderValue" class="form-control" value="500" min="0">
                                <span class="input-suffix">Rs.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="deliveryFee" class="form-label">Delivery Fee</label>
                            <div class="input-with-suffix">
                                <input type="number" id="deliveryFee" name="deliveryFee" class="form-control" value="150" min="0">
                                <span class="input-suffix">Rs.</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="settings-card">
            <div class="settings-card-header">
              <!--  <div class="settings-icon"></div> -->
                <div>
                    <h3>Payment Methods</h3>
                    <p>Configure payment options</p>
                </div>
            </div>
            <div class="settings-card-body">
                <form id="paymentSettingsForm" class="settings-form">
                    <div class="toggle-group">
                        <div class="toggle-item">
                            <div class="toggle-label">
                                <span class="toggle-icon"></span>
                                <div>
                                    <div class="toggle-title">Cash on Delivery</div>
                                    <div class="toggle-description">Allow customers to pay on delivery</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="enableCOD" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <div class="toggle-label">
                                <span class="toggle-icon"></span>
                                <div>
                                    <div class="toggle-title">Bank Transfer</div>
                                    <div class="toggle-description">Enable bank transfer payments</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="enableBankTransfer" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <div class="toggle-label">
                                <span class="toggle-icon"></span>
                                <div>
                                    <div class="toggle-title">Mobile Payment</div>
                                    <div class="toggle-description">Enable mobile payment gateways</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="enableMobilePayment" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <div class="toggle-label">
                                <span class="toggle-icon"></span>
                                <div>
                                    <div class="toggle-title">Card Payments</div>
                                    <div class="toggle-description">Credit/debit card processing</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="enableCardPayment" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="paymentTimeout" class="form-label">Payment Timeout</label>
                        <div class="input-with-suffix">
                            <input type="number" id="paymentTimeout" name="timeout" class="form-control" value="15" min="1" max="60">
                            <span class="input-suffix">minutes</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Update Payment Settings</button>
                </form>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="settings-card">
            <div class="settings-card-header">
               <!-- <div class="settings-icon"></div> -->
                <div>
                    <h3>Notifications</h3>
                    <p>Communication preferences</p>
                </div>
            </div>
            <div class="settings-card-body">
                <form id="notificationSettingsForm" class="settings-form">
                    <div class="toggle-group">
                        <div class="toggle-item">
                            <div class="toggle-label">
                                <span class="toggle-icon"></span>
                                <div>
                                    <div class="toggle-title">Email Notifications</div>
                                    <div class="toggle-description">Send email alerts and updates</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="enableEmailNotifications" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <div class="toggle-label">
                                <span class="toggle-icon"></span>
                                <div>
                                    <div class="toggle-title">SMS Notifications</div>
                                    <div class="toggle-description">Text message alerts</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="enableSMSNotifications" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-item">
                            <div class="toggle-label">
                                <span class="toggle-icon"></span>
                                <div>
                                    <div class="toggle-title">Push Notifications</div>
                                    <div class="toggle-description">Browser & mobile push alerts</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="enablePushNotifications" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="supportEmail" class="form-label">Support Email</label>
                        <input type="email" id="supportEmail" name="supportEmail" class="form-control" value="support@agrolink.lk" placeholder="support@example.com">
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Save Preferences</button>
                </form>
            </div>
        </div>

        <!-- System Maintenance -->
        <div class="settings-card">
            <div class="settings-card-header">
                <!-- <div class="settings-icon"></div> -->
                <div>
                    <h3>System Maintenance</h3>
                    <p>System operations and tools</p>
                </div>
            </div>
            <div class="settings-card-body">
                <div class="maintenance-actions">
                    <div class="maintenance-item">
                        <div class="maintenance-info">
                            <span class="maintenance-icon"></span>
                            <div>
                                <div class="maintenance-title">System Backup</div>
                                <div class="maintenance-description">Create a full system backup</div>
                            </div>
                        </div>
                        <button class="btn btn-outline" onclick="performMaintenance('backup')">Run Backup</button>
                    </div>
                    <div class="maintenance-item">
                        <div class="maintenance-info">
                            <span class="maintenance-icon">🧹</span>
                            <div>
                                <div class="maintenance-title">Database Cleanup</div>
                                <div class="maintenance-description">Remove temporary data</div>
                            </div>
                        </div>
                        <button class="btn btn-outline" onclick="performMaintenance('cleanup')">Clean Now</button>
                    </div>
                    <div class="maintenance-item">
                        <div class="maintenance-info">
                            <span class="maintenance-icon"></span>
                            <div>
                                <div class="maintenance-title">Clear Cache</div>
                                <div class="maintenance-description">Refresh system cache</div>
                            </div>
                        </div>
                        <button class="btn btn-outline" onclick="performMaintenance('cache')">Clear Cache</button>
                    </div>
                    <div class="maintenance-item">
                        <div class="maintenance-info">
                            <span class="maintenance-icon"></span>
                            <div>
                                <div class="maintenance-title">Maintenance Mode</div>
                                <div class="maintenance-description">Temporarily disable platform</div>
                            </div>
                        </div>
                        <button class="btn btn-warning" onclick="performMaintenance('maintenance')">Enable</button>
                    </div>
                </div>
            </div>
        </div>
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
                            <input type="password" id="userPass" name="password" class="form-control" required minlength="8">
                            <small class="form-text">Minimum 8 characters</small>
                        </div>
                        <div class="form-group">
                            <label for="userConfirmPass">Confirm Password *</label>
                            <input type="password" id="userConfirmPass" name="confirmPassword" class="form-control" required minlength="8">
                            <small class="form-text">Re-enter password to confirm</small>
                        </div>
                    </div>

                    <div id="addUserFormErrors" style="display: none; color: red; margin-bottom: 15px; padding: 10px; background: #ffe6e6; border-radius: 4px;">
                        <!-- Validation errors will appear here -->
                    </div>
                    
                    <div style="display: flex; gap: 15px; margin-top: var(--spacing-lg);">
                        <button type="submit" class="btn btn-primary">Add User</button>
                        <button type="button" class="btn btn-secondary" onclick="closeAddUserModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- UPDATE User Modal -->
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
                            <small class="form-text">Leave empty to keep current password. Minimum 8 characters if changing</small>
                        </div>
                    </div>

                    <div id="updateUserFormErrors" style="display: none; color: red; margin-bottom: 15px; padding: 10px; background: #ffe6e6; border-radius: 4px;">
                        <!-- Validation errors will appear here -->
                    </div>
                    
                    <div style="display: flex; gap: 15px; margin-top: var(--spacing-lg);">
                        <button type="submit" class="btn btn-primary" id="updateSubmitBtn">Update User</button>
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
                        <textarea id="notificationMessage" name="message" class="form-control" rows="4" required></textarea>
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

    <script src="<?=ROOT?>/assets/js/main.js"></script>
    <script>
        // Remove authentication check to allow dashboard access without login
        document.addEventListener('DOMContentLoaded', function() {
            initAdminDashboard();
        });

        // Initialize admin dashboard
        function initAdminDashboard() {
            loadDashboardData();
            loadUsers();
            loadOrders();
            setupNavigation();
            setupForms();
            // Show dashboard section by default on initial load
            showSection('dashboard');
        }

        // Navigation setup
        function setupNavigation() {
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
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

            // Load analytics data when analytics section is shown
            if (sectionName === 'analytics') {
                loadAnalytics();
            }
        }

        // Load dashboard data
        function loadDashboardData() {
            // Mock data - in real app, fetch from APIs
            document.getElementById('recentOrders').innerHTML = `
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-007</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">saadhiq Buyer → Ranjith Farmer - Rs. 2,450</div>
                    <span class="badge badge">Completed</span>
                </div>
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-008</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Green Valley Restaurant → Multiple Farmers - Rs. 8,900</div>
                    <span class="badge badge">Processing</span>
                </div>
            `;
            
            // New registrations
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

        // Load dashboard users table
        // Load users data
        function loadUsers(){
            const tbody = document.getElementById('usersTableBody');
            const users = <?=json_encode($users)?>; //convert php array to js object

            let html = '';
            users.forEach(user => {
                html += `
                    <tr>
                        <td>${user.id || 'N/A'}</td>
                        <td>${user.name || 'N/A'}</td>
                        <td>${user.email || 'N/A'}</td>
                        <td><span class="badge badge-${user.role === 'farmer' ? 'success' : user.role === 'buyer' ? 'info' : user.role === 'transporter' ? 'warning' : 'danger'}">${user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'User'}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="openUpdateUserModal('${user.id}')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser('${user.id}', '${user.role}')">Delete</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        // Load orders data
        function loadOrders() {
            // Order statistics
            document.getElementById('pendingOrdersCount').textContent = '5';
            document.getElementById('processingOrdersCount').textContent = '8';
            document.getElementById('completedOrdersCount').textContent = '234';
            document.getElementById('averageOrderValue').textContent = 'Rs. 1,250';
            
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = `
                <tr>
                    <td><strong>#ORD-2025-001</strong></td>
                    <td>John Buyer</td>
                    <td>Ranjith Fernando</td>
                    <td><strong>Rs. 2,450</strong></td>
                    <td><span class="badge badge">Completed</span></td>
                    <td><span class="badge badge">Paid</span></td>
                    <td>2025-01-05</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('ORD-2025-001')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-002</strong></td>
                    <td>Green Valley Restaurant</td>
                    <td>Multiple Farmers</td>
                    <td><strong>Rs. 8,900</strong></td>
                    <td><span class="badge badge">Processing</span></td>
                    <td><span class="badge badge">Pending</span></td>
                    <td>2025-01-07</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('ORD-2025-002')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-003</strong></td>
                    <td>Fresh Market Ltd</td>
                    <td>Kumari Silva</td>
                    <td><strong>Rs. 6,200</strong></td>
                    <td><span class="badge badge">Shipped</span></td>
                    <td><span class="badge badge">Paid</span></td>
                    <td>2025-01-06</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('ORD-2025-003')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-004</strong></td>
                    <td>Sarah Williams</td>
                    <td>Sunil Perera</td>
                    <td><strong>Rs. 950</strong></td>
                    <td><span class="badge badge">Completed</span></td>
                    <td><span class="badge badge">Paid</span></td>
                    <td>2025-01-04</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('ORD-2025-004')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-005</strong></td>
                    <td>Michael Brown</td>
                    <td>Pradeep Jayasinghe</td>
                    <td><strong>Rs. 3,750</strong></td>
                    <td><span class="badge badge">Pending</span></td>
                    <td><span class="badge badge">Pending</span></td>
                    <td>2025-01-08</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('ORD-2025-005')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-006</strong></td>
                    <td>Emma Davis</td>
                    <td>Nimal Bandara</td>
                    <td><strong>Rs. 1,440</strong></td>
                    <td><span class="badge badge">Cancelled</span></td>
                    <td><span class="badge badge">Refunded</span></td>
                    <td>2025-01-03</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewOrder('ORD-2025-006')">View</button>
                    </td>
                </tr>
            `;
        }


        // Setup forms
        function setupForms() {
            // Send notification form
            const sendNotificationForm = document.getElementById('sendNotificationForm');
            if (sendNotificationForm) {
                sendNotificationForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showNotification?.('Notification sent successfully!', 'success');
                    closeModal?.('sendNotificationModal');
                    this.reset();
                });
            }
            
            // Settings forms
            const platformSettingsForm = document.getElementById('platformSettingsForm');
            if (platformSettingsForm) {
                platformSettingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showNotification?.('Platform settings saved successfully!', 'success');
                });
            }
        }
        
        // Function to reload table
        async function reloadTable() {
            try {
                const response = await fetch('<?=ROOT?>/users/getTable');
                const html = await response.text();
                document.getElementById('users-table-body').innerHTML = html;
                attachDeleteListeners(); // Re-attach event listeners
            } catch (error) {
                console.error('Error reloading table:', error);
            }
        }

        // Function to attach delete button listeners
        function attachDeleteListeners() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-userid');
                    deleteUser(userId);
                });
            });
        }

        // Initial attachment
        document.addEventListener('DOMContentLoaded', function() {
            attachDeleteListeners();
        });

        // Admin-specific functions
        function suspendUser(userId) {
            if (confirm('Are you sure you want to suspend this user?')) {
                showNotification?.('User suspended successfully', 'warning');
                loadUsers();
            }
        }

        async function deleteUser(userId, userRole){
            // Check if trying to delete an admin user
            if (userRole === 'admin') {
                showNotification('Cannot delete admin users. Admin accounts are protected.', 'error');
                return;
            }

            if(!confirm('Are you sure you want to delete this user? This action cannot be undone.')){
                return;
            }

            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/deleteUser', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({user_id: userId})
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

        // Optional: Notification function
        function showNotification(message, type) {
            // Your notification implementation
            alert(message); // Simple alert for demo
        }

        function viewOrder(orderId) {
            showNotification?.('Order details modal will be implemented', 'info');
        }

        function viewProduct(productId) {
            showNotification?.('Product details modal will be implemented', 'info');
        }

        function moderateProduct(productId) {
            showNotification?.('Product moderation modal will be implemented', 'info');
        }

        function viewDispute(disputeId) {
            showNotification?.('Dispute details modal will be implemented', 'info');
        }

        function resolveDispute(disputeId) {
            if (confirm('Mark this dispute as resolved?')) {
                showNotification?.('Dispute resolved successfully', 'success');
            }
        }

        function exportUsers() {
            showNotification?.('Exporting users data...', 'info');
        }

        function exportTransactions() {
            showNotification?.('Exporting transaction data...', 'info');
        }

        function performMaintenance(type) {
            const actions = {
                'backup': 'Creating system backup...',
                'cleanup': 'Cleaning database...',
                'cache': 'Clearing cache...',
                'maintenance': 'Enabling maintenance mode...'
            };
            
            showNotification?.(actions[type], 'info');
            
            setTimeout(() => {
                showNotification?.(`${type} completed successfully!`, 'success');
            }, 2000);
        }

        // Update analytics data
        function loadAnalytics() {
            document.getElementById('monthlyActiveUsers').textContent = '189';
            document.getElementById('platformGrowth').textContent = '12.5%';
            document.getElementById('userRetention').textContent = '87%';
            document.getElementById('customerSatisfaction').textContent = '94%';
        }

        // Utility: Dummy getCurrentUser if not defined
        if (typeof getCurrentUser !== 'function') {
            function getCurrentUser() {
                return null;
            }
        }

        // Utility: Dummy showNotification if not defined
        if (typeof showNotification !== 'function') {
            function showNotification(msg, type) {
                alert(msg);
            }
        }

        // Utility: Dummy closeModal if not defined
        if (typeof closeModal !== 'function') {
            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) modal.style.display = 'none';
            }
        }

        //////////////////MY FUNCTIONS////////////////////
        async function updateUserCount(){
            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/updateUserCount');
                const result = await response.json();

                if (result.success) {
                    document.getElementById('totalUsers').textContent = result.userCount;
                }
            } catch (error) {
                console.error('Error loading user content:', error);
            }
        }

        //update count every 30s
        document.addEventListener('DOMContentLoaded', function(){
            updateUserCount();
            setInterval(updateUserCount, 30000);
        });

        // Function to open Add User Modal
        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.getElementById('addUserForm').reset();
            document.getElementById('addUserMessage').style.display = 'none';
            document.getElementById('addUserFormErrors').style.display = 'none';
        }

        // Function to close Add User Modal
        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('addUserForm').reset();
        }

        // Function to open Update User Modal
        async function openUpdateUserModal(userId) {
            try {
                const response = await fetch(`<?=ROOT?>/adminDashboard/getUser/${userId}`);
                const result = await response.json();

                if(result.success){
                    populateUpdateModal(result.data);
                    document.getElementById('updateUserModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    showMessage(result.message || 'Failed to load user details', 'error');
                }
            } catch(error) {
                console.error('Error loading user details:', error);
                showMessage('Network error occurred', 'error');
            }
        }

        // Function to close Update User Modal
        function closeUpdateUserModal() {
            document.getElementById('updateUserModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('updateUserForm').reset();
        }

        // Function to populate update modal
        function populateUpdateModal(user) {
            document.getElementById('updateUserId').value = user.id;
            document.getElementById('updateName').value = user.name;
            document.getElementById('updateEmail').value = user.email;
            document.getElementById('updateRole').value = user.role;
            document.getElementById('updatePass').value = user.pass; // Clear password field for security
            document.getElementById('updateUserMessage').style.display = 'none';
            document.getElementById('updateUserFormErrors').style.display = 'none';
        }

        // Add User Form submission
        document.getElementById('addUserForm').addEventListener('submit', async function(e){
            e.preventDefault();

            // Clear previous errors
            document.getElementById('addUserFormErrors').style.display = 'none';
            document.getElementById('addUserFormErrors').innerHTML = '';

            // Validate password confirmation
            const password = document.getElementById('userPass').value;
            const confirmPassword = document.getElementById('userConfirmPass').value;

            if (password !== confirmPassword) {
                document.getElementById('addUserFormErrors').innerHTML = '<strong>Error:</strong> Passwords do not match. Please make sure both password fields are identical.';
                document.getElementById('addUserFormErrors').style.display = 'block';
                return;
            }

            const formData = new FormData(this);

            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/register',{
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
                } else {
                    // Show validation errors
                    if (result.errors) {
                        let errorHtml = '<strong>Please fix the following errors:</strong><ul>';
                        for (const error in result.errors) {
                            errorHtml += `<li>${result.errors[error]}</li>`;
                        }
                        errorHtml += '</ul>';
                        document.getElementById('addUserFormErrors').innerHTML = errorHtml;
                        document.getElementById('addUserFormErrors').style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error occurred. Please try again.', 'error', 'addUserMessage');
            }
        });

        // Update User Form submission
        document.getElementById('updateUserForm').addEventListener('submit', async function(e){
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/updateUser',{
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if(result.success){
                    showMessage('User updated successfully!', 'success', 'updateUserMessage');
                    closeUpdateUserModal();
                    loadUsers(); // Refresh the user list
                    window.location.reload();
                } else {
                    showMessage(result.message || 'Failed to update user', 'error', 'updateUserMessage');
                    // Show validation errors
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
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        }
    </script>
    <script src="<?=ROOT?>/assets/js/dashboardNavBar.js"></script>
</body>
</html>