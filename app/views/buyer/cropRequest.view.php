<?php
// Content-only view for crop requests. Rendered inside buyerMain which provides navbar, head, scripts.
?>

<div class="container" style="margin-top: 20px; margin-bottom: 40px;">

        <?php
        // ==================== LIST VIEW ====================
        if (isset($requests)):
        ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1>My Crop Requests</h1>
                <a href="<?= ROOT ?>/croprequest/create" class="btn btn-primary" style="padding: 10px 20px; text-decoration: none; background: #2ecc71; color: white; border-radius: 5px;">
                    + New Request
                </a>
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
                                        <a href="<?= ROOT ?>/croprequest/show/<?= $request->id ?>" class="btn-sm" style="padding: 6px 12px; background: #3498db; color: white; text-decoration: none; border-radius: 3px; margin-right: 5px;">View</a>
                                        <a href="<?= ROOT ?>/croprequest/edit/<?= $request->id ?>" class="btn-sm" style="padding: 6px 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 3px; margin-right: 5px;">Edit</a>
                                        <a href="<?= ROOT ?>/croprequest/delete/<?= $request->id ?>" class="btn-sm" style="padding: 6px 12px; background: #e74c3c; color: white; text-decoration: none; border-radius: 3px;" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <p style="font-size: 16px; color: #666; margin-bottom: 20px;">No crop requests yet. Create one to get started!</p>
                    <a href="<?= ROOT ?>/croprequest/create" class="btn btn-primary" style="padding: 10px 20px; text-decoration: none; background: #2ecc71; color: white; border-radius: 5px; display: inline-block;">
                        Create First Request
                    </a>
                </div>
            <?php endif; ?>

        <?php
        // ==================== CREATE VIEW ====================
        elseif (isset($action) && $action === 'create'):
        ?>
            <div class="centered">
            <h1 style="margin-bottom: 30px;">Create Crop Request</h1>

            <!-- Display Errors -->
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger" style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach;
                        unset($_SESSION['errors']); ?>
                    </ul>
                </div>
            <?php endif; ?>
  <!-- Form -->
            <form method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <!-- Crop Name -->
                <div style="margin-bottom: 20px;">
                    <label for="crop_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Crop Name</label>
                    <input type="text" id="crop_name" name="crop_name" value="<?= $_POST['crop_name'] ?? '' ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Quantity -->
                <div style="margin-bottom: 20px;">
                    <label for="quantity" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Quantity (units)</label>
                    <input type="number" id="quantity" name="quantity" value="<?= $_POST['quantity'] ?? '' ?>" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Target Price -->
                <div style="margin-bottom: 20px;">
                    <label for="target_price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Target Price per Unit (Rs.)</label>
                    <input type="number" id="target_price" name="target_price" value="<?= $_POST['target_price'] ?? '' ?>" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Delivery Date -->
                <div style="margin-bottom: 20px;">
                    <label for="delivery_date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Delivery Date</label>
                    <input type="date" id="delivery_date" name="delivery_date" value="<?= $_POST['delivery_date'] ?? '' ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Location -->
                <div style="margin-bottom: 20px;">
                    <label for="location" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Delivery Location</label>
                    <input type="text" id="location" name="location" value="<?= $_POST['location'] ?? '' ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" placeholder="City, Province" required>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 10px; justify-content: space-between;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; background: #2ecc71; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 14px;display: flex; align-items: center; justify-content: center;">
                        Create Request
                    </button>
                    <a href="<?= ROOT ?>/croprequest" class="btn btn-secondary" style="flex: 1; padding: 12px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; text-align: center; font-size: 14px;display: flex; align-items: center; justify-content: center;">
                        Cancel
                    </a>
                </div>
            </form>
            </div>

        <?php
        // ==================== EDIT VIEW ====================
        elseif (isset($action) && $action === 'edit'):
        ?>
            <div class="centered">
            <h1 style="margin-bottom: 30px;">Edit Crop Request</h1>

            <!-- Display Errors -->
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger" style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach;
                        unset($_SESSION['errors']); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <!-- Crop Name -->
                <div style="margin-bottom: 20px;">
                    <label for="crop_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Crop Name</label>
                    <input type="text" id="crop_name" name="crop_name" value="<?= htmlspecialchars($request->crop_name) ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Quantity -->
                <div style="margin-bottom: 20px;">
                    <label for="quantity" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Quantity (units)</label>
                    <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($request->quantity) ?>" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Target Price -->
                <div style="margin-bottom: 20px;">
                    <label for="target_price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Target Price per Unit (Rs.)</label>
                    <input type="number" id="target_price" name="target_price" value="<?= htmlspecialchars($request->target_price) ?>" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Delivery Date -->
                <div style="margin-bottom: 20px;">
                    <label for="delivery_date" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Delivery Date</label>
                    <input type="date" id="delivery_date" name="delivery_date" value="<?= htmlspecialchars($request->delivery_date) ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" required>
                </div>

                <!-- Location -->
                <div style="margin-bottom: 20px;">
                    <label for="location" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Delivery Location</label>
                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($request->location) ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;" placeholder="City, Province" required>
                </div>

                <!-- Status -->
                <div style="margin-bottom: 20px;">
                    <label for="status" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Status</label>
                    <select id="status" name="status" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box;">
                        <option value="pending" <?= $request->status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="accepted" <?= $request->status === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                        <option value="rejected" <?= $request->status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="completed" <?= $request->status === 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 10px; justify-content: space-between;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; background: #2ecc71; color: white; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                        Update Request
                    </button>
                    <a href="<?= ROOT ?>/croprequest" class="btn btn-secondary" style="flex: 1; padding: 12px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; text-align: center; font-size: 14px;display: flex; align-items: center; justify-content: center;">
                        Cancel
                    </a>
                </div>
            </form>
            </div>

        <?php
        // ==================== VIEW/SHOW VIEW ====================
        else:
        ?>
            <div class="centered">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1>Crop Request Details</h1>
                <a href="<?= ROOT ?>/croprequest" class="btn btn-secondary" style="padding: 10px 20px; text-decoration: none; background: #95a5a6; color: white; border-radius: 5px;">
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
                <div style="display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 20px;">
                    <a href="<?= ROOT ?>/croprequest/edit/<?= $request->id ?>" class="btn btn-primary" style="padding: 12px 24px; background: #f39c12; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;">
                        Edit Request
                    </a>
                    <a href="<?= ROOT ?>/croprequest/delete/<?= $request->id ?>" class="btn btn-danger" style="padding: 12px 24px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;" onclick="return confirm('Are you sure you want to delete this request?')">
                        Delete Request
                    </a>
                </div>
            </div>
            </div>
        <?php endif; ?>

    </div>

<!-- scripts are loaded by the layout -->
           