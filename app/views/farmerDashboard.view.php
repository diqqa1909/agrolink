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
                <li><a href="#" class="menu-link" data-section="reviews">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        Reviews
                    </a></li>
            </ul>
            <ul class="sidebar-menu" style="margin-top: auto; border-top: 1px solid #e0e0e0; padding-top: 10px;">
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
                    <button class="btn btn-primary" data-modal="addProductModal">➕ Add New Product</button>
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

            <!-- Crop Requests Management (Dummy UI) -->
            <div id="crop-requests-section" class="content-section" style="display: none;">
                <div class="content-card">
                    <h3 class="card-title">Crop Requests from Buyers</h3>
                    <div class="card-content" id="cropRequestsContainer">
                        <!-- Filled by JS: loadCropRequestsData() -->
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

                <!-- Earnings Breakdown Table -->
                <div class="content-card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">💰 Earnings Breakdown</h3>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order ID</th>
                                    <th>Buyer</th>
                                    <th>Product</th>
                                    <th>Gross Amount</th>
                                    <th>Platform Fee (5%)</th>
                                    <th>Net Earnings</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="earningsTableBody">
                                <!-- Earnings will be populated by JavaScript -->
                            </tbody>
                        </table>
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

                <!-- Deliveries Table -->
                <div class="content-card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">📦 Active Deliveries</h3>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Delivery ID</th>
                                    <th>Order ID</th>
                                    <th>Buyer</th>
                                    <th>Product Details</th>
                                    <th>Route</th>
                                    <th>Transporter</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                    <th>Pickup Date</th>
                                    <th>Delivery Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="deliveriesTableBody">
                                <!-- Deliveries will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">⭐ Customer Reviews</h1>
                    <p class="content-subtitle">See what buyers are saying about your products</p>
                </div>

                <!-- Reviews Stats -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number">4.8</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">127</div>
                        <div class="stat-label">Total Reviews</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Positive Reviews</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">12</div>
                        <div class="stat-label">This Month</div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="content-card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">💬 Recent Reviews</h3>
                    </div>
                    <div class="card-content" id="reviewsContainer">
                        <!-- Reviews will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Profile Management -->
            <div id="profile-section" class="content-section profile-section" style="display: none;">
                <!-- Profile Header with Photo -->
                <div class="profile-header">
                    <h1>Personal Information</h1>
                    <p>Manage your profile details and account settings</p>

                    <div class="profile-photo-container">
                        <div class="profile-photo-wrapper">
                            <img id="profilePhoto" src="<?= ROOT ?>/assets/images/default-farmer.png" alt=" ">
                            <div class="photo-upload-overlay" onclick="uploadPhoto()" title="Change Photo">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <button class="photo-upload-btn" onclick="uploadPhoto()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                        Change Profile Photo
                    </button>
                </div>

                <!-- Profile Form -->
                <div class="profile-form-section">
                    <div class="profile-form-grid">
                        <div class="form-group">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" id="profileName" class="form-input" placeholder="Enter your full name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address <span class="required">*</span></label>
                            <input type="email" id="profileEmail" class="form-input" placeholder="your.email@example.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="profilePhone" class="form-input" placeholder="+94 77 123 4567">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Location <span class="required">*</span></label>
                            <input type="text" id="profileLocation" class="form-input" placeholder="City, Province">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Crops Selling <span class="required">*</span></label>
                            <input type="text" id="profileCrops" class="form-input" placeholder="e.g., Tomatoes, Rice, Mangoes, Carrots">
                            <span class="form-hint">Separate multiple crops with commas</span>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Full Address <span class="required">*</span></label>
                            <textarea id="profileAddress" class="form-input" placeholder="Enter your complete farm address"></textarea>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button class="btn btn-primary" onclick="updateProfile()">Save Changes</button>
                        <button class="btn btn-secondary" onclick="loadProfileData()">Reset</button>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="profile-stats-card">
                    <h3>Account Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-label">Member Since</div>
                            <div class="stat-value">Jan 2024</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Products Listed</div>
                            <div class="stat-value">24</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Orders Fulfilled</div>
                            <div class="stat-value">142</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Average Rating</div>
                            <div class="stat-value">4.8/5</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Response Time</div>
                            <div class="stat-value">&lt; 2h</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Total Earnings</div>
                            <div class="stat-value">Rs. 842K</div>
                        </div>
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
                <h3 class="modal-title">Add New Product Details</h3>
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
                        <label for="productLocation">Farm Location</label>
                        <input type="text" id="productLocation" name="location" class="form-control" placeholder="e.g., Matale, Central Province">
                    </div>

                    <div class="form-group">
                        <label for="listingDate">Available From</label>
                        <input type="date" id="listingDate" name="listing_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label for="productImage">Product Image *</label>
                        <input type="file" id="productImage" name="image" class="form-control" accept="image/*" required>
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
                <h3 class="modal-title">Edit Product Details</h3>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" id="editProductId" name="id">

                    <div class="form-group">
                        <label for="editProductName">Product Name *</label>
                        <input type="text" id="editProductName" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="editProductCategory">Category *</label>
                        <select id="editProductCategory" name="category" class="form-control" required>
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
                        <label for="editProductPrice">Price per KG (Rs.) *</label>
                        <input type="number" id="editProductPrice" name="price" class="form-control" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="editProductQuantity">Available Quantity (KG) *</label>
                        <input type="number" id="editProductQuantity" name="quantity" class="form-control" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="editProductLocation">Farm Location</label>
                        <input type="text" id="editProductLocation" name="location" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="editListingDate">Available From</label>
                        <input type="date" id="editListingDate" name="listing_date" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="editProductDescription">Description</label>
                        <textarea id="editProductDescription" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="editProductImage">Product Image</label>
                        <input type="file" id="editProductImage" name="image" class="form-control" accept="image/*">
                        <div id="currentImagePreview" style="margin-top: 10px;"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">💾 Save Changes</button>
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