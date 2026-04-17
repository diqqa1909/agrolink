<!-- Transporter Dashboard Content (embedded in transporterMain.view.php) -->

<div id="dashboard-section" class="content-section">
    <div class="content-header">
        <h1 class="content-title">Dashboard Overview</h1>
        <p class="content-subtitle">Welcome, <span id="welcomeUserName"><?php echo isset($username) ? htmlspecialchars($username) : 'Transporter'; ?></span>! Here's what's happening with your deliveries.</p>
    </div>

    <?php if (!empty($profileRestriction['message'] ?? '')): ?>
        <div class="content-card" style="margin-bottom: 24px; border-left: 4px solid #f59e0b;">
            <div class="card-content" style="padding: 18px 24px; color: #7c5a03;">
                <?= htmlspecialchars($profileRestriction['message']) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="dashboard-stats" style="margin-bottom: 36px;">
        <div class="stat-card">
            <div class="stat-number" id="availableDeliveries"><?php echo isset($availableRequestsCount) ? $availableRequestsCount : 0; ?></div>
            <div class="stat-label">Available Deliveries</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="activeDeliveries"><?php echo isset($earningsSummary) ? $earningsSummary->active_deliveries : 0; ?></div>
            <div class="stat-label">Active Deliveries</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="monthlyEarnings">Rs. <?php echo isset($earningsSummary) ? number_format($earningsSummary->month_earnings, 2) : '0.00'; ?></div>
            <div class="stat-label">This Month</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="completedDeliveries"><?php echo isset($earningsSummary) ? $earningsSummary->completed_deliveries : 0; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>

    <!-- Current Status -->
    <div class="content-card" style="margin-bottom: 36px;">
        <div class="card-header">
            <h3 class="card-title">Current Status</h3>
        </div>
        <div class="card-content" style="padding: 28px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 36px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                        <div id="statusIndicator" style="width: 12px; height: 12px; border-radius: 50%; background: #4CAF50;"></div>
                        <span style="font-weight: 600;">Status: <span id="currentStatus">Available</span></span>
                    </div>
                    <div style="margin-bottom: 18px; color: #666;">
                        <strong>Current Location:</strong> <span id="currentLocation">-</span>
                    </div>
                    <div style="margin-bottom: 18px; color: #666;" id="activeVehicleInfo">
                        <strong>Vehicles:</strong>
                        <div id="activeVehicle" style="margin-top: 10px;">Loading...</div>
                    </div>
                    <div style="color: #666;">
                        <strong>Next Delivery:</strong> <span id="nextDelivery">No pending deliveries</span>
                    </div>
                </div>
                <div>
                    <button class="btn btn-primary" style="width: 100%; margin-bottom: 16px; padding: 14px;" onclick="toggleAvailability()">
                        <span id="availabilityBtn">Go Offline</span>
                    </button>
                    <button class="btn btn-secondary" style="width: 100%; margin-bottom: 16px; padding: 14px;" onclick="updateLocation()">
                        Update Location
                    </button>
                    <button class="btn btn-outline" style="width: 100%; padding: 14px;" onclick="TransporterDashboard.showSection('available-deliveries')">
                        Find Deliveries
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-top: 0;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Recent Deliveries</h3>
            </div>
            <div class="card-content" id="recentDeliveries" style="padding: 24px;">
                <div style="padding: 18px; text-align: center; color: #666;">
                    No recent deliveries
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Weekly Earnings</h3>
            </div>
            <div class="card-content" style="padding: 24px;">
                <div id="weeklyEarnings" style="font-size: 3rem; font-weight: 700; color: #65b57c; margin-bottom: 28px;">Rs. 0</div>
                <div style="font-size: 0.9rem; color: #666; line-height: 1.8;">
                    <div id="weeklyCompletedDeliveries" style="margin-bottom: 16px;">0 deliveries completed</div>
                    <div id="weeklyPendingDeliveries" style="margin-bottom: 16px;">0 deliveries pending</div>
                    <div id="weeklyRating">No rating yet</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="card-content">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <button class="btn btn-primary" onclick="TransporterDashboard.showSection('available-deliveries')" style="margin: 0;">Find Deliveries</button>
                <button class="btn btn-secondary" onclick="TransporterDashboard.showSection('mydeliveries')" style="margin: 0;">My Deliveries</button>
                <button class="btn btn-outline" onclick="TransporterDashboard.showSection('vehicle')" style="margin: 0;">Vehicle Info</button>
            </div>
        </div>
    </div>
</div>

<div id="feedback-section" class="content-section" style="display: none;">
    <div class="content-header">
        <h1 class="content-title">Reviews & Complaints</h1>
        <p class="content-subtitle">Delivery feedback from buyers based on completed trips</p>
    </div>

    <div class="dashboard-stats" style="margin-bottom: 24px;">
        <div class="stat-card">
            <div class="stat-number" id="feedbackAvgRating">0.0</div>
            <div class="stat-label">Average Rating</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="feedbackComplaintCount">0</div>
            <div class="stat-label">Complaints</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="feedbackTotalCount">0</div>
            <div class="stat-label">Total Feedback</div>
        </div>
    </div>

    <div class="transporter-feedback-list" id="feedbackUnifiedList">
        <div class="transporter-feedback-loading">
            Loading feedback...
        </div>
    </div>
</div>

<!-- Available Deliveries Section -->
<div id="available-deliveries-section" class="content-section" style="display: none;">
    <div class="content-header">
        <h1 class="content-title">Available Deliveries</h1>
        <button class="btn btn-outline btn-sm" onclick="TransporterDashboard.refreshDeliveries()">Refresh</button>
    </div>

    <div class="content-card delivery-filter-card">
        <div class="delivery-filter-row">
            <div class="transporter-filter-shell">
                <div class="transporter-filter-grid">
                    <div class="transporter-filter-group">
                        <label for="locationFilter">Pickup</label>
                        <select id="locationFilter" class="form-control transporter-filter-control">
                        <option value="">All Locations</option>
                        <option value="colombo">Colombo</option>
                        <option value="kandy">Kandy</option>
                        <option value="galle">Galle</option>
                        <option value="matale">Matale</option>
                        <option value="anuradhapura">Anuradhapura</option>
                    </select>
                    </div>
                    <div class="transporter-filter-group">
                        <label for="distanceFilter">Max Distance</label>
                        <select id="distanceFilter" class="form-control transporter-filter-control">
                        <option value="">Any Distance</option>
                        <option value="10">Within 10km</option>
                        <option value="25">Within 25km</option>
                        <option value="50">Within 50km</option>
                        <option value="100">Within 100km</option>
                    </select>
                    </div>
                    <div class="transporter-filter-group">
                        <label for="weightFilter">Max Weight</label>
                        <select id="weightFilter" class="form-control transporter-filter-control">
                        <option value="">Any Weight</option>
                        <option value="10">Up to 10kg</option>
                        <option value="25">Up to 25kg</option>
                        <option value="50">Up to 50kg</option>
                        <option value="100">Up to 100kg</option>
                    </select>
                    </div>
                    <div class="transporter-filter-group">
                        <label for="paymentFilter">Min Payment</label>
                        <select id="paymentFilter" class="form-control transporter-filter-control">
                        <option value="">Any Payment</option>
                        <option value="500">Rs. 500+</option>
                        <option value="1000">Rs. 1000+</option>
                        <option value="1500">Rs. 1500+</option>
                        <option value="2000">Rs. 2000+</option>
                    </select>
                </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card transporter-requests-card" style="margin-top: 20px;">
        <div class="card-header">
            <h3 class="card-title">Delivery Requests</h3>
        </div>
        <div class="card-content">
            <div id="availableDeliveriesList" class="transporter-request-list">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<div id="mydeliveries-section" class="content-section" style="display: none;">
    <h1 style="margin-bottom: 24px; font-size: 2rem;">My Deliveries</h1>

    <div class="content-card delivery-filter-card">
        <div class="delivery-filter-row">
            <div class="delivery-filter-group" role="tablist" aria-label="Delivery filters">
                <button type="button" class="delivery-filter-link transporter-delivery-filter-link active" data-status="all" onclick="TransporterDashboard.filterMyDeliveries('all')">All</button>
                <button type="button" class="delivery-filter-link transporter-delivery-filter-link" data-status="accepted" onclick="TransporterDashboard.filterMyDeliveries('accepted')">Accepted</button>
                <button type="button" class="delivery-filter-link transporter-delivery-filter-link" data-status="in_transit" onclick="TransporterDashboard.filterMyDeliveries('in_transit')">In Transit</button>
                <button type="button" class="delivery-filter-link transporter-delivery-filter-link" data-status="delivered" onclick="TransporterDashboard.filterMyDeliveries('delivered')">Delivered</button>
            </div>
            <div style="margin-left: auto; padding: 4px 0;">
                <select id="vehicleDeliveryFilter" class="form-control" style="min-width: 180px;" onchange="TransporterDashboard.filterMyDeliveries(getCurrentDeliveryStatusFilter(), this.value)">
                    <option value="">All Vehicles</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-container" style="padding: 24px; background: #fff; border-radius: 12px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Route</th>
                    <th>Distance</th>
                    <th>Weight</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="myDeliveriesTableBody">
            </tbody>
        </table>
    </div>
</div>

