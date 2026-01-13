<?php
// Content-only view for crop requests. Rendered inside buyerLayout which provides navbar, head, scripts.
?>

<div class="container" style="margin-top: 20px; margin-bottom: 40px;">

        <?php
        // ==================== LIST VIEW ====================
        if (isset($requests)):
        ?>
            <!-- Header Section -->
            <div style="margin-bottom: 24px;">
                <h1 style="font-size: 28px; font-weight: 700; color: #2c3e50; margin: 0 0 8px 0;">My Crop Requests</h1>
                <div style="font-size: 14px; color: #7f8c8d; margin-bottom: 24px;">
                    <span>Dashboard</span> / <span style="color: #2c3e50; font-weight: 500;">My Crop Requests</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <!-- Filter: Crop Type -->
                        <select id="filterCropType" style="padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; background: white; color: #2c3e50; cursor: pointer; min-width: 150px;">
                            <option value="">All Crop Types</option>
                            <?php
                            $cropTypes = [];
                            if (is_array($requests) || is_object($requests)) {
                                foreach ($requests as $req) {
                                    if (isset($req->crop_name) && !in_array($req->crop_name, $cropTypes)) {
                                        $cropTypes[] = $req->crop_name;
                                    }
                                }
                            }
                            sort($cropTypes);
                            foreach ($cropTypes as $type):
                            ?>
                                <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <!-- Filter: Status -->
                        <select id="filterStatus" style="padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; background: white; color: #2c3e50; cursor: pointer; min-width: 130px;">
                            <option value="">All statuses</option>
                            <option value="pending">Pending</option>
                            <option value="accepted">Accepted</option>
                            <option value="completed">Completed</option>
                        </select>
                        
                        <!-- Filter: Year -->
                        <select id="filterYear" style="padding: 8px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; background: white; color: #2c3e50; cursor: pointer; min-width: 100px;">
                            <option value="">All Years</option>
                            <?php
                            $years = [];
                            foreach ($requests as $req) {
                                $year = date('Y', strtotime($req->created_at));
                                if (!in_array($year, $years)) {
                                    $years[] = $year;
                                }
                            }
                            rsort($years);
                            foreach ($years as $year):
                            ?>
                                <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <a href="<?= ROOT ?>/croprequest/create" style="padding: 10px 20px; text-decoration: none; background: #0ab627cd; color: white; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;">
                        + New Request
                    </a>
                </div>
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
                <div style="overflow-x: auto; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                                <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 13px; color: #495057; width: 40px;">
                                    <input type="checkbox" style="cursor: pointer;">
                                </th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 13px; color: #495057; cursor: pointer; user-select: none;">
                                    Submitted
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 4px; vertical-align: middle; opacity: 0.5;">
                                        <path d="M3 4.5L6 1.5L9 4.5M3 7.5L6 10.5L9 7.5"/>
                                    </svg>
                                </th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 13px; color: #495057;">Quantity</th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 13px; color: #495057;">
                                    Crop Type
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="display: inline-block; margin-left: 4px; vertical-align: middle; opacity: 0.6; cursor: help;">
                                        <circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M7 4v3M7 9h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 13px; color: #495057; cursor: pointer; user-select: none;">
                                    Delivery Date
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="display: inline-block; margin-left: 4px; vertical-align: middle; opacity: 0.6; cursor: help;">
                                        <circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M7 4v3M7 9h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 4px; vertical-align: middle; opacity: 0.5;">
                                        <path d="M3 4.5L6 1.5L9 4.5M3 7.5L6 10.5L9 7.5"/>
                                    </svg>
                                </th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 13px; color: #495057; cursor: pointer; user-select: none;">
                                    Location
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 4px; vertical-align: middle; opacity: 0.5;">
                                        <path d="M3 4.5L6 1.5L9 4.5M3 7.5L6 10.5L9 7.5"/>
                                    </svg>
                                </th>
                                <th style="padding: 16px; text-align: left; font-weight: 600; font-size: 13px; color: #495057; cursor: pointer; user-select: none;">
                                    Status
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; margin-left: 4px; vertical-align: middle; opacity: 0.5;">
                                        <path d="M3 4.5L6 1.5L9 4.5M3 7.5L6 10.5L9 7.5"/>
                                    </svg>
                                </th>
                                <th style="padding: 16px; text-align: center; font-weight: 600; font-size: 13px; color: #495057; width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): 
                                $isPending = $request->status === 'pending';
                                $rowBg = $isPending ? '#f0f9f4' : '#ffffff';
                                
                                // Status colors
                                $statusColors = [
                                    'pending' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                    'accepted' => ['bg' => '#dcfce7', 'text' => '#166534'],
                                    'completed' => ['bg' => '#e5e7eb', 'text' => '#374151']
                                ];
                                $statusColor = $statusColors[$request->status] ?? $statusColors['pending'];
                                
                                // Quantity badge color (match status)
                                $quantityColor = $isPending ? ['bg' => '#fee2e2', 'text' => '#991b1b'] : 
                                                ($request->status === 'accepted' ? ['bg' => '#dcfce7', 'text' => '#166534'] : 
                                                ['bg' => '#e5e7eb', 'text' => '#374151']);
                            ?>
                                <tr style="border-bottom: 1px solid #e9ecef; background: <?= $rowBg ?>; transition: background 0.2s;" 
                                    onmouseover="this.style.background='<?= $isPending ? '#e8f5e9' : '#f8f9fa' ?>'" 
                                    onmouseout="this.style.background='<?= $rowBg ?>'">
                                    <td style="padding: 16px;">
                                        <input type="checkbox" style="cursor: pointer;">
                                    </td>
                                    <td style="padding: 16px; font-size: 14px; color: #495057;">
                                        <?= date('d M, Y', strtotime($request->created_at)) ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; display: inline-block; background: <?= $quantityColor['bg'] ?>; color: <?= $quantityColor['text'] ?>;">
                                            <?= htmlspecialchars($request->quantity) ?> units
                                        </span>
                                    </td>
                                    <td style="padding: 16px; font-size: 14px; color: #2c3e50; font-weight: 500;">
                                        <?= htmlspecialchars($request->crop_name) ?>
                                    </td>
                                    <td style="padding: 16px; font-size: 14px; color: #495057;">
                                        <?= date('d M, Y', strtotime($request->delivery_date)) ?>
                                    </td>
                                    <td style="padding: 16px; font-size: 14px; color: #495057;">
                                        <?= htmlspecialchars($request->location) ?>
                                    </td>
                                    <td style="padding: 16px;">
                                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; display: inline-block; background: <?= $statusColor['bg'] ?>; color: <?= $statusColor['text'] ?>;">
                                            <?= ucfirst($request->status) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 16px; text-align: center;">
                                        <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                            <a href="<?= ROOT ?>/croprequest/edit/<?= $request->id ?>" 
                                               style="padding: 8px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; color: #495057; text-decoration: none; transition: all 0.2s; cursor: pointer;"
                                               onmouseover="this.style.background='#e9ecef'; this.style.color='#2c3e50';"
                                               onmouseout="this.style.background='transparent'; this.style.color='#495057';"
                                               title="Edit">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11.5 2.5a2.121 2.121 0 0 1 3 3L6.5 13.5 2.5 14.5 3.5 10.5 11.5 2.5z"/>
                                                </svg>
                                            </a>
                                            <a href="<?= ROOT ?>/croprequest/delete/<?= $request->id ?>" 
                                               onclick="return confirm('Are you sure you want to delete this request?')"
                                               style="padding: 8px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; cursor: pointer;"
                                               onmouseover="this.style.opacity='0.7';"
                                               onmouseout="this.style.opacity='1';"
                                               title="Delete">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <!-- Lid handle -->
                                                    <path d="M8 4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V5H8V4Z" fill="#E31E24"/>
                                                    
                                                    <!-- Lid -->
                                                    <rect x="4" y="5" width="16" height="2" rx="0.5" fill="#E31E24"/>
                                                    
                                                    <!-- Can body -->
                                                    <path d="M5 7H19V20C19 21.1046 18.1046 22 17 22H7C5.89543 22 5 21.1046 5 20V7Z" fill="#E31E24"/>
                                                    
                                                    <!-- Inner white space -->
                                                    <rect x="7" y="9" width="10" height="11" rx="1" fill="white"/>
                                                    
                                                    <!-- Vertical lines -->
                                                    <rect x="9" y="10" width="1.5" height="9" fill="#E31E24"/>
                                                    <rect x="11.25" y="10" width="1.5" height="9" fill="#E31E24"/>
                                                    <rect x="13.5" y="10" width="1.5" height="9" fill="#E31E24"/>
                                                </svg>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <p style="font-size: 16px; color: #666; margin-bottom: 20px;">No crop requests yet. Create one to get started!</p>
                    <a href="<?= ROOT ?>/croprequest/create" style="padding: 10px 20px; text-decoration: none; background: #2ecc71; color: white; border-radius: 5px; display: inline-block; font-weight: 600;">
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

