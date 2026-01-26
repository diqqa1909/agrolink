<?php
// Content-only view for crop request details. Rendered inside farmerMain.
?>

<div class="content-section">
    <!-- Header Section -->
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 class="content-title">Crop Request Details</h1>
            <p class="content-subtitle">Review the buyer's crop request information</p>
        </div>
        <a href="<?= ROOT ?>/farmercroprequests" class="btn btn-outline">
            Back to Requests
        </a>
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

    <!-- Details Card -->
    <div class="content-card">
        <!-- Status Badge -->
        <div style="margin-bottom: 24px;">
            <?php
            // Default to 'active' if status is empty or null
            $status = !empty($request->status) ? $request->status : 'active';
            
            $statusStyles = [
                'active' => ['bg' => '#fff3cd', 'text' => '#856404'],
                'accepted' => ['bg' => 'rgba(101, 181, 124, 0.2)', 'text' => '#499d57'],
                'declined' => ['bg' => 'rgba(244, 67, 54, 0.15)', 'text' => '#d32f2f'],
                'completed' => ['bg' => '#e0e0e0', 'text' => '#666']
            ];
            $currentStatus = $statusStyles[$status] ?? $statusStyles['active'];
            ?>
            <span style="padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; background: <?= $currentStatus['bg'] ?>; color: <?= $currentStatus['text'] ?>;">
                Status: <?= ucfirst($status) ?>
            </span>
        </div>

        <!-- Details Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 24px;">
            <!-- Crop Name -->
            <div>
                <label style="display: block; font-weight: 600; color: var(--text-light); margin-bottom: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Crop Name</label>
                <p style="font-size: 18px; color: var(--text-dark); margin: 0; font-weight: 500;"><?= htmlspecialchars($request->crop_name) ?></p>
            </div>

            <!-- Quantity -->
            <div>
                <label style="display: block; font-weight: 600; color: var(--text-light); margin-bottom: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Quantity</label>
                <p style="font-size: 18px; color: var(--text-dark); margin: 0; font-weight: 500;"><?= htmlspecialchars($request->quantity) ?> units</p>
            </div>

            <!-- Target Price -->
            <div>
                <label style="display: block; font-weight: 600; color: var(--text-light); margin-bottom: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Target Price per Unit</label>
                <p style="font-size: 18px; color: var(--primary-color); margin: 0; font-weight: 600;">Rs.<?= number_format($request->target_price, 2) ?></p>
            </div>

            <!-- Delivery Date -->
            <div>
                <label style="display: block; font-weight: 600; color: var(--text-light); margin-bottom: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Delivery Date</label>
                <p style="font-size: 18px; color: var(--text-dark); margin: 0; font-weight: 500;"><?= date('M d, Y', strtotime($request->delivery_date)) ?></p>
            </div>

            <!-- Location -->
            <div>
                <label style="display: block; font-weight: 600; color: var(--text-light); margin-bottom: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Location</label>
                <p style="font-size: 18px; color: var(--text-dark); margin: 0; font-weight: 500;"><?= htmlspecialchars($request->location) ?></p>
            </div>

            <!-- Created At -->
            <div>
                <label style="display: block; font-weight: 600; color: var(--text-light); margin-bottom: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Created On</label>
                <p style="font-size: 18px; color: var(--text-dark); margin: 0; font-weight: 500;"><?= date('M d, Y H:i', strtotime($request->created_at)) ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <?php if ($status === 'active'): ?>
            <div style="display: flex; gap: 16px; border-top: 1px solid var(--border-color); padding-top: 24px;">
                <a href="<?= ROOT ?>/farmercroprequests/accept/<?= $request->id ?>" class="btn btn-primary">
                    Accept Request
                </a>
                <a href="<?= ROOT ?>/farmercroprequests/reject/<?= $request->id ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this request?')">
                    Reject Request
                </a>
            </div>
        <?php else: ?>
            <div style="border-top: 1px solid var(--border-color); padding-top: 24px; text-align: center;">
                <p style="color: var(--text-light); font-size: 14px; margin: 0;">This request has already been <?= $request->status ?>.</p>
            </div>
        <?php endif; ?>
    </div>
</div>