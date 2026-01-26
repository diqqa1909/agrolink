<?php
// Content-only view for crop requests. Rendered inside buyerMain which provides navbar, head, scripts.
?>

<div class="content-section">
    <?php
    // ==================== LIST VIEW ====================
    if (isset($requests)):
    ?>
        <!-- Header Section -->
        <div class="content-header">
            <h1 class="content-title">My Crop Requests</h1>
            <p class="content-subtitle">Manage your crop requests and track their status</p>
        </div>

        <!-- Filter Card -->
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Filter & Actions</h3>
                <a href="<?= ROOT ?>/croprequest/create" class="btn btn-primary">
                    + New Request
                </a>
            </div>
            <div class="card-content">
                <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                    <!-- Filter: Crop Type -->
                    <select id="filterCropType" class="form-control" style="max-width: 180px;">
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
                    <select id="filterStatus" class="form-control" style="max-width: 160px;">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="accepted">Accepted</option>
                        <option value="declined">Declined</option>
                        <option value="completed">Completed</option>
                    </select>

                    <!-- Filter: Year -->
                    <select id="filterYear" class="form-control" style="max-width: 130px;">
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
            </div>
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
                                <th style="width: 40px;">
                                    <input type="checkbox" style="cursor: pointer;">
                                </th>
                                <th>Submitted</th>
                                <th>Quantity</th>
                                <th>Crop Type</th>
                                <th>Delivery Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th style="text-align: center; width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request):
                                // Default to 'active' if status is empty or null
                                $status = !empty($request->status) ? $request->status : 'active';
                                $isActive = $status === 'active';

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
                                    <td>
                                        <input type="checkbox" style="cursor: pointer;">
                                    </td>
                                    <td><?= date('d M, Y', strtotime($request->created_at)) ?></td>
                                    <td>
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; background: var(--bg-light); color: var(--text-dark);">
                                            <?= htmlspecialchars($request->quantity) ?> units
                                        </span>
                                    </td>
                                    <td style="font-weight: 500;"><?= htmlspecialchars($request->crop_name) ?></td>
                                    <td><?= date('d M, Y', strtotime($request->delivery_date)) ?></td>
                                    <td><?= htmlspecialchars($request->location) ?></td>
                                    <td>
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; background: <?= $statusColor['bg'] ?>; color: <?= $statusColor['text'] ?>;">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                            <a href="<?= ROOT ?>/croprequest/edit/<?= $request->id ?>"
                                                class="btn btn-sm btn-outline" style="padding: 6px 10px;"
                                                title="Edit">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11.5 2.5a2.121 2.121 0 0 1 3 3L6.5 13.5 2.5 14.5 3.5 10.5 11.5 2.5z" />
                                                </svg>
                                            </a>
                                            <a href="<?= ROOT ?>/croprequest/delete/<?= $request->id ?>"
                                                onclick="return confirm('Are you sure you want to delete this request?')"
                                                class="btn btn-sm btn-danger" style="padding: 6px 10px;"
                                                title="Delete">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="content-card" style="text-align: center; padding: 60px;">
                <div style="font-size: 3rem; margin-bottom: 20px;">🌾</div>
                <h3 style="color: var(--text-dark); margin-bottom: 12px;">No Crop Requests Yet</h3>
                <p style="color: var(--text-light); margin-bottom: 24px;">Create your first crop request to get started!</p>
                <a href="<?= ROOT ?>/croprequest/create" class="btn btn-primary">
                    Create First Request
                </a>
            </div>
        <?php endif; ?>

    <?php
    // ==================== CREATE VIEW ====================
    elseif (isset($action) && $action === 'create'):
    ?>
        <div class="content-header">
            <h1 class="content-title">Create Crop Request</h1>
            <p class="content-subtitle">Submit a new request for the crops you need</p>
        </div>

        <!-- Display Errors -->
        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger" style="padding: 15px; background: rgba(244, 67, 54, 0.1); color: var(--danger-dark); border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--danger);">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 20px; list-style: disc;">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach;
                    unset($_SESSION['errors']); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="content-card">
            <form method="POST">
                <div class="form-row">
                    <!-- Crop Name -->
                    <div class="form-group">
                        <label for="crop_name">Crop Name <span class="required">*</span></label>
                        <input type="text" id="crop_name" name="crop_name" class="form-control" value="<?= $_POST['crop_name'] ?? '' ?>" placeholder="e.g., Tomatoes, Rice, Carrots" required>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group">
                        <label for="quantity">Quantity (units) <span class="required">*</span></label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="<?= $_POST['quantity'] ?? '' ?>" step="0.01" placeholder="Enter quantity" required>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Target Price -->
                    <div class="form-group">
                        <label for="target_price">Target Price per Unit (Rs.) <span class="required">*</span></label>
                        <input type="number" id="target_price" name="target_price" class="form-control" value="<?= $_POST['target_price'] ?? '' ?>" step="0.01" placeholder="Enter your target price" required>
                    </div>

                    <!-- Delivery Date -->
                    <div class="form-group">
                        <label for="delivery_date">Delivery Date <span class="required">*</span></label>
                        <input type="date" id="delivery_date" name="delivery_date" class="form-control" value="<?= $_POST['delivery_date'] ?? '' ?>" required>
                    </div>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label for="location">Delivery Location <span class="required">*</span></label>
                    <input type="text" id="location" name="location" class="form-control" value="<?= $_POST['location'] ?? '' ?>" placeholder="City, Province" required>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 16px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        Create Request
                    </button>
                    <a href="<?= ROOT ?>/croprequest" class="btn btn-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    <?php
    // ==================== EDIT VIEW ====================
    elseif (isset($action) && $action === 'edit'):
    ?>
        <div class="content-header">
            <h1 class="content-title">Edit Crop Request</h1>
            <p class="content-subtitle">Update your crop request details</p>
        </div>

        <!-- Display Errors -->
        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger" style="padding: 15px; background: rgba(244, 67, 54, 0.1); color: var(--danger-dark); border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--danger);">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 20px; list-style: disc;">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach;
                    unset($_SESSION['errors']); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="content-card">
            <form method="POST">
                <div class="form-row">
                    <!-- Crop Name -->
                    <div class="form-group">
                        <label for="crop_name">Crop Name <span class="required">*</span></label>
                        <input type="text" id="crop_name" name="crop_name" class="form-control" value="<?= htmlspecialchars($request->crop_name) ?>" required>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group">
                        <label for="quantity">Quantity (units) <span class="required">*</span></label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="<?= htmlspecialchars($request->quantity) ?>" step="0.01" required>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Target Price -->
                    <div class="form-group">
                        <label for="target_price">Target Price per Unit (Rs.) <span class="required">*</span></label>
                        <input type="number" id="target_price" name="target_price" class="form-control" value="<?= htmlspecialchars($request->target_price) ?>" step="0.01" required>
                    </div>

                    <!-- Delivery Date -->
                    <div class="form-group">
                        <label for="delivery_date">Delivery Date <span class="required">*</span></label>
                        <input type="date" id="delivery_date" name="delivery_date" class="form-control" value="<?= htmlspecialchars($request->delivery_date) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Location -->
                    <div class="form-group">
                        <label for="location">Delivery Location <span class="required">*</span></label>
                        <input type="text" id="location" name="location" class="form-control" value="<?= htmlspecialchars($request->location) ?>" placeholder="City, Province" required>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?= $request->status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="accepted" <?= $request->status === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                            <option value="declined" <?= $request->status === 'declined' ? 'selected' : '' ?>>Declined</option>
                            <option value="completed" <?= $request->status === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 16px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        Update Request
                    </button>
                    <a href="<?= ROOT ?>/croprequest" class="btn btn-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    <?php
    // ==================== VIEW/SHOW VIEW ====================
    else:
    ?>
        <div class="content-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1 class="content-title">Crop Request Details</h1>
                <p class="content-subtitle">View your crop request information</p>
            </div>
            <a href="<?= ROOT ?>/croprequest" class="btn btn-outline">
                Back to Requests
            </a>
        </div>

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
            <div style="display: flex; gap: 16px; border-top: 1px solid var(--border-color); padding-top: 24px;">
                <a href="<?= ROOT ?>/croprequest/edit/<?= $request->id ?>" class="btn btn-primary">
                    Edit Request
                </a>
                <a href="<?= ROOT ?>/croprequest/delete/<?= $request->id ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this request?')">
                    Delete Request
                </a>
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
                                    const months = {
                                        'jan': 0,
                                        'feb': 1,
                                        'mar': 2,
                                        'apr': 3,
                                        'may': 4,
                                        'jun': 5,
                                        'jul': 6,
                                        'aug': 7,
                                        'sep': 8,
                                        'oct': 9,
                                        'nov': 10,
                                        'dec': 11
                                    };
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