<!-- Filter and Table Functionality -->
<?php if (isset($requests) && !empty($requests)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterCropType = document.getElementById('filterCropType');
    const filterStatus = document.getElementById('filterStatus');
    const filterYear = document.getElementById('filterYear');
    const table = document.querySelector('table');
    
    if (!table || !filterCropType || !filterStatus || !filterYear) return;
    
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    function filterTable() {
        const cropTypeValue = filterCropType.value.toLowerCase();
        const statusValue = filterStatus.value.toLowerCase();
        const yearValue = filterYear.value;
        
        rows.forEach(row => {
            let show = true;
            
            // Filter by crop type
            if (cropTypeValue) {
                const cropTypeCell = row.cells[3]; // Crop Type column
                if (cropTypeCell && !cropTypeCell.textContent.toLowerCase().includes(cropTypeValue)) {
                    show = false;
                }
            }
            
            // Filter by status
            if (statusValue) {
                const statusCell = row.cells[6]; // Status column
                if (statusCell) {
                    const statusText = statusCell.textContent.trim().toLowerCase();
                    if (statusText !== statusValue) {
                        show = false;
                    }
                }
            }
            
            // Filter by year
            if (yearValue) {
                const submittedCell = row.cells[1]; // Submitted column
                if (submittedCell) {
                    const dateText = submittedCell.textContent.trim();
                    try {
                        const dateParts = dateText.split(' ');
                        if (dateParts.length >= 3) {
                            const months = { 'jan': 0, 'feb': 1, 'mar': 2, 'apr': 3, 'may': 4, 'jun': 5, 
                                           'jul': 6, 'aug': 7, 'sep': 8, 'oct': 9, 'nov': 10, 'dec': 11 };
                            const month = months[dateParts[1].toLowerCase().substring(0, 3)];
                            const day = parseInt(dateParts[0]);
                            const year = parseInt(dateParts[2].replace(',', ''));
                            if (year.toString() !== yearValue) {
                                show = false;
                            }
                        }
                    } catch (e) {
                        // If date parsing fails, don't filter by year
                    }
                }
            }
            
            row.style.display = show ? '' : 'none';
        });
    }
    
    filterCropType.addEventListener('change', filterTable);
    filterStatus.addEventListener('change', filterTable);
    filterYear.addEventListener('change', filterTable);
    
    // Table header checkbox functionality
    const headerCheckbox = table.querySelector('thead input[type="checkbox"]');
    const rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');
    
    if (headerCheckbox && rowCheckboxes.length > 0) {
        headerCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => {
                cb.checked = headerCheckbox.checked;
            });
        });
    }
});
</script>
<?php endif; ?>

<!-- scripts are loaded by the layout -->
           