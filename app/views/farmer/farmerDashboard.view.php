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
    /* $username = $_SESSION['USER']->name ?? 'Farmer';
    $role = $_SESSION['USER']->role ?? 'farmer';
    include '../app/views/components/dashboardNavBar.view.php'; */
    ?>

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
                    <div class="user-role">Farmer</div>
                </div><!-- 
                <button class="logout-btn" onclick="logout()">Logout</button> -->
                <form method="POST" action="<?=ROOT?>/logout" style="display: inline;">
                        <button type="submit" class="logout-btn btn logout-link">Logout</button>
                    </form>
        </div>
    </nav>

    <!-- Dashboard Layout -->
    <div class="dashboard">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="menu-link active" data-section="overview">
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
                <li><a href="#" class="menu-link" data-section="products">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                        </div>
                        Products
                    </a></li>
                <li><a href="#" class="menu-link" data-section="orders">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                            </svg>
                        </div>
                        Orders
                    </a></li>
                <li><a href="#" class="menu-link" data-section="earnings">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="8" />
                                <line x1="12" y1="8" x2="12" y2="16" />
                                <line x1="8" y1="12" x2="16" y2="12" />
                            </svg>
                        </div>
                        Earnings
                    </a></li>
                <li><a href="#" class="menu-link" data-section="deliveries">
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
                <li><a href="#" class="menu-link" data-section="feedback">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2z" />
                                <line x1="9" y1="10" x2="15" y2="10" />
                                <line x1="9" y1="14" x2="13" y2="14" />
                            </svg>
                        </div>
                        Reviews & Complaints
                    </a></li>
                <li><a href="#" class="menu-link" data-section="crop-requests">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                        </div>
                        Crop Requests
                    </a></li>
                <!-- Move Profile link to end -->
                <li><a href="#" class="menu-link" data-section="profile">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        Profile
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
                    <h1 class="content-title">My Products</h1>
                    <button class="btn btn-add-product" data-modal="addProductModal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add New Product
                    </button>
                </div>
                <div class="content-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price/KG</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                    <th>Listed Date</th>
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

                <!-- Earnings Details Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Earnings Details</h3>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Order ID</th>
                                        <th>Item</th>
                                        <th>Qty (kg)</th>
                                        <th>Gross (Rs.)</th>
                                        <th>Platform Fee (5%)</th>
                                        <th>Net (Rs.)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="earningsTableBody">
                                    <!-- Filled by JS: loadEarningsDetailsData() -->
                                </tbody>
                            </table>
                        </div>
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
                </div>

                <!-- Pending Deliveries List -->
                <div class="content-card">
                    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                        <h3 class="card-title" style="margin:0;">Pending Deliveries</h3>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input id="deliveriesSearch" type="text" class="form-control" placeholder="Search buyer or order..." style="min-width: 220px;">
                            <select id="deliveriesSort" class="form-control">
                                <option value="status" selected>Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-content" id="deliveriesList">
                        <!-- Filled by JS: loadPendingDeliveriesData() -->
                    </div>
                </div>
            </div>

            <!-- Crop Requests Management (dummy) -->
            <div id="crop-requests-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Crop Requests from Buyers</h1>
                    <p class="content-subtitle">Review special requests and accept the ones you can fulfill</p>
                </div>

                <div class="content-card">
                    <div class="card-content">
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <div style="padding: 20px; background: #f8f9fa; border-radius: 12px; border-left: 4px solid #4CAF50;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h3 style="margin: 0;">Request #CR001</h3>
                                        <p style="margin: 4px 0 0 0; color: #666;">Buyer: Fresh Mart Supermarket</p>
                                    </div>
                                    <span class="order-status delivered">ACTIVE</span>
                                </div>
                                <div style="margin-top: 16px;">
                                    <p style="margin: 4px 0;"><strong>Crop Needed:</strong> Organic Tomatoes</p>
                                    <p style="margin: 4px 0;"><strong>Quantity:</strong> 200kg</p>
                                    <p style="margin: 4px 0;"><strong>Target Price:</strong> Rs. 130/kg</p>
                                    <p style="margin: 4px 0;"><strong>Delivery By:</strong> Oct 25, 2025</p>
                                    <p style="margin: 4px 0;"><strong>Delivery Location:</strong> Colombo</p>
                                </div>
                                <div style="margin-top: 16px; display: flex; gap: 12px; flex-wrap: wrap;">
                                    <button class="btn btn-primary" onclick="showNotification('Request accepted!', 'success')">Accept Request</button>
                                    <button class="btn btn-danger" onclick="showNotification('Request declined', 'warning')">Decline Request</button>
                                    <button class="btn btn-outline" onclick="showNotification('Viewing request details', 'info')">View Details</button>
                                </div>
                            </div>

                            <div style="padding: 20px; background: #f8f9fa; border-radius: 12px; border-left: 4px solid #3B82F6;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h3 style="margin: 0;">Request #CR002</h3>
                                        <p style="margin: 4px 0 0 0; color: #666;">Buyer: Green Leaf Restaurant</p>
                                    </div>
                                    <span class="order-status pending">ACTIVE</span>
                                </div>
                                <div style="margin-top: 16px;">
                                    <p style="margin: 4px 0;"><strong>Crop Needed:</strong> Fresh Spinach</p>
                                    <p style="margin: 4px 0;"><strong>Quantity:</strong> 50kg</p>
                                    <p style="margin: 4px 0;"><strong>Target Price:</strong> Rs. 80/kg</p>
                                    <p style="margin: 4px 0;"><strong>Delivery By:</strong> Oct 23, 2025</p>
                                    <p style="margin: 4px 0;"><strong>Delivery Location:</strong> Kandy</p>
                                </div>
                                <div style="margin-top: 16px; display: flex; gap: 12px; flex-wrap: wrap;">
                                    <button class="btn btn-primary" onclick="showNotification('Request accepted!', 'success')">Accept Request</button>
                                    <button class="btn btn-danger" onclick="showNotification('Request declined', 'warning')">Decline Request</button>
                                    <button class="btn btn-outline" onclick="showNotification('Viewing request details', 'info')">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews & Complaints -->
            <div id="feedback-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Reviews & Complaints</h1>
                    <p class="content-subtitle">See what buyers are saying and track any issues.</p>
                </div>

                <!-- Recent Reviews -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Reviews</h3>
                    </div>
                    <div class="card-content" id="reviewsList">
                        <!-- Filled by JS: loadDummyFeedbackData() -->
                    </div>
                </div>

                <!-- Complaints -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Complaints</h3>
                    </div>
                    <div class="card-content" id="complaintsList">
                        <!-- Filled by JS: loadDummyFeedbackData() -->
                    </div>
                </div>
            </div>

            <!-- Profile Management -->
            <div id="profile-section" class="content-section profile-section" style="display: none;">
                <!-- Profile Header with Photo -->
                <div class="profile-header-modern">
                    <div class="profile-banner">
                        <div class="banner-pattern"></div>
                    </div>
                    <div class="profile-header-content">
                        <div class="profile-photo-section">
                            <div class="profile-photo-wrapper-modern">
                                <img id="profilePhoto" src="" alt="Profile">
                                <button class="photo-edit-btn" onclick="uploadPhoto()" title="Change Photo">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                        <circle cx="12" cy="13" r="4"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="profile-header-info">
                            <h1 class="profile-name"><?= htmlspecialchars($_SESSION['USER']->name ?? 'Farmer') ?></h1>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="profile-form-modern">
                    <div class="form-section-header">
                        <h2>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Personal Information
                        </h2>
                    </div>
                    <div class="profile-form-grid-modern">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Full Name <span class="required">*</span>
                            </label>
                            <input type="text" id="profileName" class="form-input-modern" value="<?= htmlspecialchars($_SESSION['USER']->name ?? '') ?>" placeholder="Enter your full name">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                Email Address <span class="required">*</span>
                            </label>
                            <input type="email" id="profileEmail" class="form-input-modern" value="<?= htmlspecialchars($_SESSION['USER']->email ?? '') ?>" placeholder="your.email@example.com">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                Phone Number <span class="required">*</span>
                            </label>
                            <input type="tel" id="profilePhone" class="form-input-modern" placeholder="+94 77 123 4567">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Location <span class="required">*</span>
                            </label>
                            <input type="text" id="profileLocation" class="form-input-modern" placeholder="City, Province">
                        </div>
                        <div class="form-group-modern full-width">
                            <label class="form-label-modern">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                </svg>
                                Crops Selling <span class="required">*</span>
                            </label>
                            <input type="text" id="profileCrops" class="form-input-modern" placeholder="e.g., Tomatoes, Rice, Mangoes, Carrots">
                            <span class="form-hint-modern">Separate multiple crops with commas</span>
                        </div>
                        <div class="form-group-modern full-width">
                            <label class="form-label-modern">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Full Address <span class="required">*</span>
                            </label>
                            <textarea id="profileAddress" class="form-input-modern" rows="3" placeholder="Enter your complete farm address"></textarea>
                        </div>
                    </div>

                    <div class="profile-actions-modern">
                        <button class="btn btn-save-profile" onclick="updateProfile()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Save Changes
                        </button>
                        <button class="btn btn-reset-profile" onclick="loadProfileData()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10"></polyline>
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="profile-stats-modern">
                    <div class="stats-header">
                        <h3>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            Account Overview
                        </h3>
                        <p>Your farming journey at a glance</p>
                    </div>
                    <div class="stats-grid-modern">
                        <div class="stat-card-modern stat-primary">
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Member Since</div>
                                <div class="stat-value-modern">Jan 2024</div>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-success">
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Products Listed</div>
                                <div class="stat-value-modern">24</div>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-info">
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Orders Fulfilled</div>
                                <div class="stat-value-modern">142</div>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-warning">
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Average Rating</div>
                                <div class="stat-value-modern">4.8/5</div>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-purple">
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Response Time</div>
                                <div class="stat-value-modern">&lt; 2h</div>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-gradient">
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Total Earnings</div>
                                <div class="stat-value-modern">Rs. 842K</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics section removed as requested -->
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Product</h3>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="productName">Product Name *</label>
                        <input type="text" id="productName" name="name" class="form-control" required placeholder="e.g., Fresh Tomatoes">
                    </div>

                    <div class="form-group">
                        <label for="productCategory">Category *</label>
                        <select id="productCategory" name="category" class="form-control" required>
                            <option value="other">Other</option>
                            <option value="vegetables">Vegetables</option>
                            <option value="fruits">Fruits</option>
                            <option value="cereals">Cereals & Grains</option>
                            <option value="yams">Yams & Tubers</option>
                            <option value="legumes">Legumes & Pulses</option>
                            <option value="spices">Spices & Herbs</option>
                            <option value="leafy">Leafy Greens</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="productPrice">Price per KG (Rs.) *</label>
                        <input type="number" id="productPrice" name="price" class="form-control" step="0.01" min="0" required placeholder="120.00">
                    </div>

                    <div class="form-group">
                        <label for="productQuantity">Available Quantity (KG) *</label>
                        <input type="number" id="productQuantity" name="quantity" class="form-control" min="1" required placeholder="100">
                    </div>

                    <div class="form-group">
                        <label for="productLocation">Farm Location *</label>
                        <input type="text" id="productLocation" name="location" class="form-control" placeholder="e.g., Matale, Central Province" required>
                    </div>

                    <div class="form-group">
                        <label for="listingDate">Available From *</label>
                        <input type="date" id="listingDate" name="listing_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>



                    <div class="form-group">
                        <label for="productImage">Product Image *</label>
                        <input type="file" id="productImage" name="image" class="form-control" accept="image/*" required>
                        <span class="form-hint">Please upload a clear image of your product (Max 5MB, JPG/PNG/GIF/WebP)</span>
                        <div id="imagePreview" style="margin-top: 12px; display: none;">
                            <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #E8F5E9;">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Product</button>
                        <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Product</h3>
                <!-- No X close, use Cancel button -->
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <input type="hidden" id="editProductId">

                    <div class="form-group">
                        <label for="editProductName">Product Name *</label>
                        <input type="text" id="editProductName" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="editProductCategory">Category *</label>
                        <select id="editProductCategory" name="category" class="form-control" required>
                            <option value="vegetables">Vegetables</option>
                            <option value="fruits">Fruits</option>
                            <option value="cereals">Cereals & Grains</option>
                            <option value="yams">Yams & Tubers</option>
                            <option value="legumes">Legumes & Pulses</option>
                            <option value="spices">Spices & Herbs</option>
                            <option value="leafy">Leafy Greens</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editProductPrice">Price per KG (Rs.) *</label>
                        <input type="number" id="editProductPrice" name="price" class="form-control" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="editProductQuantity">Available Quantity (KG) *</label>
                        <input type="number" id="editProductQuantity" name="quantity" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="editProductLocation">Farm Location *</label>
                        <input type="text" id="editProductLocation" name="location" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="editListingDate">Available From *</label>
                        <input type="date" id="editListingDate" name="listing_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="editProductImage">Replace Image (optional)</label>
                        <input type="file" id="editProductImage" name="image" class="form-control" accept="image/*">
                        <span class="form-hint">Leave empty to keep current image. Max 5MB, JPG/PNG/GIF/WebP</span>
                        <div id="editImagePreview" style="margin-top: 12px; display: none;">
                            <img id="editPreviewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #E8F5E9;">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Make ROOT available to JS -->
    <script>
        window.APP_ROOT = "<?= ROOT ?>";
        window.USER_NAME = <?= json_encode($_SESSION['USER']->name ?? '') ?>;
        window.USER_EMAIL = <?= json_encode($_SESSION['USER']->email ?? '') ?>;
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/farmerDashboard.js"></script>
    <script src="<?= ROOT ?>/assets/js/dashboardNavBar.js"></script>
</body>

</html>