<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporter Dashboard - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Include Navbar Component -->
    <?php
    $username = $_SESSION['USER']->name ?? 'Transporter';
    $role = $_SESSION['USER']->role ?? 'transporter';
    include '../app/views/components/dashboardNavBar.view.php';
    ?>

    <div class="dashboard">

        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="menu-link active" data-section="dashboard">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        Dashboard
                    </a></li>
                <li><a href="#" class="menu-link" data-section="available-deliveries">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="16" rx="2" />
                                <line x1="7" y1="8" x2="17" y2="8" />
                                <line x1="7" y1="12" x2="17" y2="12" />
                            </svg>
                        </div>
                        Available Deliveries
                    </a></li>
                <li><a href="#" class="menu-link" data-section="mydeliveries">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 7h13v10H3z" />
                                <path d="M16 10h4l3 3v4h-7z" />
                            </svg>
                        </div>
                        My Deliveries
                    </a></li>
                <li><a href="#" class="menu-link" data-section="schedule">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        Schedule
                    </a></li>
                <li><a href="#" class="menu-link" data-section="earnings">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="8" />
                                <line x1="12" y1="8" x2="12" y2="16" />
                                <line x1="8" y1="12" x2="16" y2="12" />
                            </svg>
                        </div>
                        Earnings
                    </a></li>
                <li><a href="#" class="menu-link" data-section="vehicle">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="6" rx="2" />
                                <path d="M7 11V7h6v4" />
                                <circle cx="7.5" cy="17.5" r="1.5" />
                                <circle cx="16.5" cy="17.5" r="1.5" />
                            </svg>
                        </div>
                        Vehicle
                    </a></li>
                <li><a href="#" class="menu-link" data-section="analytics">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="20" x2="12" y2="10" />
                                <line x1="18" y1="20" x2="18" y2="4" />
                                <line x1="6" y1="20" x2="6" y2="14" />
                            </svg>
                        </div>
                        Analytics
                    </a></li>
                <li><a href="#" class="menu-link" data-section="feedback">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                        </div>
                        Reviews & Complaints
                    </a></li>
                <li><a href="#" class="menu-link" data-section="profile">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        Profile
                    </a></li>
            </ul>
        </aside>

        <main class="main-content">

            <div id="dashboard-section" class="content-section">
                <div class="content-header">
                    <h1 class="content-title">Dashboard Overview</h1>
                    <p class="content-subtitle">Welcome, <span id="welcomeUserName"><?php echo isset($username) ? htmlspecialchars($username) : 'Transporter'; ?></span>! Here's what's happening with your deliveries.</p>
                </div>

                <div class="dashboard-stats" style="margin-bottom: 36px;">
                    <div class="stat-card">
                        <div class="stat-number" id="availableDeliveries">0</div>
                        <div class="stat-label">Available Deliveries</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="activeDeliveries">0</div>
                        <div class="stat-label">Active Deliveries</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="monthlyEarnings">Rs. 0</div>
                        <div class="stat-label">This Month</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="completedDeliveries">0</div>
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
                                    <strong>Current Location:</strong> <span id="currentLocation">Colombo</span>
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
                            <div style="padding: 18px; background: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 18px;">
                                <div style="font-weight: 600; margin-bottom: 8px; color: #2c3e50;">#ORD-2025-001</div>
                                <div style="font-size: 0.9rem; color: #666; margin-bottom: 12px;">Colombo → Kandy • Rs. 850</div>
                                <span class="order-status delivered">DELIVERED</span>
                            </div>
                            <div style="padding: 18px; background: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px;">
                                <div style="font-weight: 600; margin-bottom: 8px; color: #2c3e50;">#ORD-2025-002</div>
                                <div style="font-size: 0.9rem; color: #666; margin-bottom: 12px;">Galle → Matara • Rs. 650</div>
                                <span class="order-status pending">IN PROGRESS</span>
                            </div>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Weekly Earnings</h3>
                        </div>
                        <div class="card-content" style="padding: 24px;">
                            <div id="weeklyEarnings" style="font-size: 3rem; font-weight: 700; color: #65b57c; margin-bottom: 28px;">Rs. 12,450</div>
                            <div style="font-size: 0.9rem; color: #666; line-height: 1.8;">
                                <div style="margin-bottom: 16px;"> 12 deliveries completed</div>
                                <div style="margin-bottom: 16px;"> 8 deliveries pending</div>
                                <div> 4.8 average rating</div>
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
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="buyer-avatar">AK</div>
                                    <div class="review-meta">
                                        <div class="buyer-name">Anu K.</div>
                                        <div class="review-sub">Colombo → Kandy • #ORD-2025-014</div>
                                    </div>
                                  <div class="rating-stars">⭐⭐⭐⭐⭐</div> 
                                </div>
                                <div class="review-body">Very punctual and careful with the produce. Highly recommended!</div>
                                <div class="review-footer">
                                    <span class="feedback-badge positive">On-time</span>
                                    <span class="feedback-badge positive">Professional</span>
                                    <span class="review-product">Vegetables</span>
                                </div>
                            </div>

                            <div class="review-card">
                                <div class="review-header">
                                    <div class="buyer-avatar alt">RS</div>
                                    <div class="review-meta">
                                        <div class="buyer-name">Ruwan S.</div>
                                        <div class="review-sub">Galle → Colombo • #ORD-2025-011</div>
                                    </div>
                                  <div class="rating-stars">⭐⭐⭐⭐☆</div>
                                </div>
                                <div class="review-body">Good service. One small delay due to traffic but communicated well.</div>
                                <div class="review-footer">
                                    <span class="feedback-badge neutral">Slight delay</span>
                                    <span class="review-product">Fruits</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Complaints</h3>
                        </div>
                        <div class="card-content">
                            <div class="complaint-card">
                                <div class="complaint-header">
                                    <div class="buyer-avatar">TM</div>
                                    <div class="review-meta">
                                        <div class="complaint-title">Packaging issue</div>
                                        <div class="complaint-sub">Matale → Gampaha • #ORD-2025-010</div>
                                    </div>
                                    <span class="complaint-status resolved">Resolved</span>
                                </div>
                                <div class="complaint-body">Some boxes were stacked incorrectly. Repacked at destination with no loss.</div>
                            </div>

                            <div class="complaint-card">
                                <div class="complaint-header">
                                    <div class="buyer-avatar alt">NK</div>
                                    <div class="review-meta">
                                        <div class="complaint-title">Pickup delay</div>
                                        <div class="complaint-sub">Anuradhapura → Kurunegala • #ORD-2025-008</div>
                                    </div>
                                    <span class="complaint-status in-progress">In Progress</span>
                                </div>
                                <div class="complaint-body">Driver arrived 20 minutes late due to road closure. Working on mitigation.</div>
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
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 8,500</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Distance Bonus:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 2,300</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Express Delivery:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 1,150</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 16px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Rating Bonus:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 500</span>
                                </div>
                                <hr style="margin: 20px 0; border: none; border-top: 2px solid #e0e0e0;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #e8f5e9; border-radius: 8px;">
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Total This Month:</span>
                                    <span style="font-weight: 700; font-size: 1.2rem; color: #65b57c;">Rs. 12,450</span>
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
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">23</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Average Rating:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">4.8/5</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">On-Time Delivery:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">95%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 14px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Customer Satisfaction:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">98%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Earnings per Delivery:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 541</span>
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
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vehiclesTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="profile-section" class="content-section profile-section" style="display: none;">
                <!-- Profile Header with Photo (modern UI) -->
                <div class="profile-header-modern">
                    <div class="profile-banner"></div>
                    <div class="profile-header-content">
                        <div class="profile-photo-wrapper-modern">
                            <img src="https://ui-avatars.com/api/?name=Transporter&background=4CAF50&color=fff&size=150" alt="Transporter Profile" id="profilePhoto" class="profile-photo-modern">
                            <button class="photo-edit-btn" onclick="uploadPhoto()" title="Change profile photo">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                    <circle cx="12" cy="13" r="4" />
                                </svg>
                            </button>
                        </div>
                        <div class="profile-info-modern">
                            <h2 class="profile-name-modern" id="displayProfileName">Transporter</h2>
                            <p class="profile-role-modern">Transporter Account</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="profile-form-modern">
                    <div class="form-section-header">
                        <h3>Personal Information</h3>
                        <p>Update your account details and preferences</p>
                    </div>
                    <div class="profile-form-grid-modern">
                        <div class="form-group-modern">
                            <label for="profileName">Full Name</label>
                            <input type="text" id="profileName" name="name" class="form-control" required>
                        </div>
                        <div class="form-group-modern">
                            <label for="profileEmail">Email</label>
                            <input type="email" id="profileEmail" name="email" class="form-control" required>
                        </div>
                        <div class="form-group-modern">
                            <label for="profilePhone">Phone</label>
                            <input type="tel" id="profilePhone" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group-modern">
                            <label for="profileNIC">NIC Number</label>
                            <input type="text" id="profileNIC" name="nic" class="form-control" required>
                        </div>
                        <div class="form-group-modern">
                            <label for="businessName">Business Name</label>
                            <input type="text" id="businessName" name="businessName" class="form-control">
                        </div>
                        <div class="form-group-modern">
                            <label for="businessType">Business Type</label>
                            <select id="businessType" name="businessType" class="form-control">
                                <option value="individual">Individual</option>
                                <option value="company">Company</option>
                                <option value="cooperative">Cooperative</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label for="serviceAreas">Service Areas</label>
                            <input type="text" id="serviceAreas" name="serviceAreas" class="form-control" placeholder="e.g., Colombo, Kandy, Galle">
                        </div>
                        <div class="form-group-modern">
                            <label for="baseLocation">Base Location</label>
                            <input type="text" id="baseLocation" name="baseLocation" class="form-control">
                        </div>
                    </div>

                    <div class="profile-actions-modern">
                        <button type="button" class="btn btn-primary btn-sm" onclick="updateProfile()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>
                            Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="1 4 1 10 7 10" />
                                <polyline points="23 20 23 14 17 14" />
                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" />
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="profile-stats-modern">
                    <div class="stats-header">
                        <h3>Account Statistics</h3>
                        <p>Your performance and activity overview</p>
                    </div>
                    <div class="stats-grid-modern">
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern" style="background: var(--primary-color);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 7h13v10H3z" />
                                    <path d="M16 10h4l3 3v4h-7z" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Total Deliveries</div>
                                <div class="stat-value-modern">127</div>
                            </div>
                        </div>
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern" style="background: var(--primary-color);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Average Rating</div>
                                <div class="stat-value-modern">4.8</div>
                            </div>
                        </div>
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern" style="background: var(--primary-color);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                                    <polyline points="17 6 23 6 23 12" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">On-Time Rate</div>
                                <div class="stat-value-modern">95%</div>
                            </div>
                        </div>
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern" style="background: var(--primary-color);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23" />
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label-modern">Total Earnings</div>
                                <div class="stat-value-modern">Rs. 68.5K</div>
                            </div>
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
            </div>
            <div class="modal-body">
                <form id="addVehicleForm">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="vehicleType">Vehicle Type *</label>
                            <select id="vehicleType" name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="bike">Motorcycle</option>
                                <option value="threewheeler">Three-wheeler</option>
                                <option value="car">Car</option>
                                <option value="van">Van</option>
                                <option value="truck">Truck</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vehicleRegistration">Registration Number *</label>
                            <input type="text" id="vehicleRegistration" name="registration" class="form-control" required>
                        </div>
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="vehicleCapacity">Load Capacity (kg) *</label>
                            <input type="number" id="vehicleCapacity" name="capacity" class="form-control" required>
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

                    <div class="form-group">
                        <label for="vehicleModel">Vehicle Model</label>
                        <input type="text" id="vehicleModel" name="model" class="form-control" placeholder="e.g., Toyota Hiace">
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
        window.APP_ROOT = "<?= ROOT ?>";
        window.USER_NAME = <?= json_encode($_SESSION['USER']->name ?? 'Transporter') ?>;
        window.USER_EMAIL = <?= json_encode($_SESSION['USER']->email ?? '') ?>;
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/transporterDashboard.js"></script>
    <script src="<?= ROOT ?>/assets/js/dashboardNavBar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initTransporterDashboard();
        });

        function initTransporterDashboard() {
            const user = typeof getCurrentUser === 'function' ? getCurrentUser() : null;
            if (user && document.getElementById('transporterName')) {
                document.getElementById('transporterName').textContent = user.name || 'Transporter';
            } else if (document.getElementById('transporterName')) {
                document.getElementById('transporterName').textContent = 'Transporter';
            }

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

            showSection('dashboard');
        }

        function setupNavigation() {
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');
                    showSection(section);

                    menuLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        function showSection(sectionName) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');

            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }

            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === sectionName) {
                    link.classList.add('active');
                }
            });
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
            document.getElementById('availableDeliveries').textContent = '8';
            document.getElementById('activeDeliveries').textContent = '3';
            document.getElementById('monthlyEarnings').textContent = 'Rs. 12,450';
            document.getElementById('completedDeliveries').textContent = '127';

            document.getElementById('recentDeliveries').innerHTML = `
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-001</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Colombo → Kandy - Rs. 850</div>
                    <span class="badge">Completed</span>
                </div>
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-002</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Galle → Matara - Rs. 650</div>
                    <span class="badge">In Progress</span>
                </div>
            `;
        }

        function loadAvailableDeliveries() {
            const container = document.getElementById('availableDeliveriesList');
            container.innerHTML = `
                ${generateDeliveryCard('ORD-2025-003', 'Colombo', 'Kandy', '25km', '15kg', 'Rs. 750', 'urgent')}
                ${generateDeliveryCard('ORD-2025-004', 'Matale', 'Gampaha', '45km', '30kg', 'Rs. 950', 'normal')}
                ${generateDeliveryCard('ORD-2025-005', 'Anuradhapura', 'Kurunegala', '60km', '22kg', 'Rs. 1200', 'normal')}
                ${generateDeliveryCard('ORD-2025-006', 'Galle', 'Colombo', '120km', '40kg', 'Rs. 1500', 'express')}
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
            tbody.innerHTML = `
                <tr>
                    <td><strong>#ORD-2025-001</strong></td>
                    <td>Colombo → Kandy</td>
                    <td>25km</td>
                    <td>15kg</td>
                    <td><strong>Rs. 750</strong></td>
                    <td><span class="order-status delivered">DELIVERED</span></td>
                    <td>Oct 20, 2025</td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="viewDelivery('ORD-2025-001')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-002</strong></td>
                    <td>Galle → Matara</td>
                    <td>35km</td>
                    <td>20kg</td>
                    <td><strong>Rs. 650</strong></td>
                    <td><span class="order-status pending">IN PROGRESS</span></td>
                    <td>Oct 22, 2025</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="updateDeliveryStatus('ORD-2025-002')">Update Status</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-003</strong></td>
                    <td>Matale → Gampaha</td>
                    <td>45km</td>
                    <td>30kg</td>
                    <td><strong>Rs. 950</strong></td>
                    <td><span class="order-status shipped">ACCEPTED</span></td>
                    <td>Oct 24, 2025</td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="viewDelivery('ORD-2025-003')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-004</strong></td>
                    <td>Anuradhapura → Kurunegala</td>
                    <td>60km</td>
                    <td>22kg</td>
                    <td><strong>Rs. 1,200</strong></td>
                    <td><span class="order-status pending">IN PROGRESS</span></td>
                    <td>Oct 25, 2025</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="updateDeliveryStatus('ORD-2025-004')">Update Status</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-005</strong></td>
                    <td>Galle → Colombo</td>
                    <td>120km</td>
                    <td>40kg</td>
                    <td><strong>Rs. 1,500</strong></td>
                    <td><span class="order-status delivered">DELIVERED</span></td>
                    <td>Oct 21, 2025</td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="viewDelivery('ORD-2025-005')">View</button>
                    </td>
                </tr>
                <tr>
                    <td><strong>#ORD-2025-006</strong></td>
                    <td>Colombo → Negombo</td>
                    <td>38km</td>
                    <td>18kg</td>
                    <td><strong>Rs. 700</strong></td>
                    <td><span class="order-status cancelled">CANCELLED</span></td>
                    <td>Oct 19, 2025</td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="viewDelivery('ORD-2025-006')">View</button>
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

            // Update profile photo
            const profilePhoto = document.getElementById('profilePhoto');
            const displayName = document.getElementById('displayProfileName');
            if (profilePhoto) {
                const encoded = encodeURIComponent(uname);
                profilePhoto.src = `https://ui-avatars.com/api/?name=${encoded}&background=4CAF50&color=fff&size=150`;
            }
            if (displayName) {
                displayName.textContent = uname;
            }

            // Populate form fields
            if (user) {
                document.getElementById('profileName').value = user.name || uname;
                document.getElementById('profileEmail').value = user.email || uemail;
                document.getElementById('profilePhone').value = user.phone || '';
            } else {
                document.getElementById('profileName').value = uname;
                document.getElementById('profileEmail').value = uemail || '';
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
            // Populate top stat cards
            const today = 1850;
            const week = 9450;
            const month = 12450;
            const total = 68500;

            const el = id => document.getElementById(id);
            if (el('todayEarnings')) el('todayEarnings').textContent = `Rs. ${today.toLocaleString()}`;
            if (el('weekEarnings')) el('weekEarnings').textContent = `Rs. ${week.toLocaleString()}`;
            if (el('monthEarningsDetail')) el('monthEarningsDetail').textContent = `Rs. ${month.toLocaleString()}`;
            if (el('totalEarningsDetail')) el('totalEarningsDetail').textContent = `Rs. ${total.toLocaleString()}`;

            // Fill payment history table
            const rows = [{
                    date: 'Oct 23, 2025',
                    id: 'ORD-2025-019',
                    route: 'Galle → Colombo',
                    amount: 1500,
                    status: 'Paid'
                },
                {
                    date: 'Oct 22, 2025',
                    id: 'ORD-2025-017',
                    route: 'Matale → Gampaha',
                    amount: 950,
                    status: 'Paid'
                },
                {
                    date: 'Oct 22, 2025',
                    id: 'ORD-2025-016',
                    route: 'Anuradhapura → Kurunegala',
                    amount: 1200,
                    status: 'Pending'
                },
                {
                    date: 'Oct 21, 2025',
                    id: 'ORD-2025-012',
                    route: 'Colombo → Kandy',
                    amount: 850,
                    status: 'Paid'
                }
            ];
            const body = document.getElementById('paymentHistoryBody');
            if (body) {
                body.innerHTML = rows.map(r => `
                    <tr>
                        <td>${r.date}</td>
                        <td>#${r.id}</td>
                        <td>${r.route}</td>
                        <td><strong>Rs. ${r.amount.toLocaleString()}</strong></td>
                        <td><span class="badge">${r.status}</span></td>
                    </tr>
                `).join('');
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
                    if (data.success && data.vehicles) {
                        displayVehicles(data.vehicles);
                        updateCurrentStatus(data.vehicles);
                    } else {
                        displayVehicles([]);
                        updateCurrentStatus([]);
                    }
                })
                .catch(error => {
                    console.error('Error loading vehicles:', error);
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

                    fetch('<?= ROOT ?>/TransporterDashboard/addVehicle', {
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
                                            <option value="">Select Type</option>
                                            <option value="bike" ${vehicle.type === 'bike' ? 'selected' : ''}>Motorcycle</option>
                                            <option value="threewheeler" ${vehicle.type === 'threewheeler' ? 'selected' : ''}>Three-wheeler</option>
                                            <option value="car" ${vehicle.type === 'car' ? 'selected' : ''}>Car</option>
                                            <option value="van" ${vehicle.type === 'van' ? 'selected' : ''}>Van</option>
                                            <option value="truck" ${vehicle.type === 'truck' ? 'selected' : ''}>Truck</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editVehicleRegistration">Registration Number *</label>
                                        <input type="text" id="editVehicleRegistration" name="registration" class="form-control" value="${escapeHtml(vehicle.registration)}" required>
                                    </div>
                                </div>
                                
                                <div class="grid grid-2">
                                    <div class="form-group">
                                        <label for="editVehicleCapacity">Load Capacity (kg) *</label>
                                        <input type="number" id="editVehicleCapacity" name="capacity" class="form-control" value="${vehicle.capacity}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editVehicleFuelType">Fuel Type</label>
                                        <select id="editVehicleFuelType" name="fuel_type" class="form-control">
                                            <option value="petrol" ${vehicle.fuel_type === 'petrol' ? 'selected' : ''}>Petrol</option>
                                            <option value="diesel" ${vehicle.fuel_type === 'diesel' ? 'selected' : ''}>Diesel</option>
                                            <option value="electric" ${vehicle.fuel_type === 'electric' ? 'selected' : ''}>Electric</option>
                                            <option value="hybrid" ${vehicle.fuel_type === 'hybrid' ? 'selected' : ''}>Hybrid</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="editVehicleModel">Vehicle Model</label>
                                    <input type="text" id="editVehicleModel" name="model" class="form-control" value="${escapeHtml(vehicle.model || '')}" placeholder="e.g., Toyota Hiace">
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

            fetch('<?= ROOT ?>/TransporterDashboard/editVehicle/' + vehicleId, {
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
</body>

</html>