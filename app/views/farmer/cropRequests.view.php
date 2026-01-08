<?php
// Content-only view for farmer crop requests list. Rendered inside farmerLayout.
?>

<div class="container" style="margin-top: 20px; margin-bottom: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Buyer Crop Requests</h1>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px;">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Requests Table -->
    <?php if (!empty($requests)): ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Crop Name</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Quantity</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Target Price</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Delivery Date</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Location</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Status</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 15px;"><?= htmlspecialchars($request->crop_name) ?></td>
                            <td style="padding: 15px;"><?= htmlspecialchars($request->quantity) ?></td>
                            <td style="padding: 15px;">Rs.<?= number_format($request->target_price, 2) ?></td>
                            <td style="padding: 15px;"><?= date('M d, Y', strtotime($request->delivery_date)) ?></td>
                            <td style="padding: 15px;"><?= htmlspecialchars($request->location) ?></td>
                            <td style="padding: 15px;">
                                <span style="padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                <?php
                                $statusColor = '';
                                switch ($request->status) {
                                    case 'pending':
                                        $statusColor = 'background: #fff3cd; color: #856404;';
                                        break;
                                    case 'accepted':
                                        $statusColor = 'background: #d4edda; color: #155724;';
                                        break;
                                    case 'rejected':
                                        $statusColor = 'background: #f8d7da; color: #721c24;';
                                        break;
                                    case 'completed':
                                        $statusColor = 'background: #cfe2ff; color: #084298;';
                                        break;
                                }
                                echo $statusColor;
                                ?>">
                                    <?= ucfirst($request->status) ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <a href="<?= ROOT ?>/farmercroprequests/show/<?= $request->id ?>" class="btn-sm" style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 3px; margin-right: 5px;">View</a>
                                <?php if ($request->status === 'pending'): ?>
                                    <a href="<?= ROOT ?>/farmercroprequests/accept/<?= $request->id ?>" class="btn-sm" style="padding: 6px 12px; background: #27ae60; color: white; text-decoration: none; border-radius: 3px; margin-right: 5px;">Accept</a>
                                    <a href="<?= ROOT ?>/farmercroprequests/reject/<?= $request->id ?>" class="btn-sm" style="padding: 6px 12px; background: #e74c3c; color: white; text-decoration: none; border-radius: 3px;" onclick="return confirm('Are you sure you want to reject this request?')">Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="padding: 40px; text-align: center; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p style="font-size: 16px; color: #666;">No crop requests available at the moment.</p>
        </div>
    <?php endif; ?>
</div>
