<!-- Transporter Dashboard Content (embedded in transporterMain.view.php) -->

<div id="dashboard-section" class="content-section">
    <div class="content-header">
        <h1 class="content-title">Dashboard Overview</h1>
        <p class="content-subtitle">Welcome, <span id="welcomeUserName"><?php echo isset($username) ? htmlspecialchars($username) : 'Transporter'; ?></span>! Here's what's happening with your deliveries.</p>
    </div>

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
                        <strong>Vehicle:</strong> <span id="activeVehicle">Loading...</span>
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
                    <button class="btn btn-outline" style="width: 100%; padding: 14px;" onclick="showSection('available-deliveries')">
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
                <button class="btn btn-primary" onclick="showSection('available-deliveries')" style="margin: 0;">Find Deliveries</button>
                <button class="btn btn-secondary" onclick="showSection('mydeliveries')" style="margin: 0;">My Deliveries</button>
                <button class="btn btn-outline" onclick="showSection('schedule')" style="margin: 0;">View Schedule</button>
                <button class="btn btn-outline" onclick="showSection('vehicle')" style="margin: 0;">Vehicle Info</button>
            </div>
        </div>
    </div>
</div>

<div id="feedback-section" class="content-section" style="display: none;">
    <div class="content-header">
        <h1 class="content-title">Reviews & Complaints</h1>
        <p class="content-subtitle">See what buyers and farmers are saying about your deliveries</p>
    </div>

    <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Recent Reviews</h3>
            </div>
            <div class="card-content">
                <div style="padding: 40px; text-align: center; color: #666;">
                    No reviews yet
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Complaints</h3>
            </div>
            <div class="card-content">
                <div style="padding: 40px; text-align: center; color: #666;">
                    No complaints
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Deliveries Section -->
<div id="available-deliveries-section" class="content-section" style="display: none;">
    <div class="content-header">
        <h1 class="content-title">Available Deliveries</h1>
        <button class="btn btn-outline btn-sm" onclick="refreshDeliveries()">Refresh</button>
    </div>

    <!-- Filter Section -->
    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Filter Deliveries</h3>
        </div>
        <div class="card-content">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Pickup Location</label>
                    <select id="locationFilter" class="form-control">
                        <option value="">All Locations</option>
                        <option value="Colombo">Colombo</option>
                        <option value="Kandy">Kandy</option>
                        <option value="Galle">Galle</option>
                        <option value="Matale">Matale</option>
                        <option value="Anuradhapura">Anuradhapura</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Max Distance</label>
                    <select id="distanceFilter" class="form-control">
                        <option value="">Any Distance</option>
                        <option value="10">Within 10km</option>
                        <option value="25">Within 25km</option>
                        <option value="50">Within 50km</option>
                        <option value="100">Within 100km</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Max Weight</label>
                    <select id="weightFilter" class="form-control">
                        <option value="">Any Weight</option>
                        <option value="10">Up to 10kg</option>
                        <option value="25">Up to 25kg</option>
                        <option value="50">Up to 50kg</option>
                        <option value="100">Up to 100kg</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Payment Range</label>
                    <select id="paymentFilter" class="form-control">
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

    <!-- Deliveries Grid -->
    <div id="availableDeliveriesList" style="display: flex; gap: 24px; margin-top: 28px; overflow-x: auto; padding-bottom: 16px;">
        <!-- Populated by JavaScript -->
    </div>
</div>

