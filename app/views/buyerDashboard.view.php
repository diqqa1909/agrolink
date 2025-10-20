<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - AgroLink</title>
    <meta name="description" content="Manage your orders, track deliveries, and browse fresh produce on AgroLink.">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="logo-section">
            <img src="<?=ROOT?>/assets/imgs/Logo2.png" alt="AgroLink">
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
            <h3 class="sidebar-title">Buyer Dashboard</h3>
            <!--<p class="sidebar-subtitle">Manage your orders and purchases</p>
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
            <li><a href="#cart" class="menu-link" data-section="cart">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                Cart
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

        <!-- Cart Section -->
        <div id="cart-section" class="content-section" style="display: none;">
            <div class="content-header">
                <h1 class="content-title">Shopping Cart</h1>
                <p class="content-subtitle">Review items before checkout.</p>
            </div>
            
            <div class="grid grid-2">
                <!-- Cart items will be dynamically loaded here -->
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
                                <option value="vegetables"> Vegetables</option>
                                <option value="fruits"> Fruits</option>
                                <option value="grains"> Grains</option>
                                <option value="spices"> Spices</option>
                                <option value="herbs"> Herbs</option>
                                <option value="legumes"> Legumes</option>
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

    <script src="<?=ROOT?>/assets/js/main.js"></script>
    <script src="<?=ROOT?>/assets/js/buyerDashboard.js"></script>
</body>
</html>