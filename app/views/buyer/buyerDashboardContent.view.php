            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">Dashboard Overview</h1>
                    <p class="content-subtitle">Welcome back, <?= htmlspecialchars($username) ?>! Here's what's happening with your orders.</p>
                </div>

                <!-- Stats Grid -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <!-- <div class="stat-icon primary">📦</div>-->
                        <div class="stat-number">8</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <!-- <div class="stat-icon warning">⏳</div>-->
                        <div class="stat-number">3</div>
                        <div class="stat-label">Pending Orders</div>
                    </div>
                    <div class="stat-card">
                        <!--<div class="stat-icon success">💰</div>-->
                        <div class="stat-number">Rs. 28,450</div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                    <div class="stat-card">
                        <!-- <div class="stat-icon info">❤️</div>-->
                        <div class="stat-number">12</div>
                        <div class="stat-label">Wishlist Items</div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Orders</h3>
                        <button class="btn btn-outline btn-sm" onclick="showSection('orders')">View All</button>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Farmer</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>#ORD-2045</strong></td>
                                        <td>Fresh Tomatoes</td>
                                        <td>2kg</td>
                                        <td>Ranjith Fernando (Matale)</td>
                                        <td><strong>Rs. 240</strong></td>
                                        <td>Aug 15, 2025</td>
                                        <td><span class="order-status delivered">DELIVERED</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#ORD-2044</strong></td>
                                        <td>Green Beans</td>
                                        <td>1kg</td>
                                        <td>Kumari Silva (Kandy)</td>
                                        <td><strong>Rs. 180</strong></td>
                                        <td>Aug 18, 2025</td>
                                        <td><span class="order-status pending">PENDING</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>#ORD-2043</strong></td>
                                        <td>Red Rice</td>
                                        <td>5kg</td>
                                        <td>Sunil Perera (Anuradhapura)</td>
                                        <td><strong>Rs. 475</strong></td>
                                        <td>Aug 12, 2025</td>
                                        <td><span class="order-status delivered">DELIVERED</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-content">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                            <button class="btn btn-primary" onclick="showSection('products')">Browse Products</button>
                            <button class="btn btn-secondary" onclick="showSection('orders')">View All Orders</button>
                            <button class="btn btn-outline" onclick="showSection('wishlist')">My Wishlist</button>
                            <button class="btn btn-outline" onclick="showSection('tracking')">Track Orders</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Products Section -->
            <div id="products-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Browse Products</h1>
                    <p class="content-subtitle">Discover fresh produce from local farmers</p>
                </div>

                <!-- Filter Section -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Products</h3>
                    </div>
                    <div class="card-content">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                            <div>
                                <label for="searchInput" style="display: none;">Search Products</label>
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by name or farmer..." onkeyup="filterProducts()" aria-label="Search products">
                            </div>
                            <div>
                                <label for="categoryFilter" style="display: none;">Filter by Category</label>
                                <select id="categoryFilter" class="form-control" onchange="filterProducts()" aria-label="Filter by category">
                                    <option value="">All Categories</option>
                                    <option value="vegetables">Vegetables</option>
                                    <option value="fruits">Fruits</option>
                                    <option value="cereals">Cereals</option>
                                    <option value="legumes">Legumes</option>
                                    <option value="spices">Spices</option>
                                    <option value="yams">Yams</option>
                                    <option value="leafy">Leafy Greens</option>
                                </select>
                            </div>
                            <div>
                                <label for="locationFilter" style="display: none;">Filter by Location</label>
                                <select id="locationFilter" class="form-control" onchange="filterProducts()" aria-label="Filter by location">
                                    <option value="">All Locations</option>
                                    <option value="colombo">Colombo</option>
                                    <option value="kandy">Kandy</option>
                                    <option value="matale">Matale</option>
                                    <option value="anuradhapura">Anuradhapura</option>
                                    <option value="galle">Galle</option>
                                    <option value="nuwara eliya">Nuwara Eliya</option>
                                    <option value="badulla">Badulla</option>
                                    <option value="kurunegala">Kurunegala</option>
                                </select>
                            </div>
                            <div>
                                <label for="priceFilter" style="display: none;">Filter by Price</label>
                                <select id="priceFilter" class="form-control" onchange="filterProducts()" aria-label="Filter by price">
                                    <option value="">All Prices</option>
                                    <option value="0-100">Under Rs. 100</option>
                                    <option value="100-200">Rs. 100 - Rs. 200</option>
                                    <option value="200-500">Rs. 200 - Rs. 500</option>
                                    <option value="500+">Above Rs. 500</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
                    <?php if (empty($products)): ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                            <div style="font-size: 3rem; margin-bottom: 20px;">🌾</div>
                            <h3>No products available yet</h3>
                            <p>Check back later for fresh products from our farmers!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card"
                                data-id="<?= htmlspecialchars($product->id) ?>"
                                data-name="<?= strtolower(htmlspecialchars($product->name)) ?>"
                                data-category="<?= strtolower(htmlspecialchars($product->category)) ?>"
                                data-location="<?= strtolower(htmlspecialchars($product->location)) ?>"
                                data-price="<?= htmlspecialchars($product->price) ?>"
                                data-farmer="<?= strtolower(htmlspecialchars($product->farmer_name ?? '')) ?>"
                                data-image="<?= !empty($product->image) ? htmlspecialchars($product->image) : '' ?>">

                                <div class="product-image">
                                    <?php if (!empty($product->image) && file_exists("assets/images/products/" . $product->image)): ?>
                                        <img src="<?= ROOT ?>/assets/images/products/<?= htmlspecialchars($product->image) ?>"
                                            alt="<?= htmlspecialchars($product->name) ?>">
                                    <?php else: ?>
                                        <img src="<?= ROOT ?>/assets/images/default-product.svg"
                                            alt="<?= htmlspecialchars($product->name) ?>"
                                            style="opacity: 0.6;">
                                    <?php endif; ?>
                                </div>

                                <div class="product-info">
                                    <h3 class="product-name"><?= htmlspecialchars($product->name) ?></h3>
                                    <p class="product-farmer">
                                        <?= htmlspecialchars($product->farmer_name ?? 'Unknown Farmer') ?>
                                        (<?= htmlspecialchars($product->location ?? 'Unknown Location') ?>)
                                    </p>
                                    <p class="product-description">
                                        <?= htmlspecialchars($product->description ?? 'Fresh produce from local farm') ?>
                                    </p>
                                    <div class="product-price">Rs. <?= number_format($product->price, 2) ?>/kg</div>
                                    <div class="product-stock">
                                        <?= htmlspecialchars($product->quantity) ?>kg available
                                    </div>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <button class="btn btn-primary btn-add-cart"
                                            onclick="addToCart(<?= $product->id ?>, '<?= addslashes(htmlspecialchars($product->name)) ?>', <?= $product->price ?>, <?= $product->quantity ?>)">
                                            🛒 Add to Cart
                                        </button>
                                        <button class="btn btn-outline" onclick="addToWishlist(<?= $product->id ?>, event)">
                                            ❤️ Wishlist
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Orders Section -->
            <div id="orders-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">My Orders</h1>
                    <p class="content-subtitle">Track and manage your order history</p>
                </div>

                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h4 class="order-title">Order #ORD-2045</h4>
                            <p style="color: #666; font-size: 0.9rem; margin-top: 4px;">Placed on Aug 15, 2025</p>
                        </div>
                        <span class="order-status delivered">DELIVERED</span>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="order-detail-label">Product</span>
                            <span class="order-detail-value">Fresh Tomatoes</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Quantity</span>
                            <span class="order-detail-value">2kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Farmer</span>
                            <span class="order-detail-value">Ranjith Fernando</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Total</span>
                            <span class="order-detail-value">Rs. 240</span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="showNotification('Order details viewed', 'info')">View Details</button>
                        <button class="btn btn-sm btn-secondary" onclick="showNotification('Reordering...', 'info')">Reorder</button>
                        <button class="btn btn-sm btn-outline" onclick="showNotification('Review submitted', 'success')">Write Review</button>
                    </div>
                </div>

                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h4 class="order-title">Order #ORD-2044</h4>
                            <p style="color: #666; font-size: 0.9rem; margin-top: 4px;">Placed on Aug 18, 2025</p>
                        </div>
                        <span class="order-status pending">PENDING</span>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="order-detail-label">Product</span>
                            <span class="order-detail-value">Green Beans</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Quantity</span>
                            <span class="order-detail-value">1kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Farmer</span>
                            <span class="order-detail-value">Kumari Silva</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Total</span>
                            <span class="order-detail-value">Rs. 180</span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="showNotification('Order details viewed', 'info')">View Details</button>
                        <button class="btn btn-sm btn-danger" onclick="showNotification('Order cancelled', 'warning')">Cancel Order</button>
                    </div>
                </div>

                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h4 class="order-title">Order #ORD-2043</h4>
                            <p style="color: #666; font-size: 0.9rem; margin-top: 4px;">Placed on Aug 12, 2025</p>
                        </div>
                        <span class="order-status shipped">SHIPPED</span>
                    </div>
                    <div class="order-details">
                        <div class="order-detail">
                            <span class="order-detail-label">Product</span>
                            <span class="order-detail-value">Red Rice</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Quantity</span>
                            <span class="order-detail-value">5kg</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Farmer</span>
                            <span class="order-detail-value">Sunil Perera</span>
                        </div>
                        <div class="order-detail">
                            <span class="order-detail-label">Total</span>
                            <span class="order-detail-value">Rs. 475</span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="showNotification('Tracking order...', 'info')">Track Order</button>
                        <button class="btn btn-sm btn-outline" onclick="showNotification('Order details viewed', 'info')">View Details</button>
                    </div>
                </div>
            </div>

            <!-- Tracking Section -->
            <div id="tracking-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Order Tracking</h1>
                    <p class="content-subtitle">Track your delivery status in real-time</p>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Active Deliveries</h3>
                    </div>
                    <div class="card-content">
                        <div style="padding: 20px; background: #f8f9fa; border-radius: 12px; margin-bottom: 20px;">
                            <h4 style="margin-bottom: 16px; color: #2c3e50;">Order #ORD-2044 - Green Beans</h4>
                            <div style="display: flex; flex-direction: column; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #4CAF50; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
                                    <div>
                                        <strong>Order Confirmed</strong>
                                        <p style="margin: 0; font-size: 0.875rem; color: #666;">Aug 18, 2025 - 10:30 AM</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #4CAF50; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
                                    <div>
                                        <strong>Being Prepared</strong>
                                        <p style="margin: 0; font-size: 0.875rem; color: #666;">Aug 18, 2025 - 2:15 PM</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #ff9800; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">→</div>
                                    <div>
                                        <strong>Out for Delivery</strong>
                                        <p style="margin: 0; font-size: 0.875rem; color: #666;">Expected today by 6:00 PM</p>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px; opacity: 0.4;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">○</div>
                                    <div>
                                        <strong>Delivered</strong>
                                        <p style="margin: 0; font-size: 0.875rem; color: #666;">Pending</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="padding: 20px; background: #fff9e6; border-radius: 12px; border-left: 4px solid #ff9800;">
                            <h4 style="margin-bottom: 8px; color: #f57c00;">📦 Delivery Information</h4>
                            <p style="margin: 0; color: #666;">Your order is on the way! Expected delivery: <strong>Today, 6:00 PM</strong></p>
                            <p style="margin: 8px 0 0 0; color: #666;">Contact: +94 77 123 4567</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wishlist Section -->
            <div id="wishlist-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">My Wishlist</h1>
                    <p class="content-subtitle">Products you plan to purchase later</p>
                </div>

                <div class="products-grid" id="wishlist-list">
                    <?php if (empty($wishlistItems)): ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                            <div style="font-size: 3rem; margin-bottom: 20px;"></div>
                            <h3>Your wishlist is empty</h3>
                            <p>Browse products and click "Wishlist" to save them here.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($wishlistItems as $item): ?>
                            <div class="product-card" 
                                 data-wishlist-product="<?= (int)$item->product_id ?>"
                                 data-id="<?= (int)$item->product_id ?>"
                                 data-name="<?= strtolower(htmlspecialchars($item->name ?? 'Product')) ?>"
                                 data-image="<?= !empty($item->image) ? htmlspecialchars($item->image) : '' ?>">
                                <div class="product-image">
                                    <?php if (!empty($item->image) && file_exists("assets/images/products/" . $item->image)): ?>
                                        <img src="<?= ROOT ?>/assets/images/products/<?= htmlspecialchars($item->image) ?>"
                                            alt="<?= htmlspecialchars($item->name) ?>">
                                    <?php else: ?>
                                        <img src="<?= ROOT ?>/assets/images/default-product.svg"
                                            alt="<?= htmlspecialchars($item->name) ?>"
                                            style="opacity: 0.6;">
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name"><?= htmlspecialchars($item->name ?? 'Product unavailable') ?></h3>
                                    <div class="product-price">
                                        <?= isset($item->price) ? 'Rs. ' . number_format($item->price, 2) . '/kg' : 'Price unavailable' ?>
                                    </div>
                                    <div class="product-stock">
                                        <?= isset($item->available_quantity) ? htmlspecialchars($item->available_quantity) . 'kg available' : '' ?>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 8px;">
                                                                                <button class="btn btn-primary btn-sm"
                                                                                        onclick="addToCartAjax(<?= (int)$item->product_id ?>, '<?= addslashes(htmlspecialchars($item->name ?? 'Product')) ?>', <?= (float)($item->price ?? 0) ?>, <?= (float)($item->available_quantity ?? 0) ?>)">
                                                                                            Add to Cart
                                                                                </button>
                                        <button class="btn btn-danger btn-sm" onclick="removeFromWishlist(<?= (int)$item->product_id ?>)">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>


            <!-- Requests Section -->
            <div id="requests-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Special Requests</h1>
                    <p class="content-subtitle">Custom orders and bulk purchase inquiries</p>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Submit New Request</h3>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" class="form-control" placeholder="What product do you need?">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Quantity (kg)</label>
                                <input type="number" class="form-control" placeholder="Enter quantity">
                            </div>
                            <div class="form-group">
                                <label>Target Price (Rs/kg)</label>
                                <input type="number" class="form-control" placeholder="Your budget">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Additional Details</label>
                            <textarea class="form-control" rows="4" placeholder="Describe your requirements..."></textarea>
                        </div>
                        <button class="btn btn-primary" 
                            style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%) !important; border: none !important; padding: 12px 32px !important; font-size: 1rem !important; font-weight: 600 !important; border-radius: 8px !important; color: white !important; cursor: pointer !important; transition: all 0.3s ease !important; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3) !important;"
                            onmouseover="this.style.boxShadow='0 6px 16px rgba(76, 175, 80, 0.5)'; this.style.transform='translateY(-2px)';"
                            onmouseout="this.style.boxShadow='0 4px 12px rgba(76, 175, 80, 0.3)'; this.style.transform='translateY(0)';"
                            onclick="showNotification('Request submitted successfully!', 'success')">
                            ✓ Submit Request
                        </button>
                    </div>
                </div>

                <div class="content-card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h3 class="card-title">Your Requests</h3>
                    </div>
                    <div class="card-content">
                        <div style="padding: 20px; background: #f8f9fa; border-radius: 12px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div>
                                    <h4 style="margin: 0 0 8px 0;">Organic Potatoes - 50kg</h4>
                                    <p style="margin: 0; color: #666; font-size: 0.9rem;">Requested on Aug 10, 2025</p>
                                </div>
                                <span class="order-status pending">PENDING</span>
                            </div>
                            <p style="color: #666; margin: 0 0 12px 0;">Target Price: Rs. 80/kg</p>
                            <button class="btn btn-sm btn-outline" 
                                style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%) !important; color: white !important; border: none !important; padding: 10px 24px !important; font-weight: 600 !important; border-radius: 6px !important; cursor: pointer !important; transition: all 0.3s ease !important; box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3) !important;"
                                onmouseover="this.style.boxShadow='0 5px 14px rgba(33, 150, 243, 0.5)'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.boxShadow='0 3px 10px rgba(33, 150, 243, 0.3)'; this.style.transform='translateY(0)';"
                                onclick="showNotification('Request details viewed', 'info')">
                                📋 View Details
                            </button>
                        </div>

                        <div style="padding: 20px; background: #f8f9fa; border-radius: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div>
                                    <h4 style="margin: 0 0 8px 0;">Fresh Spinach - 20kg</h4>
                                    <p style="margin: 0; color: #666; font-size: 0.9rem;">Requested on Aug 5, 2025</p>
                                </div>
                                <span class="order-status delivered">FULFILLED</span>
                            </div>
                            <p style="color: #666; margin: 0 0 12px 0;">Final Price: Rs. 120/kg</p>
                            <button class="btn btn-sm btn-outline" 
                                style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%) !important; color: white !important; border: none !important; padding: 10px 24px !important; font-weight: 600 !important; border-radius: 6px !important; cursor: pointer !important; transition: all 0.3s ease !important; box-shadow: 0 3px 10px rgba(255, 152, 0, 0.3) !important;"
                                onmouseover="this.style.boxShadow='0 5px 14px rgba(255, 152, 0, 0.5)'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.boxShadow='0 3px 10px rgba(255, 152, 0, 0.3)'; this.style.transform='translateY(0)';"
                                onclick="showNotification('Request details viewed', 'info')">
                                📋 View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">My Reviews</h1>
                    <p class="content-subtitle">Reviews you've written for products and farmers</p>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Your Reviews (5)</h3>
                    </div>
                    <div class="card-content">
                        <div style="padding: 20px; border: 1px solid #e0e0e0; border-radius: 12px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div>
                                    <h4 style="margin: 0 0 4px 0;">Fresh Tomatoes</h4>
                                    <p style="margin: 0; color: #666; font-size: 0.9rem;">by Ranjith Fernando</p>
                                </div>
                                <div style="color: #ff9800; font-size: 1.2rem;">★★★★★</div>
                            </div>
                            <p style="color: #666; margin: 12px 0; line-height: 1.6;">"Excellent quality tomatoes! Very fresh and tasty. The farmer was very professional and delivered on time. Highly recommended!"</p>
                            <p style="margin: 0; color: #999; font-size: 0.85rem;">Reviewed on Aug 16, 2025</p>
                        </div>

                        <div style="padding: 20px; border: 1px solid #e0e0e0; border-radius: 12px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div>
                                    <h4 style="margin: 0 0 4px 0;">Red Rice</h4>
                                    <p style="margin: 0; color: #666; font-size: 0.9rem;">by Sunil Perera</p>
                                </div>
                                <div style="color: #ff9800; font-size: 1.2rem;">★★★★☆</div>
                            </div>
                            <p style="color: #666; margin: 12px 0; line-height: 1.6;">"Good quality rice. Traditional variety with great nutritional value. Delivery was slightly delayed but product quality made up for it."</p>
                            <p style="margin: 0; color: #999; font-size: 0.85rem;">Reviewed on Aug 13, 2025</p>
                        </div>

                        <div style="padding: 20px; border: 1px solid #e0e0e0; border-radius: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div>
                                    <h4 style="margin: 0 0 4px 0;">Green Beans</h4>
                                    <p style="margin: 0; color: #666; font-size: 0.9rem;">by Kumari Silva</p>
                                </div>
                                <div style="color: #ff9800; font-size: 1.2rem;">★★★★★</div>
                            </div>
                            <p style="color: #666; margin: 12px 0; line-height: 1.6;">"Premium quality green beans! Very fresh and crunchy. Perfect for stir-fry dishes. Will definitely order again!"</p>
                            <p style="margin: 0; color: #999; font-size: 0.85rem;">Reviewed on Aug 10, 2025</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Section -->
            <div id="notifications-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Notifications</h1>
                    <p class="content-subtitle">Stay updated with your orders and offers</p>
                </div>

                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Notifications</h3>
                        <button class="btn btn-sm btn-outline" onclick="showNotification('All notifications marked as read', 'success')">Mark All as Read</button>
                    </div>
                    <div class="card-content">
                        <div style="padding: 16px; border-left: 4px solid #4CAF50; background: #f1f8f4; border-radius: 8px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h4 style="margin: 0; color: #2e7d32;">✓ Order Delivered</h4>
                                <span style="font-size: 0.85rem; color: #666;">2 hours ago</span>
                            </div>
                            <p style="margin: 0; color: #666;">Your order #ORD-2045 (Fresh Tomatoes) has been delivered successfully.</p>
                        </div>

                        <div style="padding: 16px; border-left: 4px solid #ff9800; background: #fff9e6; border-radius: 8px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h4 style="margin: 0; color: #f57c00;">📦 Order Shipped</h4>
                                <span style="font-size: 0.85rem; color: #666;">5 hours ago</span>
                            </div>
                            <p style="margin: 0; color: #666;">Your order #ORD-2044 (Green Beans) is out for delivery. Expected by 6:00 PM.</p>
                        </div>

                        <div style="padding: 16px; border-left: 4px solid #2196F3; background: #e3f2fd; border-radius: 8px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h4 style="margin: 0; color: #1565c0;">🎉 New Product Alert</h4>
                                <span style="font-size: 0.85rem; color: #666;">1 day ago</span>
                            </div>
                            <p style="margin: 0; color: #666;">Fresh Sweet Mangoes now available from Pradeep Jayasinghe (Galle) - Rs. 150/kg</p>
                        </div>

                        <div style="padding: 16px; border-left: 4px solid #9c27b0; background: #f3e5f5; border-radius: 8px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h4 style="margin: 0; color: #7b1fa2;">💰 Special Offer</h4>
                                <span style="font-size: 0.85rem; color: #666;">2 days ago</span>
                            </div>
                            <p style="margin: 0; color: #666;">Get 20% off on orders above Rs. 1000! Use code: FRESH20. Valid till Aug 30.</p>
                        </div>

                        <div style="padding: 16px; border-left: 4px solid #4CAF50; background: #f1f8f4; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h4 style="margin: 0; color: #2e7d32;">✓ Request Fulfilled</h4>
                                <span style="font-size: 0.85rem; color: #666;">3 days ago</span>
                            </div>
                            <p style="margin: 0; color: #666;">Your request for Fresh Spinach (20kg) has been fulfilled. Check your orders.</p>
                        </div>
                    </div>
                </div>
            </div>