<div id="schedule-section" class="content-section" style="display: none;">
    <h1 style="margin-bottom: 24px;">Delivery Schedule</h1>

    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Upcoming (Next 3 Days)</h3>
        </div>
        <div class="card-content">
            <div id="scheduleCalendar" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>

    <div class="content-card" style="margin-top: 20px;">
        <div class="card-header">
            <h3 class="card-title">Today's Deliveries</h3>
        </div>
        <div class="card-content">
            <div id="todaySchedule">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>
</div>


<div id="earnings-section" class="content-section" style="display: none;">
    <div class="content-header">
        <h1 class="content-title">Earnings Overview</h1>
        <p class="content-subtitle">Live payout context from your completed transporter deliveries</p>
    </div>

    <div class="dashboard-stats" style="margin-bottom: 28px;">
        <div class="stat-card">
            <div class="stat-number" id="todayEarnings">Rs. 0</div>
            <div class="stat-label">Today (Delivered)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="weekEarnings">Rs. 0</div>
            <div class="stat-label">This Week (7 Days)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="monthEarningsDetail">Rs. 0</div>
            <div class="stat-label">This Month (Calendar)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="totalEarningsDetail">Rs. 0</div>
            <div class="stat-label">Lifetime Earnings</div>
        </div>
    </div>

    <div style="margin-top: 32px;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Delivery Earnings Context</h3>
            </div>
            <div class="card-content" style="padding: 28px;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Deliveries Completed:</span>
                        <span id="metricCompletedDeliveries" style="font-weight: 700; font-size: 1rem; color: #2c3e50;"><?= (int)($performanceMetrics->completed_deliveries ?? 0) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Average Rating:</span>
                        <span id="metricAverageRating" style="font-weight: 700; font-size: 1rem; color: #2c3e50;"><?= number_format((float)($performanceMetrics->average_rating ?? 0), 1) ?>/5</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">On-Time Delivery:</span>
                        <span id="metricOnTimeDelivery" style="font-weight: 700; font-size: 1rem; color: #2c3e50;"><?= number_format((float)($performanceMetrics->on_time_delivery_rate ?? 0), 0) ?>%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Customer Satisfaction:</span>
                        <span id="metricCustomerSatisfaction" style="font-weight: 700; font-size: 1rem; color: #2c3e50;"><?= number_format((float)($performanceMetrics->customer_satisfaction_rate ?? 0), 0) ?>%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Earnings per Delivery:</span>
                        <span id="metricEarningsPerDelivery" style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. <?= number_format((float)($performanceMetrics->earnings_per_delivery ?? 0), 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card" style="margin-top: 32px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title">Payment History</h3>
            <button class="btn btn-secondary btn-sm" onclick="TransporterDashboard.exportPaymentHistory()">Export CSV</button>
        </div>
        <div style="padding: 28px;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Route</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="paymentHistoryBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="vehicle-section" class="content-section" style="display: none;">
    <div class="content-header" style="display:flex; align-items:center; justify-content:flex-start; margin-bottom: 32px;">
        <h1 class="content-title" style="margin:0;">Vehicle Management</h1>
    </div>
    <div style="margin-bottom: 36px;">
        <button class="btn btn-add-product" data-modal="addVehicleModal">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Vehicle
        </button>
    </div>

    <div id="myVehiclesContainer" style="margin-bottom: 40px;">

    </div>

    <div class="card" style="margin-top: 32px;">
        <div style="padding: 24px; border-bottom: 1px solid var(--medium-gray);">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #2c3e50;">All Vehicles</h3>
        </div>
        <div style="padding: 28px;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Registration</th>
                            <th>Type</th>
                            <th style="display: none;">Capacity</th>
                            <th>Status</th>
                            <th>License No.</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vehiclesTableBody">
                    </tbody>
                </table>
                <style>
                    /* Hide capacity column (4th column) in vehicles table */
                    #vehiclesTableBody tr td:nth-child(4) {
                        display: none;
                    }
                </style>
            </div>
        </div>
    </div>
</div>



