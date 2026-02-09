<div class="content-section">
    <div class="content-header">
        <h1 class="content-title">My Orders</h1>
        <p class="content-subtitle">Orders containing your products</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state" style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed #e0e0e0;">
            <div style="font-size: 3rem; margin-bottom: 20px;">📦</div>
            <h3>No Orders Yet</h3>
            <p style="color: #666;">You don't have any orders containing your products yet.</p>
        </div>
    <?php else: ?>
        <div class="orders-grid">
            <?php foreach ($orders as $order): ?>
                <div class="order-card" data-order-id="<?= htmlspecialchars($order->id) ?>">
                    <div class="order-header">
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 1rem; font-weight: 600; color: #2c3e50;">Order #<?= htmlspecialchars($order->id) ?></h3>
                            <span style="font-size: 0.85rem; color: #666;"><?= date('M d, Y', strtotime($order->created_at)) ?></span>
                        </div>
                        <span class="order-status status-<?= htmlspecialchars($order->status) ?>">
                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($order->status))) ?>
                        </span>
                    </div>

                    <div class="order-body">
                        <div class="order-info-grid">
                            <div class="info-item">
                                <span class="info-label">Buyer</span>
                                <span class="info-value"><?= htmlspecialchars($order->buyer_name) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Location</span>
                                <span class="info-value"><?= htmlspecialchars($order->delivery_city) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Items</span>
                                <span class="info-value"><?= htmlspecialchars($order->my_items_count) ?> item(s)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Your Earnings</span>
                                <span class="info-value" style="color: #4CAF50; font-weight: 600;">Rs. <?= number_format($order->my_order_total, 2) ?></span>
                            </div>
                        </div>

                        <div class="order-details-row">
                            <div style="font-size: 0.85rem; color: #666;">
                                <div style="margin-bottom: 4px;"><strong>Address:</strong> <?= htmlspecialchars($order->delivery_address) ?></div>
                                <div><strong>Phone:</strong> <?= htmlspecialchars($order->delivery_phone) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="order-footer">
                        <button class="btn btn-view-details" data-order-id="<?= htmlspecialchars($order->id) ?>">
                            View My Items
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Order Details</h2>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="orderDetailsContent">
                <div class="loading">Loading...</div>
            </div>
        </div>
    </div>
</div>