<div id="mydeliveries-section" class="content-section" style="display: none;">
    <h1 style="margin-bottom: 32px; font-size: 2rem;"> My Deliveries</h1>

    <div style="display: flex; gap: 20px; margin-bottom: 32px; border-bottom: 2px solid #f0f0f0; flex-wrap: wrap; padding-bottom: 4px;">
        <button class="tab-btn active" data-status="all" onclick="filterMyDeliveries('all')" style="margin-right: 8px; padding: 12px 20px;">All</button>
        <button class="tab-btn" data-status="accepted" onclick="filterMyDeliveries('accepted')" style="margin-right: 8px; padding: 12px 20px;">Accepted</button>
        <button class="tab-btn" data-status="in-progress" onclick="filterMyDeliveries('in-progress')" style="margin-right: 8px; padding: 12px 20px;">In Progress</button>
        <button class="tab-btn" data-status="completed" onclick="filterMyDeliveries('completed')" style="padding: 12px 20px;">Completed</button>
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
    <h1 style="margin-bottom: 32px;">Earnings Overview</h1>

    <div class="dashboard-stats" style="margin-bottom: 40px;">
        <div class="stat-card">
            <div class="stat-number" id="todayEarnings">Rs. 0</div>
            <div class="stat-label">Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="weekEarnings">Rs. 0</div>
            <div class="stat-label">This Week</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="monthEarningsDetail">Rs. 0</div>
            <div class="stat-label">This Month</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="totalEarningsDetail">Rs. 0</div>
            <div class="stat-label">Total</div>
        </div>
    </div>

    <div class="grid grid-2" style="margin-top: 32px; gap: 28px;">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Earnings Breakdown</h3>
            </div>
            <div class="card-content" style="padding: 28px;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Base Delivery Fee:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Distance Bonus:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Express Delivery:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 16px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Rating Bonus:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 0</span>
                    </div>
                    <hr style="margin: 20px 0; border: none; border-top: 2px solid #e0e0e0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #e8f5e9; border-radius: 8px;">
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Total This Month:</span>
                        <span style="font-weight: 700; font-size: 1.2rem; color: #65b57c;">Rs. 0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">Performance Metrics</h3>
            </div>
            <div class="card-content" style="padding: 28px;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Deliveries Completed:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Average Rating:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">0/5</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">On-Time Delivery:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">0%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Customer Satisfaction:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">0%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px;">
                        <span style="font-size: 0.95rem; color: #2c3e50;">Earnings per Delivery:</span>
                        <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card" style="margin-top: 32px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title">Payment History</h3>
            <button class="btn btn-secondary" onclick="exportPaymentHistory()">Export CSV</button>
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
</main>
</div>