<div id="analytics-section" class="content-section" style="display: none;">
    <div class="content-header">
        <h1 class="content-title">Analytics & Performance</h1>
        <p class="content-subtitle">Track your performance metrics and delivery statistics</p>
    </div>

    <div class="dashboard-stats" style="margin-bottom: 40px;">
        <div class="stat-card">
            <div class="stat-number">127</div>
            <div class="stat-label">Total Deliveries</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">4.8</div>
            <div class="stat-label">Average Rating</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">95%</div>
            <div class="stat-label">On-Time Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">Rs. 541</div>
            <div class="stat-label">Avg per Delivery</div>
        </div>
    </div>

    <div class="grid grid-2" style="margin-top: 0; gap: 32px;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Monthly Performance</h3>
            </div>
            <div class="card-content" style="padding: 10px 0;">
                <div style="display:grid; grid-template-columns: 1fr 4fr 1fr; gap:12px; align-items:center; padding:8px 12px;">
                    <div>Oct</div>
                    <div style="background:#E8F5E9; border-radius:8px; overflow:hidden;">
                        <div style="width: 78%; background:#66BB6A; color:#fff; padding:6px 8px;">78 deliveries</div>
                    </div>
                    <div style="text-align:right; font-weight:700;">Rs. 12.5k</div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 4fr 1fr; gap:12px; align-items:center; padding:8px 12px;">
                    <div>Sep</div>
                    <div style="background:#E8F5E9; border-radius:8px; overflow:hidden;">
                        <div style="width: 64%; background:#66BB6A; color:#fff; padding:6px 8px;">64 deliveries</div>
                    </div>
                    <div style="text-align:right; font-weight:700;">Rs. 10.1k</div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 4fr 1fr; gap:12px; align-items:center; padding:8px 12px;">
                    <div>Aug</div>
                    <div style="background:#E8F5E9; border-radius:8px; overflow:hidden;">
                        <div style="width: 58%; background:#66BB6A; color:#fff; padding:6px 8px;">58 deliveries</div>
                    </div>
                    <div style="text-align:right; font-weight:700;">Rs. 9.4k</div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Popular Routes</h3>
            </div>
            <div class="card-content">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Deliveries</th>
                                <th>Avg. Earning</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Colombo → Kandy</td>
                                <td>28</td>
                                <td>Rs. 820</td>
                            </tr>
                            <tr>
                                <td>Galle → Colombo</td>
                                <td>22</td>
                                <td>Rs. 1,050</td>
                            </tr>
                            <tr>
                                <td>Matale → Gampaha</td>
                                <td>16</td>
                                <td>Rs. 940</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addVehicleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Vehicle</h3>
            <span class="modal-close" data-modal-close>&times;</span>
        </div>
        <div class="modal-body">
            <form id="addVehicleForm" enctype="multipart/form-data">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="vehicleType">Vehicle Type *</label>
                        <select id="vehicleType" name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <?php if (!empty($vehicleTypes)): ?>
                                <?php foreach ($vehicleTypes as $vType): 
                                    $slug = strtolower(str_replace(' ', '', $vType->vehicle_name));
                                ?>
                                    <option value="<?= htmlspecialchars($slug) ?>" data-type-id="<?= (int)$vType->id ?>" data-min="<?= (int)$vType->min_weight_kg ?>" data-max="<?= (int)$vType->max_weight_kg ?>"><?= htmlspecialchars($vType->vehicle_name) ?> (<?= $vType->min_weight_kg ?>-<?= $vType->max_weight_kg ?>kg)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vehicleRegistration">Registration Number *</label>
                        <input type="text" id="vehicleRegistration" name="registration" class="form-control" placeholder="WP 1234" maxlength="8" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="vehicleLicenseNumber">Vehicle License Number</label>
                        <input type="text" id="vehicleLicenseNumber" name="license_number" class="form-control" placeholder="e.g., B1234567" maxlength="50">
                        <small style="color:#666;">License plate or vehicle licence document number</small>
                    </div>
                    <div class="form-group">
                        <label for="vehicleFuelType">Fuel Type</label>
                        <select id="vehicleFuelType" name="fuel_type" class="form-control">
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="vehicleModel">Vehicle Model</label>
                        <input type="text" id="vehicleModel" name="model" class="form-control" placeholder="e.g., Toyota Hiace">
                    </div>
                    <div class="form-group">
                        <label for="vehicleImageAdd">Vehicle Photo</label>
                        <input type="file" id="vehicleImageAdd" name="vehicle_image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        <small style="color:#666;">Optional · JPG/PNG/WebP, max 5MB</small>
                    </div>
                </div>

                <div style="display: flex; gap: var(--spacing-md); margin-top: var(--spacing-lg);">
                    <button type="submit" class="btn btn-primary">Add Vehicle</button>
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initTransporterDashboard();
    });

    // Global storage for vehicle types
    let vehicleTypesData = [];

    function initTransporterDashboard() {
        const user = typeof getCurrentUser === 'function' ? getCurrentUser() : null;
        if (user && document.getElementById('transporterName')) {
            document.getElementById('transporterName').textContent = user.name || 'Transporter';
        } else if (document.getElementById('transporterName')) {
            document.getElementById('transporterName').textContent = 'Transporter';
        }

        loadVehicleTypes(); // Load vehicle types first
        loadDashboardData();
        loadAvailableDeliveries();
        loadMyDeliveries();
        loadSchedule();
        loadEarnings();
        loadProfile();
        loadVehicles();
        loadCurrentTransporterStatus();

        setupNavigation();

        addTabStyles();

        setupAddVehicleForm();

        // Get section from URL query parameter or default to dashboard
        const params = new URLSearchParams(window.location.search);
        const section = params.get('section') || 'dashboard';
        showSection(section);
    }

    function setupNavigation() {
        const menuLinks = document.querySelectorAll('.menu-link[data-section]');
        console.log('Setup navigation for', menuLinks.length, 'links');
        
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const section = this.getAttribute('data-section');
                console.log('Clicked section:', section);
                
                if (section) {
                    showSection(section);
                    
                    // Update URL without page reload
                    const url = new URL(window.location);
                    url.searchParams.set('section', section);
                    window.history.pushState({}, '', url);

                    // Remove active from ALL menu links (including Profile)
                    const allLinks = document.querySelectorAll('.menu-link');
                    console.log('Removing active from', allLinks.length, 'links');
                    allLinks.forEach(l => {
                        l.classList.remove('active');
                        console.log('Removed active from:', l.textContent.trim());
                    });
                    
                    this.classList.add('active');
                    console.log('Added active to:', this.textContent.trim());
                }
                return false;
            });
        });
    }

    function showSection(sectionName) {
        console.log('showSection called with:', sectionName);
        
        // Hide all sections
        const sections = document.querySelectorAll('.content-section');
        console.log('Found sections:', sections.length);
        sections.forEach(section => section.style.display = 'none');

        // Show target section
        const targetSection = document.getElementById(sectionName + '-section');
        console.log('Target section ID:', sectionName + '-section');
        console.log('Target section element:', targetSection);
        
        if (targetSection) {
            targetSection.style.display = 'block';
            
            // Load data when section is shown
            if (sectionName === 'available-deliveries' && typeof loadAvailableRequests === 'function') {
                loadAvailableRequests();
            } else if (sectionName === 'mydeliveries' && typeof loadMyDeliveries === 'function') {
                loadMyDeliveries();
            } else if (sectionName === 'feedback' && typeof loadFeedbackReviews === 'function') {
                loadFeedbackReviews();
            }
        } else {
            console.warn('✗ Section not found:', sectionName + '-section');
        }
    }

    function addTabStyles() {
        const style = document.createElement('style');
        style.textContent = `
                .tab-btn {
                    padding: var(--spacing-sm) var(--spacing-md);
                    border: none;
                    background: transparent;
                    cursor: pointer;
                    border-bottom: 2px solid transparent;
                    font-weight: var(--font-weight-medium);
                }
                .tab-btn.active {
                    border-bottom-color: var(--primary-green);
                    color: var(--primary-green);
                }
                .tab-btn:hover {
                    background: var(--light-gray);
                }
            `;
        document.head.appendChild(style);
    }

    function loadDashboardData() {
        // Stats will be loaded from database
        document.getElementById('availableDeliveries').textContent = '0';
        document.getElementById('activeDeliveries').textContent = '0';
        document.getElementById('monthlyEarnings').textContent = 'Rs. 0';
        document.getElementById('completedDeliveries').textContent = '0';

        document.getElementById('recentDeliveries').innerHTML = `
                <div style="padding: 18px; text-align: center; color: #666;">
                    No recent deliveries
                </div>
            `;
    }

    function loadAvailableDeliveries() {
        const container = document.getElementById('availableDeliveriesList');
        // Available deliveries will be loaded from database
        container.innerHTML = `
        if (!container) return;
        container.innerHTML = '<div style="padding:40px;text-align:center;color:#666;">Loading available deliveries...</div>';

        fetch(transporterApi('getAvailableRequests'), { credentials: 'include' })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.requests || !data.requests.length) {
                    container.innerHTML = '<div class="content-card" style="padding:40px;text-align:center;color:#666;">No delivery requests available for your vehicle capacity.</div>';
                    return;
                }
                container.style.display = 'grid';
                container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(340px, 1fr))';
                container.style.gap = '20px';
                container.innerHTML = data.requests.map(req => {
                    const route = `${req.farmer_town_name || req.farmer_district_name || 'Farm'} → ${req.buyer_town_name || req.buyer_district_name || 'Buyer'}`;
                    const weight = req.total_weight_kg ? `${Number(req.total_weight_kg).toFixed(1)} kg` : '-';
                    const payment = req.shipping_fee ? `Rs. ${Number(req.shipping_fee).toLocaleString()}` : 'TBD';
                    const distance = req.distance_km ? `${Number(req.distance_km).toFixed(1)} km` : '-';
                    return `<div class="content-card" style="padding:20px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                            <h4 style="margin:0;color:#2c3e50;">Order #${req.order_id || req.id}</h4>
                            <span class="badge">${escapeHtml(req.required_vehicle_type || 'Any')}</span>
                        </div>
                        <div style="color:#555;line-height:2;margin-bottom:16px;">
                            <div><strong>Route:</strong> ${escapeHtml(route)}</div>
                            <div><strong>Distance:</strong> ${escapeHtml(distance)}</div>
                            <div><strong>Weight:</strong> ${escapeHtml(weight)}</div>
                            <div><strong>Payment:</strong> <span style="color:#22c55e;font-weight:700;">${escapeHtml(payment)}</span></div>
                        </div>
                        <div style="display:flex;gap:10px;">
                            <button class="btn btn-primary btn-sm" onclick="showAcceptVehicleModal(${req.id}, ${req.total_weight_kg || 0})">Accept</button>
                        </div>
                    </div>`;
                }).join('');

                // Update counter
                const counter = document.getElementById('availableDeliveries');
                if (counter) counter.textContent = data.requests.length;
            })
            .catch(err => {
                console.error('Error loading available deliveries:', err);
                container.innerHTML = '<div style="padding:40px;text-align:center;color:#e53e3e;">Failed to load deliveries. Please try again.</div>';
            });
    }

    /**
     * Show a modal for the transporter to choose a vehicle before accepting a delivery.
     * @param {number} requestId  The delivery_requests.id
     * @param {number} weightKg   The order weight so we can show which vehicles qualify
     */
    function showAcceptVehicleModal(requestId, weightKg) {
        document.getElementById('acceptVehicleModal')?.remove();

        // Build vehicle options filtered by capacity
        const eligibleVehicles = vehicleTypesData.length
            ? vehicleTypesData.filter(vt => weightKg >= vt.min_weight_kg && weightKg <= vt.max_weight_kg)
            : [];

        // Get transporter's actual active vehicles from the last loaded list
        const cachedVehicles = window._cachedTransporterVehicles || [];
        const activeVehicles = cachedVehicles.filter(v => v.status === 'active');

        const vehicleOptions = activeVehicles.length
            ? activeVehicles.map(v => `<option value="${v.id}">${escapeHtml(v.model || getVehicleTypeName(v.type))} - ${escapeHtml(v.registration)} (${v.capacity}kg cap)</option>`).join('')
            : '<option value="" disabled>No active vehicles — add one first</option>';

        const modalHtml = `
            <div id="acceptVehicleModal" class="modal" style="display:flex;align-items:center;justify-content:center;" onclick="closeModalOnBackdrop(event,'acceptVehicleModal')">
                <div class="modal-content" onclick="event.stopPropagation()" style="max-width:480px;">
                    <div class="modal-header">
                        <h3>Choose Vehicle to Accept Delivery</h3>
                    </div>
                    <div class="modal-body">
                        <p style="margin-bottom:16px;color:#555;">Select the vehicle you will use for this delivery (Order Weight: <strong>${Number(weightKg).toFixed(1)} kg</strong>).</p>
                        <div class="form-group">
                            <label for="acceptVehicleSelect">Select Vehicle *</label>
                            <select id="acceptVehicleSelect" class="form-control" required>
                                <option value="">-- Choose Vehicle --</option>
                                ${vehicleOptions}
                            </select>
                        </div>
                        <div id="vehicleCapacityWarning" style="display:none;padding:10px;background:#fef3c7;border-left:4px solid #f59e0b;border-radius:4px;margin-top:10px;font-size:0.9rem;color:#92400e;"></div>
                    </div>
                    <div style="display:flex;gap:12px;padding:20px;">
                        <button class="btn btn-primary" onclick="confirmAcceptDelivery(${requestId}, ${weightKg})">Confirm Accept</button>
                        <button class="btn btn-secondary" onclick="document.getElementById('acceptVehicleModal').remove()">Cancel</button>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Validate capacity on selection change
        document.getElementById('acceptVehicleSelect').addEventListener('change', function() {
            const vehicleId = this.value;
            const warning = document.getElementById('vehicleCapacityWarning');
            if (!vehicleId) { warning.style.display = 'none'; return; }
            const vehicle = cachedVehicles.find(v => String(v.id) === String(vehicleId));
            if (!vehicle) { warning.style.display = 'none'; return; }
            if (weightKg > Number(vehicle.capacity)) {
                warning.textContent = `⚠ This vehicle's max capacity (${vehicle.capacity}kg) is less than the order weight (${Number(weightKg).toFixed(1)}kg). It may still be eligible based on vehicle type range.`;
                warning.style.display = 'block';
            } else {
                warning.style.display = 'none';
            }
        });
    }

    function confirmAcceptDelivery(requestId, weightKg) {
        const vehicleId = document.getElementById('acceptVehicleSelect')?.value;
        if (!vehicleId) {
            showNotification('Please select a vehicle', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('vehicle_id', vehicleId);

        fetch(transporterApi('acceptRequest/' + requestId), {
            method: 'POST',
            credentials: 'include',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Delivery accepted!', 'success');
                document.getElementById('acceptVehicleModal')?.remove();
                loadAvailableDeliveries();
                loadMyDeliveries();
            } else {
                showNotification(data.message || 'Failed to accept delivery', 'error');
            }
        })
        .catch(() => showNotification('Failed to accept delivery', 'error'));
    }

    function generateDeliveryCard(orderId, from, to, distance, weight, payment, priority) {
        const priorityColors = {
            'urgent': 'delivered',
            'express': 'pending',
            'normal': 'shipped'
        };
        const priorityLabels = {
            'urgent': 'URGENT',
            'express': 'EXPRESS',
            'normal': 'NORMAL'
        };

        return `
                <div class="content-card" style="margin: 0; min-width: 350px; max-width: 350px; flex-shrink: 0;">
                    <div style="padding: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <h4 style="margin: 0; color: #2c3e50; font-weight: 600;">${orderId}</h4>
                            <span class="order-status ${priorityColors[priority]}">${priorityLabels[priority]}</span>
                        </div>
                        <div style="margin-bottom: 20px; color: #666; line-height: 1.8;">
                            <div style="margin-bottom: 8px;">
                                <strong style="color: #2c3e50;">Route:</strong> ${from} → ${to}
                            </div>
                            <div style="margin-bottom: 8px;">
                                <strong style="color: #2c3e50;">Distance:</strong> ${distance}
                            </div>
                            <div style="margin-bottom: 8px;">
                                <strong style="color: #2c3e50;">Weight:</strong> ${weight}
                            </div>
                            <div>
                                <strong style="color: #2c3e50;">Payment:</strong> <span style="color: #65b57c; font-weight: 600;">${payment}</span>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-primary btn-sm" onclick="acceptDelivery('${orderId}')">Accept</button>
                            <button class="btn btn-outline btn-sm" onclick="viewDeliveryDetails('${orderId}')">Details</button>
                        </div>
                    </div>
                </div>
            `;
    }

    function loadMyDeliveries(status, vehicleId) {
        const tbody = document.getElementById('myDeliveriesTableBody');
        if (tbody) tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:24px; color:#666;">Loading...</td></tr>';

        let url = transporterApi('getMyRequests');
        const params = [];
        if (status && status !== 'all') {
            params.push('status=' + encodeURIComponent(status));
        }
        if (vehicleId && parseInt(vehicleId) > 0) {
            params.push('vehicle_id=' + encodeURIComponent(vehicleId));
        }
        if (params.length) url += '?' + params.join('&');

        fetch(url, { credentials: 'include' })
            .then(response => response.json())
            .then(data => {
                const requests = data.success ? (data.requests || []) : [];
                if (!tbody) return;
                if (!requests.length) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:40px 20px; color:#666;">No deliveries found</td></tr>';
                    return;
                }
                tbody.innerHTML = requests.map(req => {
                    const route = `${req.farmer_town_name || req.farmer_district_name || '-'} → ${req.buyer_town_name || req.buyer_district_name || '-'}`;
                    const statusText = String(req.status || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    const payment = req.shipping_fee ? `Rs. ${Number(req.shipping_fee).toLocaleString()}` : 'TBD';
                    const vehicleLabel = req.vehicle_model || req.vehicle_registration || '-';
                    return `<tr>
                        <td>#${req.order_id || req.id}</td>
                        <td>${escapeHtml(route)}</td>
                        <td>${req.distance_km ? Number(req.distance_km).toFixed(1) + ' km' : '-'}</td>
                        <td>${req.total_weight_kg ? Number(req.total_weight_kg).toFixed(1) + ' kg' : '-'}</td>
                        <td>${payment}</td>
                        <td><span class="badge">${escapeHtml(statusText)}</span></td>
                        <td>${req.expected_delivery_at ? new Date(req.expected_delivery_at).toLocaleDateString() : '-'}</td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                ${req.status === 'accepted' ? `<button class="btn btn-sm btn-primary" onclick="updateDeliveryStatusAction(${req.id}, 'in_transit')">Start Transit</button>` : ''}
                                ${req.status === 'in_transit' ? `<button class="btn btn-sm btn-success" onclick="updateDeliveryStatusAction(${req.id}, 'delivered')">Mark Delivered</button>` : ''}
                                <button class="btn btn-sm btn-outline" onclick="viewRequestDetails(${req.id})">Details</button>
                            </div>
                        </td>
                    </tr>`;
                }).join('');
            })
            .catch(error => {
                console.error('Error loading deliveries:', error);
                if (tbody) tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:24px; color:#e53e3e;">Failed to load deliveries</td></tr>';
            });
    }

    function loadSchedule() {
        const calendar = document.getElementById('scheduleCalendar');
        const today = new Date();
        const options = {
            weekday: 'short',
            month: 'short',
            day: 'numeric'
        };

        const next3Days = [0, 1, 2].map(offset => {
            const d = new Date(today);
            d.setDate(today.getDate() + offset);
            return d;
        });

        calendar.innerHTML = next3Days.map((date, idx) => `
                <div style="padding: 16px; background: #ffffff; border: 2px solid #e0e0e0; border-radius: 12px;">
                    <div style="font-weight: 700; margin-bottom: 10px; color: #2c3e50;">${date.toLocaleDateString(undefined, options)}</div>
                    <div style="display: grid; gap: 10px;">
                        <div style="padding: 10px; background:#f8f9fa; border-radius:8px;">
                            <div style="font-weight:600; color:#2c3e50;">08:30 AM • Pickup</div>
                            <div style="color:#666; font-size:0.9rem;">Order #ORD-2025-00${7+idx} • ${idx === 0 ? 'Colombo' : idx === 1 ? 'Matale' : 'Galle'}</div>
                        </div>
                        <div style="padding: 10px; background:#f8f9fa; border-radius:8px;">
                            <div style="font-weight:600; color:#2c3e50;">01:45 PM • Delivery</div>
                            <div style="color:#666; font-size:0.9rem;">Order #ORD-2025-00${6+idx} • ${idx === 0 ? 'Kandy' : idx === 1 ? 'Kurunegala' : 'Colombo'}</div>
                        </div>
                    </div>
                </div>
            `).join('');

        document.getElementById('todaySchedule').innerHTML = `
                <div style="padding: 20px; background: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; margin-bottom: 16px;">
                    <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">9:00 AM - Pickup</div>
                    <div style="color: #666;">Order #ORD-2025-007 - Colombo Farm</div>
                </div>
                <div style="padding: 20px; background: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px;">
                    <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">2:00 PM - Delivery</div>
                    <div style="color: #666;">Order #ORD-2025-006 - Kandy Market</div>
                </div>
            `;
    }

    function loadProfile() {
        const user = getCurrentUser();
        const uname = (window.USER_NAME || 'Transporter').trim() || 'Transporter';
        const uemail = (window.USER_EMAIL || '').trim();

        // Update profile photo if it exists on this page
        const profilePhoto = document.getElementById('profilePhoto');
        const displayName = document.getElementById('displayProfileName');

        if (profilePhoto) {
            const encoded = encodeURIComponent(uname);
            profilePhoto.src = `https://ui-avatars.com/api/?name=${encoded}&background=4CAF50&color=fff&size=150`;
        }
        if (displayName) {
            displayName.textContent = uname;
        }

        // Populate form fields only if they exist (profile page only)
        const profileNameEl = document.getElementById('profileName');
        const profileEmailEl = document.getElementById('profileEmail');
        const profilePhoneEl = document.getElementById('profilePhone');

        if (profileNameEl && profileEmailEl && profilePhoneEl) {
            if (user) {
                profileNameEl.value = user.name || uname;
                profileEmailEl.value = user.email || uemail;
                profilePhoneEl.value = user.phone || '';
            } else {
                profileNameEl.value = uname;
                profileEmailEl.value = uemail || '';
            }
        }
    }

    function uploadPhoto() {
        let input = document.getElementById('photoUploadInput');
        if (!input) {
            input = document.createElement('input');
            input.type = 'file';
            input.id = 'photoUploadInput';
            input.accept = 'image/*';
            input.style.display = 'none';
            document.body.appendChild(input);
        }

        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    showNotification('Please select a valid image file', 'error');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('Image size should be less than 5MB', 'error');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const profilePhoto = document.getElementById('profilePhoto');
                    if (profilePhoto) {
                        profilePhoto.src = ev.target.result;
                        showNotification('Photo uploaded successfully!', 'success');
                    }
                };
                reader.onerror = function() {
                    showNotification('Failed to read image file', 'error');
                };
                reader.readAsDataURL(file);
            }
        };

        input.click();
    }

    function updateProfile() {
        const name = document.getElementById('profileName')?.value?.trim();
        const email = document.getElementById('profileEmail')?.value?.trim();
        const phone = document.getElementById('profilePhone')?.value?.trim();

        if (!name || !email || !phone) {
            showNotification('Please fill all required fields', 'error');
            return;
        }
        showNotification('Profile updated successfully!', 'success');
    }

    function toggleAvailability() {
        const btn = document.getElementById('availabilityBtn');
        const status = document.getElementById('currentStatus');
        const indicator = document.getElementById('statusIndicator');

        if (status.textContent === 'Available') {
            status.textContent = 'Offline';
            btn.textContent = 'Go Online';
            indicator.style.background = '#f44336';
            showNotification('You are now offline', 'info');
        } else {
            status.textContent = 'Available';
            btn.textContent = 'Go Offline';
            indicator.style.background = '#4CAF50';
            showNotification('You are now available for deliveries', 'success');
        }
    }

    function updateLocation() {
        const existingModal = document.getElementById('updateLocationModal');
        if (existingModal) {
            existingModal.remove();
        }

        const modalHtml = `
            <div id="updateLocationModal" class="modal" style="display:flex;align-items:center;justify-content:center;" onclick="closeModalOnBackdrop(event, 'updateLocationModal')">
                <div class="modal-content" onclick="event.stopPropagation()">
                    <div class="modal-header">
                        <h3>Update Current Location</h3>
                    </div>
                    <div class="modal-body">
                        <form id="updateLocationForm" onsubmit="submitLocationUpdate(event)">
                            <div class="form-group">
                                <label for="currentDistrictSelect">District *</label>
                                <select id="currentDistrictSelect" class="form-control" required>
                                    <option value="">Select District</option>
                                    <?php foreach (($districts ?? []) as $district): ?>
                                        <option value="<?= (int)$district->id ?>"><?= esc($district->district_name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="currentTownSelect">Town / City *</label>
                                <select id="currentTownSelect" class="form-control" required disabled>
                                    <option value="">Select Town / City</option>
                                </select>
                            </div>
                            <div style="display:flex; gap: 16px; margin-top: 20px;">
                                <button type="submit" class="btn btn-primary">Save Location</button>
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('updateLocationModal').remove()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const districtSelect = document.getElementById('currentDistrictSelect');
        if (districtSelect) {
            districtSelect.addEventListener('change', function() {
                loadLocationTowns(this.value);
            });
        }
    }

    function loadLocationTowns(districtId, selectedTownId = '') {
        const townSelect = document.getElementById('currentTownSelect');
        if (!townSelect) return;

        if (!districtId) {
            townSelect.innerHTML = '<option value="">Select Town / City</option>';
            townSelect.disabled = true;
            return;
        }

        townSelect.disabled = true;
        townSelect.innerHTML = '<option value="">Loading towns...</option>';

        fetch(`<?= ROOT ?>/location/towns/${districtId}`, { credentials: 'include' })
            .then(response => response.json())
            .then(data => {
                const towns = Array.isArray(data.towns) ? data.towns : [];
                townSelect.innerHTML = '<option value="">Select Town / City</option>' +
                    towns.map(town => `<option value="${town.id}" ${String(selectedTownId) === String(town.id) ? 'selected' : ''}>${escapeHtml(town.town_name)}</option>`).join('');
                townSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading towns:', error);
                townSelect.innerHTML = '<option value="">Select Town / City</option>';
                townSelect.disabled = false;
                showNotification('Failed to load towns', 'error');
            });
    }

    function submitLocationUpdate(event) {
        event.preventDefault();

        const districtId = document.getElementById('currentDistrictSelect')?.value;
        const townId = document.getElementById('currentTownSelect')?.value;
        if (!districtId || !townId) {
            showNotification('Please select both district and town', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('district_id', districtId);
        formData.append('town_id', townId);

        fetch(transporterApi('updateCurrentLocation'), {
            method: 'POST',
            credentials: 'include',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                document.getElementById('updateLocationModal')?.remove();
                loadCurrentTransporterStatus();
            } else {
                showNotification(data.message || 'Failed to update location', 'error');
            }
        })
        .catch(error => {
            console.error('Location update error:', error);
            showNotification('Failed to update location', 'error');
        });
    }

    function updateDeliveryStatusAction(requestId, newStatus) {
        if (!confirm(`Mark this delivery as ${newStatus.replace(/_/g, ' ')}?`)) return;
        fetch(transporterApi('updateDeliveryStatus/' + requestId), {
            method: 'POST',
            credentials: 'include',
            body: new URLSearchParams({ status: newStatus })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Status updated', 'success');
                const status = getCurrentDeliveryStatusFilter();
                loadMyDeliveries(status === 'all' ? null : status);
            } else {
                showNotification(data.message || 'Failed to update status', 'error');
            }
        })
        .catch(() => showNotification('Failed to update status', 'error'));
    }

    function viewRequestDetails(requestId) {
        fetch(transporterApi('getRequestDetails/' + requestId), { credentials: 'include' })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.request) { showNotification('Details not found', 'error'); return; }
                const req = data.request;
                const route = `${req.farmer_town_name || req.farmer_district_name || '-'} → ${req.buyer_town_name || req.buyer_district_name || '-'}`;
                const modalHtml = `
                    <div id="requestDetailsModal" class="modal" style="display:flex;align-items:center;justify-content:center;" onclick="closeModalOnBackdrop(event,'requestDetailsModal')">
                        <div class="modal-content" onclick="event.stopPropagation()" style="max-width:600px;">
                            <div class="modal-header"><h3>Delivery #${req.order_id || req.id}</h3></div>
                            <div class="modal-body">
                                <p><strong>Route:</strong> ${escapeHtml(route)}</p>
                                <p><strong>Weight:</strong> ${req.total_weight_kg ? Number(req.total_weight_kg).toFixed(1) + ' kg' : '-'}</p>
                                <p><strong>Payment:</strong> ${req.shipping_fee ? 'Rs. ' + Number(req.shipping_fee).toLocaleString() : 'TBD'}</p>
                                <p><strong>Status:</strong> ${escapeHtml(String(req.status || '').replace(/_/g,' '))}</p>
                                <p><strong>Vehicle:</strong> ${escapeHtml(req.vehicle_model || req.vehicle_registration || '-')}</p>
                                <p><strong>Expected Delivery:</strong> ${req.expected_delivery_at ? new Date(req.expected_delivery_at).toLocaleString() : '-'}</p>
                            </div>
                            <div style="display:flex;gap:12px;padding:20px;">
                                <button class="btn btn-secondary" onclick="document.getElementById('requestDetailsModal').remove()">Close</button>
                            </div>
                        </div>
                    </div>`;
                document.getElementById('requestDetailsModal')?.remove();
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            })
            .catch(() => showNotification('Failed to load details', 'error'));
    }

    function refreshDeliveries() {
        showNotification('Refreshing available deliveries...', 'info');
        setTimeout(() => {
            loadAvailableDeliveries();
            showNotification('Deliveries refreshed', 'success');
        }, 1000);
    }

    function filterMyDeliveries(status, vehicleId) {
        // Update tab active state
        document.querySelectorAll('.transporter-delivery-filter-link').forEach(btn => btn.classList.remove('active'));
        const activeBtn = document.querySelector(`.transporter-delivery-filter-link[data-status="${status}"]`);
        if (activeBtn) activeBtn.classList.add('active');

        // If no vehicleId passed, use the vehicle filter select
        if (vehicleId === undefined) {
            vehicleId = document.getElementById('vehicleDeliveryFilter')?.value || '';
        }

        loadMyDeliveries(status, vehicleId);
    }

    function getCurrentDeliveryStatusFilter() {
        const active = document.querySelector('.transporter-delivery-filter-link.active');
        return active ? active.dataset.status : 'all';
    }

    function updateDeliveryStatus(orderId) {
        showNotification('Delivery status update modal will be implemented', 'info');
    }

    function exportPaymentHistory() {
        showNotification('Exporting payment history...', 'info');
    }

    function loadEarnings() {
        // Earnings will be loaded from database
        const today = 0;
        const week = 0;
        const month = 0;
        const total = 0;

        const el = id => document.getElementById(id);
        if (el('todayEarnings')) el('todayEarnings').textContent = `Rs. ${today.toLocaleString()}`;
        if (el('weekEarnings')) el('weekEarnings').textContent = `Rs. ${week.toLocaleString()}`;
        if (el('monthEarningsDetail')) el('monthEarningsDetail').textContent = `Rs. ${month.toLocaleString()}`;
        if (el('totalEarningsDetail')) el('totalEarningsDetail').textContent = `Rs. ${total.toLocaleString()}`;

        // Payment history will be loaded from database
        const body = document.getElementById('paymentHistoryBody');
        if (body) {
            body.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px 20px; color: #666;">
                            No payment history yet
                        </td>
                    </tr>
                `;
        }
    }

    function loadFeedbackReviews() {
        fetch(transporterApi('getFeedbackReviews'), { credentials: 'include' })
            .then(response => response.json())
            .then(data => {
                const reviews = (data.success && Array.isArray(data.reviews)) ? data.reviews : [];
                const unifiedEl = document.getElementById('feedbackUnifiedList');
                const totalEl = document.getElementById('feedbackTotalCount');
                const avgEl = document.getElementById('feedbackAvgRating');
                const complaintsCountEl = document.getElementById('feedbackComplaintCount');

                const complaints = reviews.filter(review => String(review.complaint_status || 'none') !== 'none' || String(review.complaint_text || '').trim() !== '');
                const avg = reviews.length ? (reviews.reduce((sum, review) => sum + Number(review.rating || 0), 0) / reviews.length) : 0;

                if (totalEl) totalEl.textContent = String(reviews.length);
                if (avgEl) avgEl.textContent = avg.toFixed(1);
                if (complaintsCountEl) complaintsCountEl.textContent = String(complaints.length);

                const satisfactionLabels = {
                    very_satisfied: 'Very satisfied',
                    satisfied: 'Satisfied',
                    neutral: 'Neutral',
                    dissatisfied: 'Dissatisfied',
                    very_dissatisfied: 'Very dissatisfied'
                };

                const renderCard = (item) => {
                    const rating = Number(item.rating || 0);
                    const hasComplaint = String(item.complaint_status || 'none') !== 'none' || String(item.complaint_text || '').trim() !== '';
                    const stars = `${'★'.repeat(rating)}${'☆'.repeat(Math.max(0, 5 - rating))}`;
                    const reviewerName = escapeHtml(item.reviewer_name || 'Reviewer');
                    const reviewerRole = String(item.reviewer_type || 'buyer').toLowerCase();
                    const reviewerLabel = reviewerRole === 'farmer' ? 'Farmer' : 'Buyer';
                    const reviewerInitial = reviewerName.charAt(0).toUpperCase();
                    const created = item.created_at ? new Date(item.created_at).toLocaleDateString() : '';
                    const orderText = item.order_id ? `Order #${item.order_id}` : 'Order #-';
                    const onTimeText = Number(item.on_time_flag) === 1 ? 'On time' : 'Delayed';
                    const satisfactionText = satisfactionLabels[item.satisfaction_status] || 'Neutral';
                    const routeText = item.distance_km ? `${Number(item.distance_km).toFixed(1)} km route` : 'Distance unavailable';

                    return `
                        <div class="review-card" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px; gap: 12px;">
                                <div style="display: flex; gap: 16px;">
                                    <div style="width: 50px; height: 50px; background: #e3f2fd; color: #2196F3; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem;">${reviewerInitial}</div>
                                    <div>
                                        <h4 style="margin: 0 0 4px 0;">${orderText}</h4>
                                        <p style="margin: 0; color: #666; font-size: 0.9rem;">${reviewerLabel} feedback by <span style="font-weight: 500;">${reviewerName}</span></p>
                                        <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: #999;">${created}</p>
                                    </div>
                                </div>
                                <div class="star-rating" style="color: #ff9800; font-size: 1.2rem; text-align: right;">
                                    ${stars}
                                    ${hasComplaint ? '<div style="font-size: 0.72rem; margin-top: 4px; color: #b91c1c;">Complaint</div>' : ''}
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 12px;">
                                <span style="padding: 6px 10px; background: #f3f4f6; border-radius: 999px; font-size: 0.82rem; color: #4b5563;">${onTimeText}</span>
                                <span style="padding: 6px 10px; background: #f3f4f6; border-radius: 999px; font-size: 0.82rem; color: #4b5563;">${satisfactionText}</span>
                                <span style="padding: 6px 10px; background: #f3f4f6; border-radius: 999px; font-size: 0.82rem; color: #4b5563;">${routeText}</span>
                            </div>
                            <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin-top: 12px; border-left: 3px solid ${hasComplaint ? '#ef4444' : '#10b981'};">
                                <p style="margin: 0; color: #444; line-height: 1.5;">"${escapeHtml(item.review_text || '')}"</p>
                                ${hasComplaint ? `<div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1d5d5; color: #991b1b;"><strong>Complaint:</strong> ${escapeHtml(item.complaint_text || '')}<br><span style="font-size: 0.82rem;">Status: ${escapeHtml(String(item.complaint_status || 'open').replace(/_/g, ' '))}</span></div>` : ''}
                            </div>
                        </div>
                    `;
                };

                if (unifiedEl) {
                    unifiedEl.innerHTML = reviews.length
                        ? `<div class="reviews-grid" style="display: grid; gap: 16px;">${reviews.map(renderCard).join('')}</div>`
                        : '<div style="padding: 30px; text-align: center; color: #666;">No feedback yet</div>';
                }
            })
            .catch(error => {
                console.error('Error loading feedback reviews:', error);
            });
    }

    function previousWeek() {
        showNotification('Loading previous week...', 'info');
    }

    function nextWeek() {
        showNotification('Loading next week...', 'info');
    }

    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            document.getElementById(modalId)?.remove();
        }
    }

    if (typeof getCurrentUser !== 'function') {
        function getCurrentUser() {
            return null;
        }
    }

    if (typeof showNotification !== 'function') {
        function showNotification(msg, type) {
            alert(msg);
        }
    }

    if (typeof closeModal !== 'function') {
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'none';
        }
    }

    function getTransporterApiBase() {
        const origin = String(window.location.origin || '').replace(/\/+$/, '');
        const path = String(window.location.pathname || '');
        const publicMatch = path.match(/^(.*\/public)(?:\/|$)/i);
        if (publicMatch && publicMatch[1]) {
            return origin + publicMatch[1];
        }

        const appRoot = String(window.APP_ROOT || '').replace(/\/+$/, '');
        if (appRoot) {
            return appRoot;
        }

        return origin;
    }

    function transporterApi(path) {
        const cleanPath = String(path || '').replace(/^\/+/, '');
        return `${getTransporterApiBase()}/transporterdashboard/${cleanPath}`;
    }

    function loadVehicles() {
        fetch(transporterApi('getVehicles'))
            .then(response => response.json())
            .then(data => {
                const vehicles = (data.success && Array.isArray(data.vehicles)) ? data.vehicles : [];
                // Cache globally for the Accept modal
                window._cachedTransporterVehicles = vehicles;

                // Populate the vehicle delivery filter dropdown
                const vehicleFilter = document.getElementById('vehicleDeliveryFilter');
                if (vehicleFilter) {
                    vehicleFilter.innerHTML = '<option value="">All Vehicles</option>' +
                        vehicles.map(v => `<option value="${v.id}">${escapeHtml(v.model || getVehicleTypeName(v.type))} - ${escapeHtml(v.registration)}</option>`).join('');
                }

                if (vehicles.length > 0) {
                    displayVehicles(vehicles);
                    updateCurrentStatus(vehicles);
                } else {
                    displayVehicles([]);
                    updateCurrentStatus([]);
                }
            })
            .catch(error => {
                console.error('Error loading vehicles:', error);
                showNotification('Failed to load vehicles', 'error');
                displayVehicles([]);
                updateCurrentStatus([]);
            });
    }

    function loadCurrentTransporterStatus() {
        Promise.all([
            fetch(`<?= ROOT ?>/transporterprofile?ajax=1&t=${Date.now()}`, { credentials: 'include' }).then(response => response.json()),
            fetch(transporterApi('getMyRequests?status=accepted'), { credentials: 'include' }).then(response => response.json())
        ])
            .then(([profileResponse, deliveriesResponse]) => {
                const profile = profileResponse.success ? (profileResponse.profile || {}) : {};
                const deliveries = deliveriesResponse.success ? (deliveriesResponse.requests || []) : [];

                const currentLocation = document.getElementById('currentLocation');
                const currentStatus = document.getElementById('currentStatus');
                const statusIndicator = document.getElementById('statusIndicator');
                const nextDelivery = document.getElementById('nextDelivery');

                if (currentLocation) {
                    const districtName = profile.current_district_name || profile.district_name || profile.district || '';
                    const townName = profile.current_town_name || profile.town_name || profile.city || '';
                    currentLocation.textContent = [townName, districtName].filter(Boolean).join(', ') || '-';
                }

                if (currentStatus) {
                    const availability = String(profile.availability || 'available').toLowerCase();
                    currentStatus.textContent = availability === 'available' ? 'Available' : availability === 'busy' ? 'Busy' : 'Offline';
                    if (statusIndicator) {
                        statusIndicator.style.background = availability === 'available'
                            ? '#4CAF50'
                            : availability === 'busy'
                                ? '#f59e0b'
                                : '#f44336';
                    }
                }

                if (nextDelivery) {
                    const next = deliveries[0] || null;
                    nextDelivery.textContent = next
                        ? `Order #${next.order_id} · ${(next.farmer_town_name || next.farmer_city || next.farmer_district_name || 'Pickup')} to ${(next.buyer_town_name || next.buyer_city || next.buyer_district_name || 'Delivery')}`
                        : 'No pending deliveries';
                }
            })
            .catch(error => {
                console.error('Error loading current transporter status:', error);
            });
    }

    function updateCurrentStatus(vehicles) {
        const activeVehicleSpan = document.getElementById('activeVehicle');

        if (!vehicles || vehicles.length === 0) {
            activeVehicleSpan.innerHTML = '<div style="color: #666;">No vehicles added</div>';
            return;
        }

        activeVehicleSpan.innerHTML = vehicles.map(vehicle => {
            const vehicleName = escapeHtml(vehicle.model || getVehicleTypeName(vehicle.type));
            const registration = escapeHtml(vehicle.registration || 'N/A');
            const statusText = escapeHtml(String(vehicle.status || 'inactive').replace(/_/g, ' '));
            const isSelected = Number(vehicle.is_selected || 0) === 1;
            const canSelect = String(vehicle.status || '') === 'active' && !isSelected;

            return `
                <div style="display:flex; justify-content:space-between; align-items:center; gap: 14px; padding: 12px 14px; border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 10px; background: ${isSelected ? '#f0fdf4' : '#fff'};">
                    <div>
                        <div style="font-weight: 600; color: #2c3e50;">${vehicleName}</div>
                        <div style="font-size: 0.88rem; color: #666;">${registration} · ${statusText.charAt(0).toUpperCase() + statusText.slice(1)}</div>
                    </div>
                    <button class="btn btn-sm ${isSelected ? 'btn-secondary' : 'btn-outline'}" ${canSelect ? '' : 'disabled'} onclick="selectDeliveryVehicle(${vehicle.id})">
                        ${isSelected ? 'Selected' : 'Select Vehicle'}
                    </button>
                </div>
            `;
        }).join('');
    }

    function selectDeliveryVehicle(vehicleId) {
        fetch(transporterApi(`selectVehicle/${vehicleId}`), {
            method: 'POST',
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Vehicle selected successfully', 'success');
                    loadVehicles();
                    loadCurrentTransporterStatus();
                } else {
                    showNotification(data.message || 'Failed to select vehicle', 'error');
                }
            })
            .catch(error => {
                console.error('Vehicle selection error:', error);
                showNotification('Failed to select vehicle', 'error');
            });
    }

    function displayVehicles(vehicles) {
        const container = document.getElementById('myVehiclesContainer');
        const tbody = document.getElementById('vehiclesTableBody');

        if (!vehicles || vehicles.length === 0) {
            container.innerHTML = `
                    <div class="content-card">
                        <div style="padding: 60px 20px; text-align: center; color: #666;">
                            <div style="font-size: 4rem; margin-bottom: 20px;">🚗</div>
                            <h3 style="color: #2c3e50; margin-bottom: 12px;">No Vehicles Yet</h3>
                            <p>Click "Add Vehicle" button to add your first vehicle.</p>
                        </div>
                    </div>
                `;

            tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 60px 20px; color: #666;">
                            No vehicles added yet. Click "Add Vehicle" to get started.
                        </td>
                    </tr>
                `;
            return;
        }

        container.innerHTML = vehicles.map(vehicle => {
            const statusText = vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1);
            const vehicleIcon = getVehicleIcon(vehicle.type);
            const vehicleTypeName = getVehicleTypeName(vehicle.type);
            const licenseVerified = Number(vehicle.license_verified || 0) === 1;
            const verifiedBadge = licenseVerified
                ? '<span style="display:inline-block;padding:2px 8px;background:#d1fae5;color:#065f46;border-radius:999px;font-size:0.78rem;font-weight:600;margin-left:6px;">✓ Verified</span>'
                : '<span style="display:inline-block;padding:2px 8px;background:#fef3c7;color:#92400e;border-radius:999px;font-size:0.78rem;font-weight:600;margin-left:6px;">Pending</span>';
            const vehicleImageHtml = vehicle.vehicle_image
                ? `<img src="<?= ROOT ?>/public/assets/images/vehicles/${escapeHtml(vehicle.vehicle_image)}" alt="Vehicle" style="width:100%;height:160px;object-fit:cover;border-radius:10px;margin-bottom:12px;">`
                : `<div style="font-size:4rem;margin-bottom:16px;">${vehicleIcon}</div>`;

            return `
                    <div class="content-card" style="margin-bottom: 24px;">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="card-title">${escapeHtml(vehicle.model || vehicleTypeName)}</h3>
                        </div>
                        <div class="card-content">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                <div>
                                    <div style="margin-bottom: 20px; line-height: 2.2;">
                                        <strong>Vehicle Type:</strong> ${vehicleTypeName}<br>
                                        <strong>Registration:</strong> ${escapeHtml(vehicle.registration)}<br>
                                        <strong>License No.:</strong> ${escapeHtml(vehicle.license_number || '-')} ${verifiedBadge}<br>
                                        <strong>Capacity:</strong> ${escapeHtml(vehicle.capacity)}kg<br>
                                        <strong>Fuel Type:</strong> ${escapeHtml(vehicle.fuel_type || 'N/A')}<br>
                                        <strong>Status:</strong> <span class="badge">${statusText}</span><br>
                                        <strong>Selected:</strong> ${Number(vehicle.is_selected || 0) === 1 ? 'Yes' : 'No'}
                                    </div>
                                    <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 20px;">
                                        ${vehicle.status !== 'active' ? `<button class="btn btn-primary" onclick="setActiveVehicle(${vehicle.id})">Set as Active</button>` : ''}
                                        ${vehicle.status === 'active' && Number(vehicle.is_selected || 0) !== 1 ? `<button class="btn btn-secondary" onclick="selectDeliveryVehicle(${vehicle.id})">Select Vehicle</button>` : ''}
                                        <button class="btn btn-outline" onclick="editVehicleModal(${vehicle.id})">Edit</button>
                                        <button class="btn btn-outline" onclick="uploadVehicleImageUI(${vehicle.id})">Upload Photo</button>
                                        <button class="btn btn-outline" onclick="deleteVehicleConfirm(${vehicle.id})">Delete</button>
                                    </div>
                                </div>
                                <div>
                                    <div style="background: #f8f9fa; border-radius: 12px; padding: 24px; text-align: center;">
                                        ${vehicleImageHtml}
                                        <div style="font-weight: 600; margin-bottom: 12px; color: #2c3e50;">${escapeHtml(vehicle.model || vehicleTypeName)}</div>
                                        <div style="color: #666; margin-bottom: 20px;">
                                            ${vehicle.status === 'active' ? 'Available for delivery' : vehicle.status === 'maintenance' ? 'Under maintenance' : 'Not available'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
        }).join('');

        tbody.innerHTML = vehicles.map(vehicle => {
            const statusText = vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1);
            const licenseVerified = Number(vehicle.license_verified || 0) === 1;

            return `
                    <tr>
                        <td>${escapeHtml(vehicle.model || 'N/A')}</td>
                        <td>${escapeHtml(vehicle.registration)}</td>
                        <td>${getVehicleTypeName(vehicle.type)}</td>
                        <td>${escapeHtml(vehicle.capacity)}kg</td>
                        <td><span class="badge">${statusText}${Number(vehicle.is_selected || 0) === 1 ? ' · Selected' : ''}</span></td>
                        <td><span style="font-size:0.82rem;">${escapeHtml(vehicle.license_number || '-')} ${licenseVerified ? '✓' : ''}</span></td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                ${vehicle.status !== 'active' ? `<button class="btn btn-sm btn-primary" onclick="setActiveVehicle(${vehicle.id})">Set Active</button>` : ''}
                                ${vehicle.status === 'active' && Number(vehicle.is_selected || 0) !== 1 ? `<button class="btn btn-sm btn-secondary" onclick="selectDeliveryVehicle(${vehicle.id})">Select</button>` : ''}
                                <button class="btn btn-sm btn-outline" onclick="editVehicleModal(${vehicle.id})">Edit</button>
                                <button class="btn btn-sm btn-outline" onclick="deleteVehicleConfirm(${vehicle.id})">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
        }).join('');
    }

    /**
     * Trigger file input for vehicle image upload.
     */
    function uploadVehicleImageUI(vehicleId) {
        let inp = document.getElementById('vehicleImageFileInput');
        if (!inp) {
            inp = document.createElement('input');
            inp.type = 'file';
            inp.id = 'vehicleImageFileInput';
            inp.accept = 'image/jpeg,image/jpg,image/png,image/webp';
            inp.style.display = 'none';
            document.body.appendChild(inp);
        }
        inp.onchange = function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) { showNotification('Image must be less than 5MB', 'error'); return; }
            const fd = new FormData();
            fd.append('vehicle_image', file);
            fetch(transporterApi('uploadVehicleImage/' + vehicleId), {
                method: 'POST',
                credentials: 'include',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showNotification('Vehicle photo uploaded!', 'success');
                    loadVehicles();
                } else {
                    showNotification(data.message || 'Upload failed', 'error');
                }
            })
            .catch(() => showNotification('Upload failed', 'error'));
            inp.value = '';
        };
        inp.click();
    }


    // Load vehicle types from database
    function loadVehicleTypes() {
        fetch(transporterApi('getVehicleTypes'))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.vehicleTypes) {
                    vehicleTypesData = data.vehicleTypes;
                }
            })
            .catch(error => {
                console.error('Error loading vehicle types:', error);
            });
    }

    // Convert vehicle_name to slug (e.g., "Small Van" -> "smallvan")
    function vehicleNameToSlug(name) {
        return name.toLowerCase().replace(/\s+/g, '');
    }

    // Get vehicle type display name from slug
    function getVehicleTypeName(slug) {
        if (!slug) return '';
        
        // First try to find in loaded vehicle types
        const type = vehicleTypesData.find(vt => vehicleNameToSlug(vt.vehicle_name) === slug.toLowerCase());
        if (type) {
            return type.vehicle_name;
        }
        
        // Fallback to capitalize slug
        return slug.charAt(0).toUpperCase() + slug.slice(1).replace(/([a-z])([A-Z])/g, '$1 $2');
    }

    function getVehicleIcon(type) {
        // Simple icons based on type name
        const slug = String(type || '').toLowerCase();
        if (slug.includes('bike') || slug.includes('motor')) return '🏍️';
        if (slug.includes('three') || slug.includes('wheel')) return '🛺';
        if (slug.includes('car')) return '🚗';
        if (slug.includes('van')) return '🚐';
        if (slug.includes('lorry') || slug.includes('truck')) return '🚚';
        return '🚗'; // default
    }

    // Generate vehicle type options HTML
    function generateVehicleTypeOptions(selectedType = '') {
        const selectedSlug = String(selectedType || '').toLowerCase();
        let options = '<option value="">Select Type</option>';
        vehicleTypesData.forEach(vt => {
            const slug = vehicleNameToSlug(vt.vehicle_name);
            const selected = (slug === selectedSlug) ? 'selected' : '';
            options += `<option value="${slug}" data-min="${vt.min_weight_kg}" data-max="${vt.max_weight_kg}" data-type-id="${vt.id}" ${selected}>${vt.vehicle_name} (${vt.min_weight_kg}-${vt.max_weight_kg}kg)</option>`;
        });
        return options;
    }

    function formatVehicleRegistrationInput(value) {
        const raw = String(value || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
        const match = raw.match(/^([A-Z]{0,3})(\d{0,4})/);
        if (!match) {
            return '';
        }

        const letters = match[1] || '';
        const numbers = match[2] || '';
        return numbers ? `${letters} ${numbers}`.trim() : letters;
    }

    function attachVehicleRegistrationFormatter(input) {
        if (!input || input.dataset.formatted === '1') {
            return;
        }

        input.dataset.formatted = '1';
        input.addEventListener('input', function() {
            this.value = formatVehicleRegistrationInput(this.value);
        });
        input.value = formatVehicleRegistrationInput(input.value);
    }

    function setupAddVehicleForm() {
        const form = document.getElementById('addVehicleForm');
        attachVehicleRegistrationFormatter(document.getElementById('vehicleRegistration'));
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const typeSelect = document.getElementById('vehicleType');
                if (typeSelect) {
                    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
                    const selectedTypeId = selectedOption ? selectedOption.dataset.typeId : '';
                    if (selectedTypeId) {
                        formData.set('vehicle_type_id', selectedTypeId);
                    }
                }

                fetch(transporterApi('addVehicle'), {
                        method: 'POST',
                        credentials: 'include',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            form.reset();
                            closeModal('addVehicleModal');
                            loadVehicles();
                        } else {
                            if (data.errors) {
                                let errorMsg = 'Validation errors:\n';
                                for (let field in data.errors) {
                                    errorMsg += `- ${data.errors[field]}\n`;
                                }
                                showNotification(errorMsg, 'error');
                            } else {
                                showNotification(data.message, 'error');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Failed to add vehicle. Please try again.', 'error');
                    });
            });
        }
    }

    function editVehicleModal(vehicleId) {
        fetch(transporterApi('getVehicles'))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.vehicles) {
                    const vehicle = data.vehicles.find(v => v.id == vehicleId);
                    if (vehicle) {
                        showEditVehicleModal(vehicle);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to load vehicle data', 'error');
            });
    }

    function showEditVehicleModal(vehicle) {
        // Fetch vehicle types to populate dropdown
        fetch(transporterApi('getVehicleTypes'), { credentials: 'include' })
            .then(r => r.json())
            .then(res => {
                const types = res.types || res.vehicleTypes || [];
                if (!res.success || !types.length) {
                    showNotification('Failed to load vehicle types', 'error');
                    return;
                }
                vehicleTypesData = types;
                
                const modalHtml = `
                <div id="editVehicleModal" class="modal" style="display: flex; align-items: center; justify-content: center;" onclick="closeModalOnBackdrop(event, 'editVehicleModal')">
                    <div class="modal-content" onclick="event.stopPropagation()">
                        <div class="modal-header">
                            <h3>Edit Vehicle</h3>
                        </div>
                        <div class="modal-body">
                            <form id="editVehicleForm" onsubmit="submitEditVehicle(event, ${vehicle.id})">
                                <div class="grid grid-2">
                                    <div class="form-group">
                                        <label for="editVehicleType">Vehicle Type *</label>
                                        <select id="editVehicleType" name="type" class="form-control" required>
                                            ${generateVehicleTypeOptions(vehicle.type)}
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editVehicleRegistration">Registration Number *</label>
                                        <input type="text" id="editVehicleRegistration" name="registration" class="form-control" value="${escapeHtml(vehicle.registration)}" placeholder="WP 1234" maxlength="8" required>
                                    </div>
                                </div>
                                
                                <div id="editWeightRangeDisplay" style="display: none; padding: 10px; background: #f0f9ff; border-left: 3px solid #3b82f6; margin: 10px 0;">
                                    <strong>Weight Range:</strong> <span id="editWeightRangeText"></span>
                                </div>
                                
                                <div class="grid grid-2">
                                    <div class="form-group">
                                        <label for="editVehicleFuelType">Fuel Type</label>
                                        <select id="editVehicleFuelType" name="fuel_type" class="form-control">
                                            <option value="petrol" ${vehicle.fuel_type === 'petrol' ? 'selected' : ''}>Petrol</option>
                                            <option value="diesel" ${vehicle.fuel_type === 'diesel' ? 'selected' : ''}>Diesel</option>
                                            <option value="electric" ${vehicle.fuel_type === 'electric' ? 'selected' : ''}>Electric</option>
                                            <option value="hybrid" ${vehicle.fuel_type === 'hybrid' ? 'selected' : ''}>Hybrid</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editVehicleModel">Vehicle Model</label>
                                        <input type="text" id="editVehicleModel" name="model" class="form-control" value="${escapeHtml(vehicle.model || '')}" placeholder="e.g., Toyota Hiace">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editVehicleStatus">Status</label>
                                    <select id="editVehicleStatus" name="status" class="form-control">
                                        <option value="active" ${vehicle.status === 'active' ? 'selected' : ''}>Active</option>
                                        <option value="inactive" ${vehicle.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                        <option value="maintenance" ${vehicle.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                                    </select>
                                </div>
                                
                                <div style="display: flex; gap: 20px; margin-top: var(--spacing-lg);">
                                    <button type="submit" class="btn btn-primary">Update Vehicle</button>
                                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;

        const existingModal = document.getElementById('editVehicleModal');
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        attachVehicleRegistrationFormatter(document.getElementById('editVehicleRegistration'));
        
        // Add event listener for vehicle type change to show weight range
        const editVehicleTypeSelect = document.getElementById('editVehicleType');
        if (editVehicleTypeSelect) {
            // Show initial weight range if a type is selected
            const selectedOption = editVehicleTypeSelect.options[editVehicleTypeSelect.selectedIndex];
            if (selectedOption.value) {
                const min = selectedOption.dataset.min;
                const max = selectedOption.dataset.max;
                document.getElementById('editWeightRangeText').textContent = `${min}-${max}kg`;
                document.getElementById('editWeightRangeDisplay').style.display = 'block';
            }
            
            editVehicleTypeSelect.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];
                const display = document.getElementById('editWeightRangeDisplay');
                const text = document.getElementById('editWeightRangeText');

                if (option.value) {
                    const min = option.dataset.min;
                    const max = option.dataset.max;
                    text.textContent = `${min}-${max}kg`;
                    display.style.display = 'block';
                } else {
                    display.style.display = 'none';
                }
            });
        }
    })
    .catch(err => {
        console.error('Error loading vehicle types:', err);
        showNotification('Failed to load vehicle types', 'error');
    });
}

    function closeEditModal() {
        const modal = document.getElementById('editVehicleModal');
        if (modal) {
            modal.remove();
        }
    }

    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.remove();
            }
        }
    }

    function submitEditVehicle(event, vehicleId) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const typeSelect = document.getElementById('editVehicleType');
        if (typeSelect) {
            const selectedOption = typeSelect.options[typeSelect.selectedIndex];
            const selectedTypeId = selectedOption ? selectedOption.dataset.typeId : '';
            if (selectedTypeId) {
                formData.set('vehicle_type_id', selectedTypeId);
            }
        }

        fetch(transporterApi('editVehicle/' + vehicleId), {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeEditModal();
                    loadVehicles();
                } else {
                    if (data.errors) {
                        let errorMsg = 'Validation errors:\n';
                        for (let field in data.errors) {
                            errorMsg += `- ${data.errors[field]}\n`;
                        }
                        showNotification(errorMsg, 'error');
                    } else {
                        showNotification(data.message, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to update vehicle. Please try again.', 'error');
            });
    }

    function setActiveVehicle(vehicleId) {
        if (confirm('Set this vehicle as active? This will deactivate all other vehicles.')) {
            fetch(transporterApi('setActiveVehicle/' + vehicleId), {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        loadVehicles();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to set active vehicle. Please try again.', 'error');
                });
        }
    }

    function deleteVehicleConfirm(vehicleId) {
        if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
            fetch(transporterApi('deleteVehicle/' + vehicleId), {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        loadVehicles();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to delete vehicle. Please try again.', 'error');
                });
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    // Fallback namespaced handlers used by inline onclick attributes.
    // External transporterDashboard.js may replace these with API-backed versions.
    window.TransporterDashboard = window.TransporterDashboard || {};
    if (typeof window.TransporterDashboard.showSection !== 'function') window.TransporterDashboard.showSection = showSection;
    if (typeof window.TransporterDashboard.refreshDeliveries !== 'function') window.TransporterDashboard.refreshDeliveries = refreshDeliveries;
    if (typeof window.TransporterDashboard.filterMyDeliveries !== 'function') window.TransporterDashboard.filterMyDeliveries = filterMyDeliveries;
    if (typeof window.TransporterDashboard.exportPaymentHistory !== 'function') window.TransporterDashboard.exportPaymentHistory = exportPaymentHistory;
</script>
