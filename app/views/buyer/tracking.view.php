<div class="buyer-tracking-page">
    <div class="content-header">
        <h1 class="content-title">Order Delivery Tracking</h1>
        <p class="content-subtitle">Current delivery statuses for your ongoing orders</p>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Ongoing Orders</h3>
        </div>
        <div class="card-content">
            <?php if (empty($trackingRows)): ?>
                <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                    <div style="font-size: 2.25rem; margin-bottom: 12px;">🚚</div>
                    <h3 style="margin-bottom: 8px;">No active deliveries</h3>
                    <p style="margin-bottom: 16px;">Your delivery statuses will appear here once an order is confirmed.</p>
                    <a href="<?= ROOT ?>/buyerproducts" class="btn btn-primary">Browse Products</a>
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
                                <th>Destination</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trackingRows as $row): ?>
                                <?php
                                $deliveryStatus = $row->delivery_status ?? null;
                                $effectiveStatus = $deliveryStatus ?: $row->order_status;
                                $statusClass = strtolower((string)$effectiveStatus);
                                ?>
                                <tr>
                                    <td>
                                        <strong>#ORD-<?= (int)$row->order_id ?></strong>
                                        <div style="font-size: 0.82rem; color: #6b7280;">
                                            <?= date('M d, Y', strtotime($row->order_created_at)) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="order-status <?= htmlspecialchars(strtolower((string)$row->order_status)) ?>">
                                            <?= strtoupper($row->order_status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="order-status <?= htmlspecialchars($statusClass) ?>">
                                            <?= strtoupper(str_replace('_', ' ', $effectiveStatus)) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row->transporter_name ?? 'Pending Assignment') ?></td>
                                    <td><?= htmlspecialchars($row->delivery_city ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $updatedAt = $row->delivery_updated_at ?? $row->delivery_created_at ?? $row->order_created_at;
                                        echo htmlspecialchars(date('M d, Y h:i A', strtotime($updatedAt)));
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