<div id="addVehicleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Vehicle</h3>
            <span class="modal-close" data-modal-close>&times;</span>
        </div>
        <div class="modal-body">
            <form id="addVehicleForm">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="vehicleType">Vehicle Type *</label>
                        <select id="vehicleType" name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <?php if (!empty($vehicleTypes)): ?>
                                <?php foreach ($vehicleTypes as $vType): 
                                    $slug = strtolower(str_replace(' ', '', $vType->vehicle_name));
                                ?>
                                    <option value="<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($vType->vehicle_name) ?> (<?= $vType->min_weight_kg ?>-<?= $vType->max_weight_kg ?>kg)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vehicleRegistration">Registration Number *</label>
                        <input type="text" id="vehicleRegistration" name="registration" class="form-control" required>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="vehicleFuelType">Fuel Type</label>
                        <select id="vehicleFuelType" name="fuel_type" class="form-control">
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vehicleModel">Vehicle Model</label>
                        <input type="text" id="vehicleModel" name="model" class="form-control" placeholder="e.g., Toyota Hiace">
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
                <div class="content-card" style="margin: 0; min-width: 350px; max-width: 350px; flex-shrink: 0;">
                    <div style="padding: 40px 20px; text-align: center; color: #666;">
                        No available deliveries at the moment
                    </div>
                </div>
            `;
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

    function loadMyDeliveries() {
        const tbody = document.getElementById('myDeliveriesTableBody');
        // Deliveries will be loaded from database
        tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 60px 20px; color: #666;">
                        No deliveries yet
                    </td>
                </tr>
            `;
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
        showNotification('Location updated successfully', 'success');
    }

    function refreshDeliveries() {
        showNotification('Refreshing available deliveries...', 'info');
        setTimeout(() => {
            loadAvailableDeliveries();
            showNotification('Deliveries refreshed', 'success');
        }, 1000);
    }

    function acceptDelivery(orderId) {
        showNotification(`Delivery ${orderId} accepted! Contact details will be shared.`, 'success');
        loadAvailableDeliveries();
        loadMyDeliveries();
    }

    function viewDeliveryDetails(orderId) {
        showNotification('Delivery details modal will be implemented', 'info');
    }

    function filterMyDeliveries(status) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-status="${status}"]`).classList.add('active');

        showNotification(`Filtering deliveries by: ${status}`, 'info');
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

    function previousWeek() {
        showNotification('Loading previous week...', 'info');
    }

    function nextWeek() {
        showNotification('Loading next week...', 'info');
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

    function loadVehicles() {
        fetch('<?= ROOT ?>/TransporterDashboard/getVehicles')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.vehicles && data.vehicles.length > 0) {
                    displayVehicles(data.vehicles);
                    updateCurrentStatus(data.vehicles);
                } else {
                    // Show empty state with proper message
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

    function updateCurrentStatus(vehicles) {
        const activeVehicleSpan = document.getElementById('activeVehicle');

        if (!vehicles || vehicles.length === 0) {
            activeVehicleSpan.textContent = 'No vehicles added';
            activeVehicleSpan.style.color = '#666';
            return;
        }

        const activeVehicle = vehicles.find(v => v.status === 'active');

        if (activeVehicle) {
            const vehicleName = activeVehicle.model || getVehicleTypeName(activeVehicle.type);
            activeVehicleSpan.textContent = `${vehicleName} (${activeVehicle.registration})`;
            activeVehicleSpan.style.color = '#65b57c';
            activeVehicleSpan.style.fontWeight = 'bold';
        } else {
            const firstVehicle = vehicles[0];
            const vehicleName = firstVehicle.model || getVehicleTypeName(firstVehicle.type);
            activeVehicleSpan.textContent = `${vehicleName} (${firstVehicle.registration}) - ${firstVehicle.status}`;
            activeVehicleSpan.style.color = '#666';
        }
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
            const statusClass = vehicle.status === 'active' ? 'success' : vehicle.status === 'maintenance' ? 'warning' : 'secondary';
            const statusText = vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1);
            const vehicleIcon = getVehicleIcon(vehicle.type);
            const vehicleTypeName = getVehicleTypeName(vehicle.type);

            return `
                    <div class="content-card" style="margin-bottom: 24px;">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="card-title">${escapeHtml(vehicle.model || vehicleTypeName)}</h3>
                            <span class="badge">${statusText}</span>
                        </div>
                        <div class="card-content">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                <div>
                                    <div style="margin-bottom: 20px; line-height: 2.2;">
                                        <strong>Vehicle Type:</strong> ${vehicleTypeName}<br>
                                        <strong>Registration:</strong> ${escapeHtml(vehicle.registration)}<br>
                                        <strong>Capacity:</strong> ${escapeHtml(vehicle.capacity)}kg<br>
                                        <strong>Fuel Type:</strong> ${escapeHtml(vehicle.fuel_type || 'N/A')}<br>
                                        <strong>Status:</strong> <span class="badge">${statusText}</span>
                                    </div>
                                    <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 20px;">
                                        ${vehicle.status !== 'active' ? `<button class="btn btn-primary" onclick="setActiveVehicle(${vehicle.id})" style="margin: 0;">✓ Set as Active</button>` : ''}
                                        <button class="btn btn-secondary" onclick="editVehicleModal(${vehicle.id})" style="background: #dc3545; border-color: #dc3545; color: white; margin: 0;">✏️ Edit</button>
                                        <button class="btn btn-secondary" onclick="deleteVehicleConfirm(${vehicle.id})" style="margin: 0;">🗑️ Delete</button>
                                    </div>
                                </div>
                                <div>
                                    <div style="background: #f8f9fa; border-radius: 12px; padding: 24px; text-align: center;">
                                        <div style="font-size: 4rem; margin-bottom: 20px;">${vehicleIcon}</div>
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
            const statusClass = vehicle.status === 'active' ? 'success' : vehicle.status === 'maintenance' ? 'warning' : 'secondary';
            const statusText = vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1);

            return `
                    <tr>
                        <td>${escapeHtml(vehicle.model || 'N/A')}</td>
                        <td>${escapeHtml(vehicle.registration)}</td>
                        <td>${getVehicleTypeName(vehicle.type)}</td>
                        <td>${escapeHtml(vehicle.capacity)}kg</td>
                        <td><span class="badge">${statusText}</span></td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                ${vehicle.status !== 'active' ? `<button class="btn btn-sm btn-primary" onclick="setActiveVehicle(${vehicle.id})" style="background: #65b57c; border-color: #65b57c;">Set Active</button>` : ''}
                                <button class="btn btn-sm btn-secondary" onclick="editVehicleModal(${vehicle.id})" style="background: #dc3545; border-color: #dc3545; color: white;">Edit</button>
                                <button class="btn btn-sm btn-secondary" onclick="deleteVehicleConfirm(${vehicle.id})">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
        }).join('');
    }

    // Load vehicle types from database
    function loadVehicleTypes() {
        fetch('<?= ROOT ?>/TransporterDashboard/getVehicleTypes')
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
        const slug = type.toLowerCase();
        if (slug.includes('bike') || slug.includes('motor')) return '🏍️';
        if (slug.includes('three') || slug.includes('wheel')) return '🛺';
        if (slug.includes('car')) return '🚗';
        if (slug.includes('van')) return '🚐';
        if (slug.includes('lorry') || slug.includes('truck')) return '🚚';
        return '🚗'; // default
    }

    // Generate vehicle type options HTML
    function generateVehicleTypeOptions(selectedType = '') {
        let options = '<option value="">Select Type</option>';
        vehicleTypesData.forEach(vt => {
            const slug = vehicleNameToSlug(vt.vehicle_name);
            const selected = (slug === selectedType.toLowerCase()) ? 'selected' : '';
            options += `<option value="${slug}" ${selected}>${vt.vehicle_name} (${vt.min_weight_kg}-${vt.max_weight_kg}kg)</option>`;
        });
        return options;
    }

    function getVehicleIcon(type) {
        const icons = {
            'bike': '🏍️',
            'threewheeler': '🛺',
            'car': '🚗',
            'van': '🚐',
            'truck': '🚚'
        };
        return icons[type] || '🚗';
    }

    function getVehicleTypeName(type) {
        const names = {
            'bike': 'Motorcycle',
            'threewheeler': 'Three-wheeler',
            'car': 'Car',
            'van': 'Van',
            'truck': 'Truck'
        };
        return names[type] || type.charAt(0).toUpperCase() + type.slice(1);
    }

    function setupAddVehicleForm() {
        const form = document.getElementById('addVehicleForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                fetch('<?= ROOT ?>/transporterDashboard/addVehicle', {
                        method: 'POST',
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
        fetch('<?= ROOT ?>/TransporterDashboard/getVehicles')
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
        fetch(`${window.APP_ROOT}/transporterDashboard/getVehicleTypes`, { credentials: 'include' })
            .then(r => r.json())
            .then(res => {
                if (!res.success || !res.types) {
                    showNotification('Failed to load vehicle types', 'error');
                    return;
                }
                
                const vehicleTypesOptions = res.types.map(t => {
                    const isSelected = t.vehicle_name.toLowerCase() === vehicle.type.toLowerCase() ? 'selected' : '';
                    return `<option value="${t.id}" data-min="${t.min_weight_kg}" data-max="${t.max_weight_kg}" ${isSelected}>${escapeHtml(t.vehicle_name)}</option>`;
                }).join('');
                
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
                                        <input type="text" id="editVehicleRegistration" name="registration" class="form-control" value="${escapeHtml(vehicle.registration)}" required>
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

        fetch('<?= ROOT ?>/transporterDashboard/editVehicle/' + vehicleId, {
                method: 'POST',
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
            fetch('<?= ROOT ?>/TransporterDashboard/setActiveVehicle/' + vehicleId, {
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
            fetch('<?= ROOT ?>/TransporterDashboard/deleteVehicle/' + vehicleId, {
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
</script>