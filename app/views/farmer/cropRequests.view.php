<?php
// Content-only view for farmer crop requests list. Rendered inside farmerMain.
?>

<div class="content-section">
    <!-- Header Section -->
    <div class="content-header">
        <h1 class="content-title">Buyer Crop Requests</h1>
        <p class="content-subtitle">View and respond to crop requests from buyers</p>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="padding: 15px; background: rgba(101, 181, 124, 0.15); color: var(--primary-dark); border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--primary-color);">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" style="padding: 15px; background: rgba(244, 67, 54, 0.1); color: var(--danger-dark); border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--danger);">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Requests Table -->
    <?php if (!empty($requests)): ?>
        <div class="content-card" style="padding: 0; overflow: hidden;">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Crop Name</th>
                            <th>Quantity</th>
                            <th>Target Price</th>
                            <th>Delivery Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): 
                            // Default to 'active' if status is empty or null
                            $status = !empty($request->status) ? $request->status : 'active';
                            
                            // Status colors using theme
                            $statusColors = [
                                'active' => ['bg' => '#fff3cd', 'text' => '#856404'],
                                'accepted' => ['bg' => 'rgba(101, 181, 124, 0.2)', 'text' => '#499d57'],
                                'declined' => ['bg' => 'rgba(244, 67, 54, 0.15)', 'text' => '#d32f2f'],
                                'completed' => ['bg' => '#e0e0e0', 'text' => '#666']
                            ];
                            $statusColor = $statusColors[$status] ?? $statusColors['active'];
                        ?>
                            <tr>
                                <td style="font-weight: 500;"><?= htmlspecialchars($request->crop_name) ?></td>
                                <td><?= htmlspecialchars($request->quantity) ?> units</td>
                                <td style="color: var(--primary-color); font-weight: 600;">Rs.<?= number_format($request->target_price, 2) ?></td>
                                <td><?= date('M d, Y', strtotime($request->delivery_date)) ?></td>
                                <td><?= htmlspecialchars($request->location) ?></td>
                                <td>
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; background: <?= $statusColor['bg'] ?>; color: <?= $statusColor['text'] ?>;">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?= ROOT ?>/farmercroprequests/show/<?= $request->id ?>" class="btn btn-sm btn-outline" style="padding: 6px 12px;">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="content-card" style="text-align: center; padding: 60px;">
            <div style="font-size: 3rem; margin-bottom: 20px;">📋</div>
            <h3 style="color: var(--text-dark); margin-bottom: 12px;">No Crop Requests Available</h3>
            <p style="color: var(--text-light);">There are no buyer crop requests at the moment. Check back later!</p>
        </div>
    <?php endif; ?>
</div>
