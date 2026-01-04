<?php
// Content-only view for crop request details. Rendered inside farmerLayout.
?>

<div class="container" style="margin-top: 20px; margin-bottom: 40px;">
    <div class="centered">
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

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>Crop Request Details</h1>
            <a href="<?= ROOT ?>/farmercroprequests" class="btn btn-secondary" style="padding: 10px 20px; text-decoration: none; background: #95a5a6; color: white; border-radius: 5px;">
                Back to Requests
            </a>
        </div>

        <!-- Details Card -->
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <!-- Status Badge -->
            <div style="margin-bottom: 30px;">
                <span style="padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600;
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
                    Status: <?= ucfirst($request->status) ?>
                </span>
            </div>

            <!-- Details Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <!-- Crop Name -->
                <div>
                    <label style="display: block; font-weight: 600; color: #666; margin-bottom: 8px; font-size: 12px; text-transform: uppercase;">Crop Name</label>
                    <p style="font-size: 18px; color: #333; margin: 0;"><?= htmlspecialchars($request->crop_name) ?></p>
                </div>

                <!-- Quantity -->
                <div>
                    <label style="display: block; font-weight: 600; color: #666; margin-bottom: 8px; font-size: 12px; text-transform: uppercase;">Quantity</label>
                    <p style="font-size: 18px; color: #333; margin: 0;"><?= htmlspecialchars($request->quantity) ?> units</p>
                </div>

                <!-- Target Price -->
                <div>
                    <label style="display: block; font-weight: 600; color: #666; margin-bottom: 8px; font-size: 12px; text-transform: uppercase;">Target Price per Unit</label>
                    <p style="font-size: 18px; color: #333; margin: 0;">Rs.<?= number_format($request->target_price, 2) ?></p>
                </div>

                <!-- Delivery Date -->
                <div>
                    <label style="display: block; font-weight: 600; color: #666; margin-bottom: 8px; font-size: 12px; text-transform: uppercase;">Delivery Date</label>
                    <p style="font-size: 18px; color: #333; margin: 0;"><?= date('M d, Y', strtotime($request->delivery_date)) ?></p>
                </div>

                <!-- Location -->
                <div>
                    <label style="display: block; font-weight: 600; color: #666; margin-bottom: 8px; font-size: 12px; text-transform: uppercase;">Location</label>
                    <p style="font-size: 18px; color: #333; margin: 0;"><?= htmlspecialchars($request->location) ?></p>
                </div>

                <!-- Created At -->
                <div>
                    <label style="display: block; font-weight: 600; color: #666; margin-bottom: 8px; font-size: 12px; text-transform: uppercase;">Created On</label>
                    <p style="font-size: 18px; color: #333; margin: 0;"><?= date('M d, Y H:i', strtotime($request->created_at)) ?></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <?php if ($request->status === 'pending'): ?>
                <div style="display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 20px;">
                    <a href="<?= ROOT ?>/farmercroprequests/accept/<?= $request->id ?>" class="btn btn-primary" style="flex: 1; padding: 12px 24px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; text-align: center;">
                        Accept Request
                    </a>
                    <a href="<?= ROOT ?>/farmercroprequests/reject/<?= $request->id ?>" class="btn btn-danger" style="flex: 1; padding: 12px 24px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; text-align: center;" onclick="return confirm('Are you sure you want to reject this request?')">
                        Reject Request
                    </a>
                </div>
            <?php else: ?>
                <div style="border-top: 1px solid #eee; padding-top: 20px; text-align: center;">
                    <p style="color: #666; font-size: 14px;">This request has already been <?= $request->status ?>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
