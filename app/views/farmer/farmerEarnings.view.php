<div class="content-section">
    <div class="content-header">
        <h1 class="content-title">My Earnings</h1>
        <p class="content-subtitle">Track your income and financial performance</p>
    </div>

    <!-- Earnings Stats Cards -->
    <div class="earnings-stats-grid">
        <div class="earnings-stat-card primary">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Earnings</div>
                <div class="stat-value">Rs. <?= number_format($totalEarnings, 2) ?></div>
            </div>
        </div>

        <div class="earnings-stat-card success">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">This Month</div>
                <div class="stat-value">Rs. <?= number_format($monthlyEarnings, 2) ?></div>
            </div>
        </div>

        <div class="earnings-stat-card info">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value"><?= number_format($earningsStats->total_orders) ?></div>
            </div>
        </div>

        <div class="earnings-stat-card warning">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Avg. Order Value</div>
                <div class="stat-value">Rs. <?= number_format($earningsStats->avg_order_value, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Earnings by Product -->
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Top Earning Products</h3>
        </div>
        <div class="card-content">
            <?php if (empty($earningsByProduct)): ?>
                <div class="empty-state-small">
                    <p>No product earnings data yet</p>
                </div>
            <?php else: ?>
                <div class="products-earnings-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Orders</th>
                                <th>Quantity Sold</th>
                                <th>Total Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($earningsByProduct as $product): ?>
                                <tr>
                                    <td class="product-name"><?= htmlspecialchars($product->product_name) ?></td>
                                    <td><?= number_format($product->order_count) ?></td>
                                    <td><?= number_format($product->total_quantity) ?> kg</td>
                                    <td class="earnings-amount">Rs. <?= number_format($product->total_earnings, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Earnings -->
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Recent Transactions</h3>
        </div>
        <div class="card-content">
            <?php if (empty($recentEarnings)): ?>
                <div class="empty-state-small">
                    <p>No recent transactions</p>
                </div>
            <?php else: ?>
                <div class="earnings-timeline">
                    <?php foreach ($recentEarnings as $earning): ?>
                        <div class="earning-item">
                            <div class="earning-date">
                                <div class="date-day"><?= date('d', strtotime($earning->order_date)) ?></div>
                                <div class="date-month"><?= date('M', strtotime($earning->order_date)) ?></div>
                            </div>
                            <div class="earning-details">
                                <div class="earning-title">Order #<?= htmlspecialchars($earning->order_id) ?></div>
                                <div class="earning-meta">
                                    <span class="buyer-name"><?= htmlspecialchars($earning->buyer_name) ?></span>
                                    <span class="separator">•</span>
                                    <span class="item-count"><?= $earning->item_count ?> item(s)</span>
                                    <span class="separator">•</span>
                                    <span class="order-status status-<?= htmlspecialchars($earning->status) ?>">
                                        <?= ucfirst(str_replace('_', ' ', htmlspecialchars($earning->status))) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="earning-amount">
                                Rs. <?= number_format($earning->order_earnings, 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="earnings-additional-stats">
        <div class="stat-box">
            <div class="stat-box-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
            <div class="stat-box-content">
                <div class="stat-box-value"><?= number_format($earningsStats->total_items_sold) ?></div>
                <div class="stat-box-label">Total Items Sold</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-box-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </div>
            <div class="stat-box-content">
                <div class="stat-box-value"><?= number_format($earningsStats->products_sold) ?></div>
                <div class="stat-box-label">Products Sold</div>
            </div>
        </div>
    </div>
</div>