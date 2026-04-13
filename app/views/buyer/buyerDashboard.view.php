<!-- Dashboard Section -->
<div id="dashboard-section" class="content-section buyer-dashboard-overview">
    <div class="content-header">
        <h1 class="content-title">Dashboard Overview</h1>
        <p class="content-subtitle">Welcome back, <?= htmlspecialchars($username) ?>! Here's what's happening with your orders.</p>
    </div>

    <!-- Stats Grid -->
    <div class="dashboard-stats buyer-dashboard-stats">
        <div class="stat-card buyer-dashboard-stat-card">
            <!-- <div class="stat-icon primary">📦</div>-->
            <div class="stat-number buyer-dashboard-stat-number"><?= $totalOrders ?? 0 ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card buyer-dashboard-stat-card">
            <!-- <div class="stat-icon warning">⏳</div>-->
            <div class="stat-number buyer-dashboard-stat-number"><?= $pendingOrders ?? 0 ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
        <div class="stat-card buyer-dashboard-stat-card">
            <!--<div class="stat-icon success">💰</div>-->
            <div class="stat-number buyer-dashboard-stat-number">Rs. <?= number_format($totalSpent ?? 0, 2) ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
        <div class="stat-card buyer-dashboard-stat-card">
            <!-- <div class="stat-icon info">❤️</div>-->
            <div class="stat-number buyer-dashboard-stat-number"><?= $wishlistCount ?? 0 ?></div>
            <div class="stat-label">Wishlist Items</div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="content-card buyer-dashboard-recent-orders-card">
        <div class="card-header">
            <h3 class="card-title">Recent Orders</h3>
            <button class="btn btn-outline btn-sm" onclick="BuyerDashboard.showSection('orders')">View All</button>
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
                                        $productNames = array_slice(array_map(function ($item) {
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
    <div class="content-card buyer-dashboard-quick-actions-card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="card-content">
            <div class="buyer-dashboard-quick-actions-grid">
                <button class="btn btn-primary buyer-dashboard-quick-btn" onclick="window.location.href='<?= ROOT ?>/buyerproducts'">Browse Products</button>
                <button class="btn btn-secondary buyer-dashboard-quick-btn" onclick="BuyerDashboard.showSection('orders')">View All Orders</button>
                <button class="btn btn-outline buyer-dashboard-quick-btn" onclick="window.location.href='<?= ROOT ?>/wishlist'">My Wishlist</button>
                <button class="btn btn-outline buyer-dashboard-quick-btn" onclick="BuyerDashboard.showSection('tracking')">Track Orders</button>
            </div>
        </div>
    </div>
</div>

<!-- Orders Section -->
<div id="orders-section" class="content-section buyer-orders-section" style="display: none;">
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
                    <button class="btn btn-sm btn-secondary" onclick="BuyerDashboard.viewOrderDetails(<?= $order->id ?>)">View Order Details</button>
                    <?php if ($statusClass === 'delivered' && !empty($items)): ?>
                        <?php $firstReviewableItem = $items[0]; ?>
                        <button class="btn btn-sm btn-outline"
                            onclick="BuyerDashboard.openReviewModal(<?= $order->id ?>, <?= (int)$firstReviewableItem->product_id ?>, <?= (int)$firstReviewableItem->farmer_id ?>, '<?= addslashes(htmlspecialchars($firstReviewableItem->product_name)) ?>')">
                            ★ Write Review
                        </button>
                    <?php endif; ?>
                    <?php if ($order->status === 'pending' || $order->status === 'confirmed'): ?>
                        <button class="btn btn-sm btn-danger" onclick="BuyerDashboard.cancelOrder(<?= $order->id ?>)">Cancel Order</button>
                    <?php endif; ?>
                    <?php if ($order->status === 'shipped'): ?>
                        <button class="btn btn-sm btn-secondary" onclick="BuyerDashboard.trackOrder(<?= $order->id ?>)">Track Order</button>
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
<div id="tracking-section" class="content-section buyer-tracking-section" style="display: none;">
    <div class="content-header">
        <h1 class="content-title">Order Tracking</h1>
        <p class="content-subtitle">Track ongoing order delivery statuses</p>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Active Deliveries</h3>
        </div>
        <div class="card-content">
            <?php if (empty($trackingRows)): ?>
                <div style="padding: 20px; background: #f8f9fa; border-radius: 12px; text-align: center; color: #666;">
                    No ongoing delivery statuses yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Order Status</th>
                                <th>Delivery Status</th>
                                <th>Transporter</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trackingRows as $row): ?>
                                <?php
                                $effectiveStatus = $row->delivery_status ?? $row->order_status;
                                $statusClass = strtolower((string)$effectiveStatus);
                                $updatedAt = $row->delivery_updated_at ?? $row->delivery_created_at ?? $row->order_created_at;
                                ?>
                                <tr>
                                    <td>#ORD-<?= (int)$row->order_id ?></td>
                                    <td><span class="order-status <?= htmlspecialchars(strtolower((string)$row->order_status)) ?>"><?= strtoupper($row->order_status) ?></span></td>
                                    <td><span class="order-status <?= htmlspecialchars($statusClass) ?>"><?= strtoupper(str_replace('_', ' ', $effectiveStatus)) ?></span></td>
                                    <td><?= htmlspecialchars($row->transporter_name ?? 'Pending Assignment') ?></td>
                                    <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($updatedAt))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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

<!-- Order Details Modal -->
<div id="order-details-modal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 800px; border-radius: 12px; position: relative; animation: slideIn 0.3s ease-out;">
        <span class="close-modal" onclick="BuyerDashboard.closeOrderModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>

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
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .close-modal:hover,
    .close-modal:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
