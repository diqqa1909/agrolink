<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - AgroLink</title>
    <meta name="description" content="Manage your orders, track deliveries, and browse fresh produce on AgroLink.">
    <link rel="stylesheet" href="assets/css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="logo-section">
            <img src="assets/img/Logo.png" alt="AgroLink">
        </div>
        
        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">DR</div>
                <div class="user-details">
                    <div class="user-name" id="userName">Duleeka Rathnayake</div>
                    <div class="user-role">Buyer</div>
                </div>
            </div>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar">
       <div class="sidebar-header">
           <!-- <h3 class="sidebar-title">Buyer Dashboard</h3>
            <p class="sidebar-subtitle">Manage your orders and purchases</p>
        </div>-->
        
        <ul class="sidebar-menu">
            <li><a href="#overview" class="menu-link active" data-section="overview">
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
            <li><a href="#profile" class="menu-link" data-section="profile">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                Profile
            </a></li>
            <li><a href="#products" class="menu-link" data-section="products">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </div>
                Products
            </a></li>
            <li><a href="#orders" class="menu-link" data-section="orders">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </div>
                Orders
            </a></li>
            <li><a href="#tracking" class="menu-link" data-section="tracking">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                Tracking
            </a></li>
            <li><a href="#wishlist" class="menu-link" data-section="wishlist">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </div>
                Wishlist
            </a></li>
            <li><a href="<?php echo ROOT; ?>/cart" class="menu-link">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                Cart <span class="cart-count" id="cartCount"><?php echo $cartItemCount; ?></span>
            </a></li>
            <li><a href="#requests" class="menu-link" data-section="requests">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14,2 14,8 20,8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10,9 9,9 8,9"></polyline>
                    </svg>
                </div>
                Requests
            </a></li>
            <li><a href="#reviews" class="menu-link" data-section="reviews">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"></polygon>
                    </svg>
                </div>
                Reviews
            </a></li>
            <li><a href="#notifications" class="menu-link" data-section="notifications">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                Notifications
            </a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Overview Section -->
        <div id="overview-section" class="content-section">
            <div class="content-header">
                <h1 class="content-title">Dashboard Overview</h1>
                <p class="content-subtitle">Welcome back! Here's what's happening with your orders.</p>
            </div>

        <!-- Dashboard Stats -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number" id="totalOrders">8</div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pendingOrders">3</div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalSpent">Rs. 2,340</div>
                <div class="stat-label">Total Spent</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="wishlistItems">5</div>
                <div class="stat-label">Wishlist Items</div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
            </div>
            <div class="card-content">
                <div class="order-card" data-status="delivered">
                    <div class="order-header">
                        <h4 class="order-title">Order #1001</h4>
                        <div class="order-status delivered">Delivered</div>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="detail-label">Product</span>
                            <span class="detail-value">Fresh Tomatoes</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Quantity</span>
                            <span class="detail-value">2kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Farmer</span>
                            <span class="detail-value">Ranjith Fernando</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Total</span>
                            <span class="detail-value">Rs. 240</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">Aug 15, 2025</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="btn btn-secondary">View Details</button>
                        <button class="btn btn-primary">Reorder</button>
                        <button class="btn btn-outline">Rate & Review</button>
                    </div>
                </div>

                <div class="order-card" data-status="pending">
                    <div class="order-header">
                        <h4 class="order-title">Order #1002</h4>
                        <div class="order-status pending">Pending</div>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="detail-label">Product</span>
                            <span class="detail-value">Green Beans</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Quantity</span>
                            <span class="detail-value">1kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Farmer</span>
                            <span class="detail-value">Kumari Silva</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Total</span>
                            <span class="detail-value">Rs. 180</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">Aug 18, 2025</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="btn btn-secondary">View Details</button>
                        <button class="btn btn-danger">Cancel Order</button>
                    </div>
                </div>
            </div>
        </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-4">
                        <button class="btn btn-primary" onclick="showSection('products')">Browse Products</button>
                        <button class="btn btn-secondary" onclick="showSection('orders')">View Orders</button>
                        <button class="btn btn-secondary" onclick="showSection('cart')">View Cart</button>
                        <button class="btn btn-secondary" onclick="showSection('profile')">Update Profile</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div id="orders-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">My Orders</h1>
                <p class="content-subtitle">Track and manage your order history.</p>
            </div>
            
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Filter Orders</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-4">
                        <div class="form-group">
                            <label for="orderSearch">Search Orders</label>
                            <input type="text" id="orderSearch" class="form-control" placeholder="Search by product or order ID...">
                        </div>
                        <div class="form-group">
                            <label for="statusFilter">Order Status</label>
                            <select id="statusFilter" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dateFilter">Order Date</label>
                            <input type="date" id="dateFilter" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="farmerFilter">Farmer</label>
                            <input type="text" id="farmerFilter" class="form-control" placeholder="Filter by farmer name...">
                        </div>
                    </div>
                </div>
            </div>

            <div id="ordersContainer">
                <!-- Orders will be dynamically loaded here -->
            </div>
        </div>

        <!-- Wishlist Section -->
        <div id="wishlist-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">My Wishlist</h1>
                <p class="content-subtitle">Your saved favorite products.</p>
            </div>
            
            <div class="grid grid-3">
                <!-- Wishlist items will be dynamically loaded here -->
            </div>
        </div>


        <!-- Profile Section -->
        <div id="profile-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">Profile & Settings</h1>
                <p class="content-subtitle">Manage your personal information and preferences.</p>
            </div>
            
            <!-- Profile Information -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Personal Information</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="profileName">Full Name</label>
                            <input type="text" id="profileName" class="form-control" value="Duleeka Rathnayake">
                        </div>
                        <div class="form-group">
                            <label for="profileEmail">Email Address</label>
                            <input type="email" id="profileEmail" class="form-control" value="duleeka@restaurant.com" readonly>
                        </div>
                        <div class="form-group">
                            <label for="profilePhone">Phone Number</label>
                            <input type="tel" id="profilePhone" class="form-control" value="+94 71 234 5678">
                        </div>
                        <div class="form-group">
                            <label for="profileLocation">Location</label>
                            <input type="text" id="profileLocation" class="form-control" value="Colombo">
                        </div>
                        <div class="form-group">
                            <label for="profileBusiness">Business Name</label>
                            <input type="text" id="profileBusiness" class="form-control" value="Green Leaf Restaurant">
                        </div>
                        <div class="form-group">
                            <label for="profileType">Business Type</label>
                            <select id="profileType" class="form-control">
                                <option value="restaurant" selected>Restaurant</option>
                                <option value="hotel">Hotel</option>
                                <option value="catering">Catering Service</option>
                                <option value="retail">Retail Store</option>
                                <option value="wholesale">Wholesale Buyer</option>
                                <option value="individual">Individual Consumer</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="profileAddress">Delivery Address</label>
                        <textarea id="profileAddress" class="form-control" rows="3">123 Main Street, Colombo 03, Western Province, Sri Lanka</textarea>
                    </div>
                    <div style="margin-top: 20px;">
                        <button class="btn btn-primary" onclick="updateProfile()">Update Profile</button>
                        <button class="btn btn-secondary" onclick="resetProfile()">Reset Changes</button>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Account Settings</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" class="form-control" placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" class="form-control" placeholder="Enter new password">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm new password">
                    </div>
                    <div style="margin-top: 20px;">
                        <button class="btn btn-primary" onclick="changePassword()">Change Password</button>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Preferences & Notifications</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-2">
                        <div>
                            <h4 style="margin-bottom: 15px;">Notification Settings</h4>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="emailNotifications" checked>
                                    <span class="checkmark"></span>
                                    Email Notifications
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="smsNotifications">
                                    <span class="checkmark"></span>
                                    SMS Notifications
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="orderUpdates" checked>
                                    <span class="checkmark"></span>
                                    Order Status Updates
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="priceAlerts" checked>
                                    <span class="checkmark"></span>
                                    Price Drop Alerts
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="newProducts">
                                    <span class="checkmark"></span>
                                    New Product Notifications
                                </label>
                            </div>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 15px;">Delivery Preferences</h4>
                            <div class="form-group">
                                <label for="preferredTime">Preferred Delivery Time</label>
                                <select id="preferredTime" class="form-control">
                                    <option value="morning">Morning (8 AM - 12 PM)</option>
                                    <option value="afternoon" selected>Afternoon (12 PM - 4 PM)</option>
                                    <option value="evening">Evening (4 PM - 8 PM)</option>
                                    <option value="anytime">Anytime</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="deliveryRadius">Maximum Delivery Distance</label>
                                <select id="deliveryRadius" class="form-control">
                                    <option value="10">10 km</option>
                                    <option value="25" selected>25 km</option>
                                    <option value="50">50 km</option>
                                    <option value="100">100 km</option>
                                    <option value="any">Any distance</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="contactlessDelivery" checked>
                                    <span class="checkmark"></span>
                                    Prefer Contactless Delivery
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="specialInstructions">Special Delivery Instructions</label>
                                <textarea id="specialInstructions" class="form-control" rows="3" placeholder="Any special instructions for delivery...">Please call 10 minutes before arrival</textarea>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <button class="btn btn-primary" onclick="savePreferences()">Save Preferences</button>
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Account Statistics</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-4">
                        <div style="text-align: center; padding: 15px;">
                            <div style="font-size: 2rem; font-weight: bold; color: #4CAF50;">8</div>
                            <div style="color: #666;">Total Orders</div>
                        </div>
                        <div style="text-align: center; padding: 15px;">
                            <div style="font-size: 2rem; font-weight: bold; color: #4CAF50;">Rs. 2,340</div>
                            <div style="color: #666;">Total Spent</div>
                        </div>
                        <div style="text-align: center; padding: 15px;">
                            <div style="font-size: 2rem; font-weight: bold; color: #4CAF50;">15</div>
                            <div style="color: #666;">Reviews Given</div>
                        </div>
                        <div style="text-align: center; padding: 15px;">
                            <div style="font-size: 2rem; font-weight: bold; color: #4CAF50;">6</div>
                            <div style="color: #666;">Favorite Farmers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Account Actions</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-2">
                        <div>
                            <h4 style="margin-bottom: 15px;">Data & Privacy</h4>
                            <button class="btn btn-secondary" onclick="downloadData()">📥 Download My Data</button>
                            <button class="btn btn-secondary" onclick="exportOrders()">📊 Export Order History</button>
                            <button class="btn btn-outline" onclick="viewPrivacyPolicy()">🔒 Privacy Policy</button>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 15px;">Account Management</h4>
                            <button class="btn btn-outline" onclick="deactivateAccount()">⏸️ Deactivate Account</button>
                            <button class="btn btn-danger" onclick="deleteAccount()">🗑️ Delete Account</button>
                            <button class="btn btn-outline" onclick="contactSupport()">💬 Contact Support</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div id="products-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">Browse Products</h1>
                <p class="content-subtitle">Discover fresh produce from local farmers.</p>
            </div>
            
            <!-- Product Filters -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Filter Products</h3>
                </div>
                <div class="card-content">
                    <div class="grid grid-4">
                        <div class="form-group">
                            <label for="productSearch">Search Products</label>
                            <input type="text" id="productSearch" class="form-control" placeholder="Search by name or farmer...">
                        </div>
                        <div class="form-group">
                            <label for="categoryFilter">Category</label>
                            <select id="categoryFilter" class="form-control">
                                <option value="">All Categories</option>
                                <option value="vegetables">🥬 Vegetables</option>
                                <option value="fruits">🍎 Fruits</option>
                                <option value="grains">🌾 Grains</option>
                                <option value="spices">🌶️ Spices</option>
                                <option value="herbs">🌿 Herbs</option>
                                <option value="legumes">🫘 Legumes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="locationFilter">Location</label>
                            <select id="locationFilter" class="form-control">
                                <option value="">All Locations</option>
                                <option value="Colombo">Colombo</option>
                                <option value="Kandy">Kandy</option>
                                <option value="Galle">Galle</option>
                                <option value="Matale">Matale</option>
                                <option value="Anuradhapura">Anuradhapura</option>
                                <option value="Nuwara Eliya">Nuwara Eliya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="priceFilter">Price Range</label>
                            <select id="priceFilter" class="form-control">
                                <option value="">All Prices</option>
                                <option value="0-100">Under Rs. 100</option>
                                <option value="100-200">Rs. 100 - 200</option>
                                <option value="200-500">Rs. 200 - 500</option>
                                <option value="500+">Above Rs. 500</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid grid-3">
                <!-- Products will be dynamically loaded here -->
            </div>
        </div>

        <!-- Tracking Section -->
        <div id="tracking-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">Order Tracking</h1>
                <p class="content-subtitle">Monitor the status of your deliveries.</p>
            </div>
            <div class="content-card">
                <div class="card-content">
                    <!-- Tracking content will be dynamically loaded here -->
                </div>
            </div>
        </div>

        <!-- Requests Section -->
        <div id="requests-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">Crop Requests</h1>
                <p class="content-subtitle">Request specific crops from farmers.</p>
            </div>
            <div class="content-card">
                <div class="card-content">
                    <p>Crop requests section will be added here.</p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div id="reviews-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">Reviews</h1>
                <p class="content-subtitle">Rate and review your purchases.</p>
            </div>
            <div class="content-card">
                <div class="card-content">
                    <!-- Reviews content will be dynamically loaded here -->
                </div>
            </div>
        </div>

        <!-- Notifications Section -->
        <div id="notifications-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">Notifications</h1>
                <p class="content-subtitle">Stay updated with your account activity.</p>
            </div>
            <div class="content-card">
                <div class="card-content">
                    <!-- Notifications content will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </main>

    <script>
        // Dashboard functionality
        function showSection(sectionName) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.style.display = 'none';
            });

            // Show selected section
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }

            // Update active menu item
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === sectionName) {
                    link.classList.add('active');
                }
            });

            // Update content title and subtitle
            const contentTitle = document.querySelector('.content-title');
            const contentSubtitle = document.querySelector('.content-subtitle');
            
            const sectionTitles = {
                'overview': { title: 'Dashboard Overview', subtitle: 'Welcome back! Here\'s what\'s happening with your orders.' },
                'profile': { title: 'Profile & Settings', subtitle: 'Manage your personal information and preferences.' },
                'products': { title: 'Browse Products', subtitle: 'Discover fresh produce from local farmers.' },
                'orders': { title: 'My Orders', subtitle: 'Track and manage your order history.' },
                'tracking': { title: 'Order Tracking', subtitle: 'Monitor the status of your deliveries.' },
                'wishlist': { title: 'My Wishlist', subtitle: 'Your saved favorite products.' },
                'requests': { title: 'Crop Requests', subtitle: 'Request specific crops from farmers.' },
                'reviews': { title: 'Reviews', subtitle: 'Rate and review your purchases.' },
                'notifications': { title: 'Notifications', subtitle: 'Stay updated with your account activity.' }
            };

            if (sectionTitles[sectionName]) {
                contentTitle.textContent = sectionTitles[sectionName].title;
                contentSubtitle.textContent = sectionTitles[sectionName].subtitle;
            }

            // Load section-specific data
            switch(sectionName) {
                case 'orders':
                    loadOrders();
                    break;
                case 'wishlist':
                    loadWishlist();
                    break;
                case 'products':
                    loadProducts();
                    break;
                case 'tracking':
                    loadTracking();
                    break;
                case 'reviews':
                    loadReviews();
                    break;
                case 'notifications':
                    loadNotifications();
                    break;
            }
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                // Clear localStorage
                localStorage.removeItem('user_id');
                localStorage.removeItem('user_email');
                localStorage.removeItem('user_role');
                localStorage.removeItem('user_name');
                localStorage.removeItem('business_name');
                localStorage.removeItem('buyerCart');
                localStorage.removeItem('buyerWishlist');
                
                // Redirect to logout handler
                window.location.href = 'auth/logout.php';
            }
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            // Style the notification
            Object.assign(notification.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '1rem 2rem',
                borderRadius: '8px',
                color: 'white',
                backgroundColor: type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : type === 'error' ? '#ef4444' : '#3b82f6',
                zIndex: '1000',
                fontSize: '14px',
                fontWeight: 'bold',
                boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)'
            });
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Load orders data
        function loadOrders() {
            const ordersContainer = document.getElementById('ordersContainer');
            if (!ordersContainer) return;

            ordersContainer.innerHTML = `
                <div class="order-card" data-status="delivered">
                    <div class="order-header">
                        <h4 class="order-title">Order #1001</h4>
                        <div class="order-status delivered">Delivered</div>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="detail-label">Product</span>
                            <span class="detail-value">Fresh Tomatoes</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Quantity</span>
                            <span class="detail-value">2kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Farmer</span>
                            <span class="detail-value">Ranjith Fernando</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Total</span>
                            <span class="detail-value">Rs. 240</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">Aug 15, 2025</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="viewOrderDetails('1001')">View Details</button>
                        <button class="btn btn-primary" onclick="reorder('1001')">Reorder</button>
                        <button class="btn btn-outline" onclick="rateOrder('1001')">Rate & Review</button>
                    </div>
                </div>

                <div class="order-card" data-status="pending">
                    <div class="order-header">
                        <h4 class="order-title">Order #1002</h4>
                        <div class="order-status pending">Pending</div>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="detail-label">Product</span>
                            <span class="detail-value">Green Beans</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Quantity</span>
                            <span class="detail-value">1kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Farmer</span>
                            <span class="detail-value">Kumari Silva</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Total</span>
                            <span class="detail-value">Rs. 180</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">Aug 18, 2025</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="viewOrderDetails('1002')">View Details</button>
                        <button class="btn btn-danger" onclick="cancelOrder('1002')">Cancel Order</button>
                    </div>
                </div>

                <div class="order-card" data-status="shipped">
                    <div class="order-header">
                        <h4 class="order-title">Order #1003</h4>
                        <div class="order-status shipped">Shipped</div>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="detail-label">Product</span>
                            <span class="detail-value">Sweet Mangoes</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Quantity</span>
                            <span class="detail-value">3kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Farmer</span>
                            <span class="detail-value">Pradeep Jayasinghe</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Total</span>
                            <span class="detail-value">Rs. 450</span>
                        </div>
                        <div class="order-detail">
                            <span class="detail-label">Date</span>
                            <span class="detail-value">Aug 19, 2025</span>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="viewOrderDetails('1003')">View Order Details</button>
                        <button class="btn btn-primary" onclick="trackOrder('1003')">Track Order</button>
                    </div>
                </div>
            `;
        }

        // Load wishlist data
        function loadWishlist() {
            const wishlistContainer = document.querySelector('#wishlist-section .grid');
            if (!wishlistContainer) return;

            wishlistContainer.innerHTML = `
                <div class="content-card">
                    <div style="text-align: center; padding: 25px;">
                        <div style="font-size: 3rem; margin-bottom: 15px;">🥭</div>
                        <h4>Sweet Mangoes</h4>
                        <p class="text-muted">Pradeep Jayasinghe (Galle)</p>
                        <div style="font-size: 1.25rem; font-weight: bold; color: #4CAF50; margin: 15px 0;">Rs. 150/kg</div>
                        <button class="btn btn-primary w-full" onclick="addToCart('mangoes', 1)">Add to Cart</button>
                        <button class="btn btn-outline w-full mt-sm" onclick="removeFromWishlist('mangoes')">Remove from Wishlist</button>
                    </div>
                </div>

                <div class="content-card">
                    <div style="text-align: center; padding: 25px;">
                        <div style="font-size: 3rem; margin-bottom: 15px;">🌾</div>
                        <h4>Red Rice</h4>
                        <p class="text-muted">Sunil Perera (Anuradhapura)</p>
                        <div style="font-size: 1.25rem; font-weight: bold; color: #4CAF50; margin: 15px 0;">Rs. 95/kg</div>
                        <button class="btn btn-primary w-full" onclick="addToCart('red-rice', 1)">Add to Cart</button>
                        <button class="btn btn-outline w-full mt-sm" onclick="removeFromWishlist('red-rice')">Remove from Wishlist</button>
                    </div>
                </div>

                <div class="content-card">
                    <div style="text-align: center; padding: 25px;">
                        <div style="font-size: 3rem; margin-bottom: 15px;">🥕</div>
                        <h4>Organic Carrots</h4>
                        <p class="text-muted">Lakshmi Perera (Nuwara Eliya)</p>
                        <div style="font-size: 1.25rem; font-weight: bold; color: #4CAF50; margin: 15px 0;">Rs. 120/kg</div>
                        <button class="btn btn-primary w-full" onclick="addToCart('carrots', 1)">Add to Cart</button>
                        <button class="btn btn-outline w-full mt-sm" onclick="removeFromWishlist('carrots')">Remove from Wishlist</button>
                    </div>
                </div>
            `;
        }


        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Update user info in header
            document.getElementById('userName').textContent = '<?php echo htmlspecialchars($username); ?>';
            document.getElementById('userAvatar').textContent = '<?php echo strtoupper(substr($username, 0, 2)); ?>';
            
            // Update profile section
            const profileName = document.querySelector('.profile-info h4');
            const profileAvatar = document.querySelector('.profile-avatar');
            if (profileName) profileName.textContent = '<?php echo htmlspecialchars($username); ?>';
            if (profileAvatar) profileAvatar.textContent = '<?php echo strtoupper(substr($username, 0, 2)); ?>';
            
            // Setup navigation
            setupNavigation();
            
            // Show overview section by default
            showSection('overview');
            
            // Load cart count from server
            loadCartCount();
        });

        // Navigation setup
        function setupNavigation() {
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    const section = this.getAttribute('data-section');
                    
                    // If it's a cart link, redirect to cart page
                    if (href && href.includes('/cart')) {
                        window.location.href = href;
                        return;
                    }
                    
                    // Otherwise, show section if data-section exists
                    if (section) {
                        showSection(section);
                    }
                });
            });
        }

        // Cart functions
        function addToCart(productId, quantity) {
            // Product data mapping
            const productData = {
                '1': { name: 'Fresh Tomatoes', price: 120, farmer: 'Ranjith Fernando', location: 'Matale', icon: '🍅' },
                '2': { name: 'Green Beans', price: 180, farmer: 'Kumari Silva', location: 'Kandy', icon: '🫛' },
                '3': { name: 'Red Rice', price: 95, farmer: 'Sunil Perera', location: 'Anuradhapura', icon: '🌾' },
                '4': { name: 'Sweet Mangoes', price: 150, farmer: 'Pradeep Jayasinghe', location: 'Galle', icon: '🥭' },
                '5': { name: 'Fresh Carrots', price: 200, farmer: 'Manjula Rathnayake', location: 'Nuwara Eliya', icon: '🥕' },
                '6': { name: 'Red Chili Powder', price: 800, farmer: 'Gamini Wickramasinghe', location: 'Matale', icon: '🌶️' },
                '7': { name: 'Ripe Bananas', price: 80, farmer: 'Asanka Perera', location: 'Ratnapura', icon: '🍌' },
                '8': { name: 'Fresh Cabbage', price: 160, farmer: 'Sandamali Fernando', location: 'Kurunegala', icon: '🥬' },
                '9': { name: 'Fresh Curry Leaves', price: 250, farmer: 'Dharmasena Silva', location: 'Kandy', icon: '🌿' },
                'mangoes': { name: 'Sweet Mangoes', price: 150, farmer: 'Pradeep Jayasinghe', location: 'Galle', icon: '🥭' },
                'red-rice': { name: 'Red Rice', price: 95, farmer: 'Sunil Perera', location: 'Anuradhapura', icon: '🌾' },
                'carrots': { name: 'Organic Carrots', price: 120, farmer: 'Lakshmi Perera', location: 'Nuwara Eliya', icon: '🥕' }
            };

            const product = productData[productId];
            if (!product) {
                showNotification('Product not found', 'error');
                return;
            }

            // Send AJAX request to add item to cart
            fetch('<?php echo ROOT; ?>/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&product_name=${encodeURIComponent(product.name)}&product_price=${product.price}&quantity=${quantity}&farmer_name=${encodeURIComponent(product.farmer)}&farmer_location=${encodeURIComponent(product.location)}&product_image=${encodeURIComponent(product.icon)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Update cart count
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        cartCount.textContent = data.cartItemCount;
                    }
                } else {
                    showNotification(data.message, 'error');
                    console.error('Cart add error:', data);
                }
            })
            .catch(error => {
                showNotification('An error occurred while adding to cart', 'error');
                console.error('Error:', error);
            });
        }

        // Load cart count from server
        function loadCartCount() {
            fetch('<?php echo ROOT; ?>/cart/getCartData')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        cartCount.textContent = data.cartItemCount;
                    }
                }
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
        }

        // Wishlist functions
        function addToWishlist(productId) {
            const wishlist = JSON.parse(localStorage.getItem('buyerWishlist')) || [];
            if (!wishlist.includes(productId)) {
                wishlist.push(productId);
                localStorage.setItem('buyerWishlist', JSON.stringify(wishlist));
                showNotification('Added to wishlist!', 'success');
            } else {
                showNotification('Already in wishlist!', 'info');
            }
        }

        function removeFromWishlist(productId) {
            const wishlist = JSON.parse(localStorage.getItem('buyerWishlist')) || [];
            const updatedWishlist = wishlist.filter(id => id !== productId);
            localStorage.setItem('buyerWishlist', JSON.stringify(updatedWishlist));
            loadWishlist();
            showNotification('Removed from wishlist', 'success');
        }

        // Order functions
        function viewOrderDetails(orderId) {
            showNotification(`Viewing order details for #${orderId}`, 'info');
        }

        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                showNotification(`Order #${orderId} cancelled successfully`, 'success');
                loadOrders();
            }
        }

        function reorder(orderId) {
            showNotification(`Reordering items from order #${orderId}`, 'info');
        }

        function rateOrder(orderId) {
            showNotification(`Rating order #${orderId}`, 'info');
        }

        function trackOrder(orderId) {
            showSection('tracking');
        }

        // Checkout function
        function proceedToCheckout() {
            const cart = JSON.parse(localStorage.getItem('buyerCart')) || [];
            if (cart.length === 0) {
                showNotification('Your cart is empty!', 'warning');
                return;
            }
            window.location.href = 'checkout.html';
        }

        // Profile functions
        function updateProfile() {
            const name = document.getElementById('profileName').value;
            const phone = document.getElementById('profilePhone').value;
            const location = document.getElementById('profileLocation').value;
            const business = document.getElementById('profileBusiness').value;
            const businessType = document.getElementById('profileType').value;
            const address = document.getElementById('profileAddress').value;

            if (!name || !phone || !location || !business || !address) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }

            // Save to localStorage (in real app, this would be an API call)
            const profileData = {
                name: name,
                phone: phone,
                location: location,
                business: business,
                businessType: businessType,
                address: address
            };
            localStorage.setItem('buyerProfile', JSON.stringify(profileData));
            
            // Update the header name
            document.getElementById('userName').textContent = name;
            
            showNotification('Profile updated successfully!', 'success');
        }

        function resetProfile() {
            // Reset to original values
            document.getElementById('profileName').value = 'Duleeka Rathnayake';
            document.getElementById('profilePhone').value = '+94 71 234 5678';
            document.getElementById('profileLocation').value = 'Colombo';
            document.getElementById('profileBusiness').value = 'Green Leaf Restaurant';
            document.getElementById('profileType').value = 'restaurant';
            document.getElementById('profileAddress').value = '123 Main Street, Colombo 03, Western Province, Sri Lanka';
            
            showNotification('Profile reset to original values', 'info');
        }

        function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                showNotification('Please fill in all password fields', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                showNotification('New passwords do not match', 'error');
                return;
            }

            if (newPassword.length < 6) {
                showNotification('Password must be at least 6 characters long', 'error');
                return;
            }

            showNotification('Password changed successfully!', 'success');
            
            // Clear password fields
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        }

        function savePreferences() {
            const preferences = {
                emailNotifications: document.getElementById('emailNotifications').checked,
                smsNotifications: document.getElementById('smsNotifications').checked,
                orderUpdates: document.getElementById('orderUpdates').checked,
                priceAlerts: document.getElementById('priceAlerts').checked,
                newProducts: document.getElementById('newProducts').checked,
                preferredTime: document.getElementById('preferredTime').value,
                deliveryRadius: document.getElementById('deliveryRadius').value,
                contactlessDelivery: document.getElementById('contactlessDelivery').checked,
                specialInstructions: document.getElementById('specialInstructions').value
            };

            localStorage.setItem('buyerPreferences', JSON.stringify(preferences));
            showNotification('Preferences saved successfully!', 'success');
        }

        // Account management functions
        function downloadData() {
            const profileData = JSON.parse(localStorage.getItem('buyerProfile')) || {};
            const orderData = JSON.parse(localStorage.getItem('buyerOrders')) || [];
            const cartData = JSON.parse(localStorage.getItem('buyerCart')) || [];
            const wishlistData = JSON.parse(localStorage.getItem('buyerWishlist')) || [];

            const dataToDownload = {
                profile: profileData,
                orders: orderData,
                cart: cartData,
                wishlist: wishlistData,
                exportDate: new Date().toISOString()
            };

            const dataStr = JSON.stringify(dataToDownload, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            
            const link = document.createElement('a');
            link.href = url;
            link.download = 'agrolink-buyer-data.json';
            link.click();
            
            URL.revokeObjectURL(url);
            showNotification('Data downloaded successfully!', 'success');
        }

        function exportOrders() {
            const orders = [
                { id: '1001', product: 'Fresh Tomatoes', quantity: '2kg', total: 'Rs. 240', date: 'Aug 15, 2025', status: 'Delivered' },
                { id: '1002', product: 'Green Beans', quantity: '1kg', total: 'Rs. 180', date: 'Aug 18, 2025', status: 'Pending' },
                { id: '1003', product: 'Sweet Mangoes', quantity: '3kg', total: 'Rs. 450', date: 'Aug 19, 2025', status: 'Shipped' }
            ];

            let csvContent = 'Order ID,Product,Quantity,Total,Date,Status\n';
            orders.forEach(order => {
                csvContent += `${order.id},${order.product},${order.quantity},${order.total},${order.date},${order.status}\n`;
            });

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            
            const link = document.createElement('a');
            link.href = url;
            link.download = 'agrolink-orders.csv';
            link.click();
            
            URL.revokeObjectURL(url);
            showNotification('Order history exported successfully!', 'success');
        }

        function viewPrivacyPolicy() {
            showNotification('Opening privacy policy...', 'info');
        }

        function deactivateAccount() {
            if (confirm('Are you sure you want to deactivate your account? You can reactivate it later by contacting support.')) {
                showNotification('Account deactivated. Contact support to reactivate.', 'warning');
            }
        }

        function deleteAccount() {
            if (confirm('Are you sure you want to permanently delete your account? This action cannot be undone.')) {
                if (confirm('This will permanently delete all your data including orders, reviews, and preferences. Are you absolutely sure?')) {
                    showNotification('Account deletion request submitted. You will receive a confirmation email.', 'warning');
                }
            }
        }

        function contactSupport() {
            showNotification('Opening support chat...', 'info');
        }

        // Load products data
        function loadProducts() {
            const productsGrid = document.getElementById('productsGrid');
            if (!productsGrid) return;

            const products = [
                {
                    id: '1',
                    name: 'Fresh Tomatoes',
                    price: 120,
                    image: '🍅',
                    farmer: 'Ranjith Fernando',
                    location: 'Matale',
                    category: 'vegetables',
                    description: 'Fresh organic tomatoes grown in Matale. Perfect for cooking and salads.',
                    organic: true,
                    available: true
                },
                {
                    id: '2',
                    name: 'Green Beans',
                    price: 180,
                    image: '🫛',
                    farmer: 'Kumari Silva',
                    location: 'Kandy',
                    category: 'vegetables',
                    description: 'Premium quality green beans, freshly harvested and ready for delivery.',
                    available: true
                },
                {
                    id: '3',
                    name: 'Red Rice',
                    price: 95,
                    image: '🌾',
                    farmer: 'Sunil Perera',
                    location: 'Anuradhapura',
                    category: 'grains',
                    description: 'Traditional red rice variety, rich in nutrients and perfect for healthy meals.',
                    organic: true,
                    available: true
                },
                {
                    id: '4',
                    name: 'Sweet Mangoes',
                    price: 150,
                    image: '🥭',
                    farmer: 'Pradeep Jayasinghe',
                    location: 'Galle',
                    category: 'fruits',
                    description: 'Juicy and sweet mangoes from Galle region, perfect ripeness guaranteed.',
                    available: true
                },
                {
                    id: '5',
                    name: 'Fresh Carrots',
                    price: 200,
                    image: '🥕',
                    farmer: 'Manjula Rathnayake',
                    location: 'Nuwara Eliya',
                    category: 'vegetables',
                    description: 'Fresh carrots from the cool climate of Nuwara Eliya, sweet and crunchy.',
                    organic: true,
                    available: true
                }
            ];

            let productsHTML = '';
            products.forEach(product => {
                const badges = [];
                if (product.organic) badges.push('<span class="badge badge-success">Organic</span>');
                if (product.available) badges.push('<span class="badge badge-info">Available</span>');

                productsHTML += `
                    <div class="content-card" data-category="${product.category}" data-location="${product.location}" data-price="${product.price}">
                        <div style="text-align: center; padding: 25px;">
                            <div style="font-size: 3rem; margin-bottom: 15px;">${product.image}</div>
                            <h4>${product.name}</h4>
                            <p class="text-muted">${product.farmer} (${product.location})</p>
                            <div style="font-size: 1.25rem; font-weight: bold; color: #4CAF50; margin: 15px 0;">Rs. ${product.price}/kg</div>
                            <p style="font-size: 0.9rem; color: #666; margin: 10px 0;">
                                ${product.description}
                            </p>
                            <div style="display: flex; gap: 5px; margin: 10px 0; justify-content: center;">
                                ${badges.join('')}
                            </div>
                            <div style="display: flex; gap: 10px; margin-bottom: 10px; justify-content: center;">
                                <button class="btn btn-sm" onclick="updateProductQuantity('${product.id}', -1)">-</button>
                                <span style="padding: 0 10px;" id="${product.id}-qty">1</span>
                                <button class="btn btn-sm" onclick="updateProductQuantity('${product.id}', 1)">+</button>
                            </div>
                            <button class="btn btn-primary w-full" onclick="addToCart('${product.id}', parseInt(document.getElementById('${product.id}-qty').textContent))">Add to Cart</button>
                            <button class="btn btn-outline w-full mt-sm" onclick="addToWishlist('${product.id}')">Add to Wishlist</button>
                        </div>
                    </div>
                `;
            });

            productsGrid.innerHTML = productsHTML;
        }

        function updateProductQuantity(productId, change) {
            const qtyElement = document.getElementById(productId + '-qty');
            if (qtyElement) {
                let currentQty = parseInt(qtyElement.textContent);
                currentQty = Math.max(1, currentQty + change);
                qtyElement.textContent = currentQty;
            }
        }

        // Load tracking data
        function loadTracking() {
            const trackingContainer = document.querySelector('#tracking-section .card-content');
            if (!trackingContainer) return;

            trackingContainer.innerHTML = `
                <h3 style="margin-bottom: 20px;">Order Tracking</h3>
                
                <div class="order-card" data-status="shipped">
                    <div class="order-header">
                        <h4 class="order-title">Order #1003 - Sweet Mangoes</h4>
                        <div class="order-status shipped">In Transit</div>
                    </div>
                    <div style="margin: 20px 0;">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 20px; height: 20px; background: #4CAF50; border-radius: 50%; margin-right: 10px;"></div>
                            <div>
                                <div style="font-weight: bold;">Order Confirmed</div>
                                <div style="font-size: 0.9rem; color: #666;">Aug 19, 2025 - 10:30 AM</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 20px; height: 20px; background: #4CAF50; border-radius: 50%; margin-right: 10px;"></div>
                            <div>
                                <div style="font-weight: bold;">Processing</div>
                                <div style="font-size: 0.9rem; color: #666;">Aug 19, 2025 - 2:15 PM</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 20px; height: 20px; background: #4CAF50; border-radius: 50%; margin-right: 10px;"></div>
                            <div>
                                <div style="font-weight: bold;">Shipped</div>
                                <div style="font-size: 0.9rem; color: #666;">Aug 20, 2025 - 9:00 AM</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                            <div style="width: 20px; height: 20px; background: #ddd; border-radius: 50%; margin-right: 10px;"></div>
                            <div>
                                <div style="font-weight: bold;">Out for Delivery</div>
                                <div style="font-size: 0.9rem; color: #666;">Expected: Aug 21, 2025</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <div style="width: 20px; height: 20px; background: #ddd; border-radius: 50%; margin-right: 10px;"></div>
                            <div>
                                <div style="font-weight: bold;">Delivered</div>
                                <div style="font-size: 0.9rem; color: #666;">Pending</div>
                            </div>
                        </div>
                    </div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="viewOrderDetails('1003')">View Order Details</button>
                        <button class="btn btn-primary" onclick="contactTransporter('1003')">Contact Transporter</button>
                    </div>
                </div>
            `;
        }

        function contactTransporter(orderId) {
            showNotification(`Contacting transporter for order #${orderId}`, 'info');
        }

        // Load reviews data
        function loadReviews() {
            const reviewsContainer = document.querySelector('#reviews-section .card-content');
            if (!reviewsContainer) return;

            reviewsContainer.innerHTML = `
                <h3 style="margin-bottom: 20px;">My Reviews</h3>
                
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="font-size: 2rem; margin-right: 10px;">🍅</div>
                        <div>
                            <div style="font-weight: bold;">Fresh Tomatoes - Ranjith Fernando</div>
                            <div style="color: #666;">Order #1001</div>
                            <div style="margin-top: 5px;">
                                <span style="color: #fbbf24;">★★★★★</span>
                                <span style="margin-left: 10px;">Excellent quality and fast delivery!</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="font-size: 2rem; margin-right: 10px;">🥬</div>
                        <div>
                            <div style="font-weight: bold;">Green Beans - Kumari Silva</div>
                            <div style="color: #666;">Order #1002</div>
                            <div style="margin-top: 5px;">
                                <span style="color: #fbbf24;">★★★★☆</span>
                                <span style="margin-left: 10px;">Good quality, slightly delayed delivery</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary" onclick="writeNewReview()">Write New Review</button>
            `;
        }

        function writeNewReview() {
            showNotification('Opening review form...', 'info');
        }

        // Load notifications data
        function loadNotifications() {
            const notificationsContainer = document.querySelector('#notifications-section .card-content');
            if (!notificationsContainer) return;

            notificationsContainer.innerHTML = `
                <h3 style="margin-bottom: 20px;">Notifications</h3>
                
                <div style="margin-bottom: 15px; padding: 15px; background: #f0f9ff; border-radius: 8px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Order Shipped</div>
                    <div style="font-size: 0.9rem; color: #666;">Your order #1003 has been shipped and is on its way!</div>
                    <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">2 hours ago</div>
                </div>

                <div style="margin-bottom: 15px; padding: 15px; background: #f0f9ff; border-radius: 8px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">New Product Available</div>
                    <div style="font-size: 0.9rem; color: #666;">Fresh organic carrots are now available from Lakshmi Perera</div>
                    <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">1 day ago</div>
                </div>

                <div style="margin-bottom: 15px; padding: 15px; background: #f0f9ff; border-radius: 8px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Price Drop Alert</div>
                    <div style="font-size: 0.9rem; color: #666;">Sweet mangoes price has dropped to Rs. 140/kg</div>
                    <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">2 days ago</div>
                </div>

                <button class="btn btn-secondary" onclick="markAllAsRead()">Mark All as Read</button>
            `;
        }

        function markAllAsRead() {
            showNotification('All notifications marked as read', 'success');
            loadNotifications();
        }
    </script>
    
    <style>
        .cart-count {
            background: #4CAF50;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
    </style>
</body>
</html>