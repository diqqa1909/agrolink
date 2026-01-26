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
                        <div class="stat-number"><?= $totalOrders ?? 0 ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <!-- <div class="stat-icon warning">⏳</div>-->
                        <div class="stat-number"><?= $pendingOrders ?? 0 ?></div>
                        <div class="stat-label">Pending Orders</div>
                    </div>
                    <div class="stat-card">
                        <!--<div class="stat-icon success">💰</div>-->
                        <div class="stat-number">Rs. <?= number_format($totalSpent ?? 0, 2) ?></div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                    <div class="stat-card">
                        <!-- <div class="stat-icon info">❤️</div>-->
                        <div class="stat-number"><?= $wishlistCount ?? 0 ?></div>
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
                                        <th>Products</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($orders) && count($orders) > 0): ?>
                                        <?php foreach (array_slice($orders, 0, 5) as $orderData): ?>
                                            <?php 
                                            $order = $orderData['order'];
                                            $items = $orderData['items'];
                                            $orderDate = date('M d, Y', strtotime($order->created_at));
                                            $statusClass = strtolower($order->status);
                                            ?>
                                            <tr>
                                                <td><strong>#ORD-<?= $order->id ?></strong></td>
                                                <td>
                                                    <?php 
                                                    $productNames = array_slice(array_map(function($item) { 
                                                        return htmlspecialchars($item->product_name); 
                                                    }, $items), 0, 2);
                                                    echo implode(', ', $productNames);
                                                    if (count($items) > 2) echo ' +' . (count($items) - 2) . ' more';
                                                    ?>
                                                </td>
                                                <td><?= $order->item_count ?? count($items) ?></td>
                                                <td><strong>Rs. <?= number_format($order->order_total, 2) ?></strong></td>
                                                <td><?= $orderDate ?></td>
                                                <td><span class="order-status <?= $statusClass ?>"><?= strtoupper($order->status) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                                No orders yet. Start shopping to see your orders here!
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
                            <button class="btn btn-primary" onclick="window.location.href='<?= ROOT ?>/buyerproducts'">Browse Products</button>
                            <button class="btn btn-secondary" onclick="showSection('orders')">View All Orders</button>
                            <button class="btn btn-outline" onclick="window.location.href='<?= ROOT ?>/wishlist'">My Wishlist</button>
                            <button class="btn btn-outline" onclick="showSection('tracking')">Track Orders</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Section -->
            <div id="orders-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">My Orders</h1>
                    <p class="content-subtitle">Track and manage your order history</p>
                </div>

                <?php if (!empty($orders) && count($orders) > 0): ?>
                    <?php foreach ($orders as $orderData): ?>
                        <?php 
                        $order = $orderData['order'];
                        $items = $orderData['items'];
                        $orderDate = date('M d, Y', strtotime($order->created_at));
                        $statusClass = strtolower($order->status);
                        ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <h4 class="order-title">Order #ORD-<?= $order->id ?></h4>
                                    <p style="color: #666; font-size: 0.9rem; margin-top: 4px;">Placed on <?= $orderDate ?></p>
                                </div>
                                <span class="order-status <?= $statusClass ?>"><?= strtoupper($order->status) ?></span>
                            </div>
                            <div class="order-details">
                                <div class="order-detail">
                                    <span class="order-detail-label">Items</span>
                                    <span class="order-detail-value"><?= count($items) ?> product(s)</span>
                                </div>
                                <div class="order-detail">
                                    <span class="order-detail-label">Subtotal</span>
                                    <span class="order-detail-value">Rs. <?= number_format($order->total_amount, 2) ?></span>
                                </div>
                                <div class="order-detail">
                                    <span class="order-detail-label">Shipping</span>
                                    <span class="order-detail-value">Rs. <?= number_format($order->shipping_cost, 2) ?></span>
                                </div>
                                <div class="order-detail">
                                    <span class="order-detail-label">Total</span>
                                    <span class="order-detail-value">Rs. <?= number_format($order->order_total, 2) ?></span>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e0e0e0;">
                                <h5 style="margin-bottom: 12px; font-size: 0.95rem; color: #666;">Products:</h5>
                                <?php foreach ($items as $item): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                        <div>
                                            <span style="font-weight: 500;"><?= htmlspecialchars($item->product_name) ?></span>
                                            <span style="color: #666; font-size: 0.9rem;"> x <?= $item->quantity ?>kg</span>
                                        </div>
                                        <div>
                                            <span style="font-weight: 500;">Rs. <?= number_format($item->product_price * $item->quantity, 2) ?></span>
                                            <?php if (!empty($item->farmer_name)): ?>
                                                <span style="color: #666; font-size: 0.85rem; display: block; margin-top: 4px;">by <?= htmlspecialchars($item->farmer_name) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-primary" onclick="viewOrderDetails(<?= $order->id ?>)">View Details</button>
                                <?php if ($order->status === 'pending' || $order->status === 'confirmed'): ?>
                                    <button class="btn btn-sm btn-danger" onclick="cancelOrder(<?= $order->id ?>)">Cancel Order</button>
                                <?php endif; ?>
                                <?php if ($order->status === 'shipped'): ?>
                                    <button class="btn btn-sm btn-secondary" onclick="trackOrder(<?= $order->id ?>)">Track Order</button>
                                <?php endif; ?>
                                <?php if ($order->status === 'delivered'): ?>
                                    <button class="btn btn-sm btn-outline" onclick="reorderItems(<?= $order->id ?>)">Reorder</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="content-card">
                        <div class="card-content" style="text-align: center; padding: 60px 20px;">
                            <div style="font-size: 4rem; margin-bottom: 20px;">📦</div>
                            <h3 style="margin-bottom: 12px; color: #2c3e50;">No Orders Yet</h3>
                            <p style="color: #666; margin-bottom: 24px;">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                            <button class="btn btn-primary" onclick="window.location.href='<?= ROOT ?>/buyerproducts'">Browse Products</button>
                        </div>
                    </div>
                <?php endif; ?>
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

            <!-- Requests Section -->
            

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

            <!-- Order Details Modal -->
            <div id="order-details-modal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
                <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 800px; border-radius: 12px; position: relative; animation: slideIn 0.3s ease-out;">
                    <span class="close-modal" onclick="closeOrderModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                    
                    <div id="modal-body">
                        <div style="text-align: center; padding: 40px;">
                            <div class="loader" style="border: 4px solid #f3f3f3; border-top: 4px solid #4CAF50; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                            <p style="margin-top: 16px; color: #666;">Loading order details...</p>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                @keyframes slideIn {
                    from {transform: translateY(-50px); opacity: 0;}
                    to {transform: translateY(0); opacity: 1;}
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                .close-modal:hover,
                .close-modal:focus {
                    color: black;
                    text-decoration: none;
                    cursor: pointer;
                }
            </style>
