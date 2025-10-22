<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporter Dashboard - AgroLink</title>
    <link rel="stylesheet" href="assets/css/style2.css?v=2">
</head>
<body>

    <nav class="top-navbar">
        <div class="logo-section">
            <img src="<?php echo ROOT; ?>/assets/imgs/Logo.png" alt="AgroLink" style="height: 40px;">
        </div>
        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">TR</div>
                <div class="user-details">
                    <div class="user-name" id="transporterName">Transporter</div>
                    <div class="user-role">Transporter</div>
                </div>
            </div>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </nav>

    <div class="dashboard">
 
        <aside class="sidebar">
           <!-- <div class="sidebar-header">
               <h3 class="sidebar-title">Transporter Dashboard</h3>
            </div> -->
            <ul class="sidebar-menu">
                <li><a href="#dashboard" class="menu-link active" data-section="dashboard">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    Dashboard
                </a></li>
                <li><a href="#available" class="menu-link" data-section="available">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <line x1="7" y1="8" x2="17" y2="8"/>
                            <line x1="7" y1="12" x2="17" y2="12"/>
                        </svg>
                    </div>
                    Available
                </a></li>
                <li><a href="#mydeliveries" class="menu-link" data-section="mydeliveries">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7h13v10H3z"/>
                            <path d="M16 10h4l3 3v4h-7z"/>
                        </svg>
                    </div>
                    My Deliveries
                </a></li>
                <li><a href="#schedule" class="menu-link" data-section="schedule">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    Schedule
                </a></li>
                <li><a href="#earnings" class="menu-link" data-section="earnings">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="8"/>
                            <line x1="12" y1="8" x2="12" y2="16"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                    </div>
                    Earnings
                </a></li>
                <li><a href="#vehicle" class="menu-link" data-section="vehicle">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="6" rx="2"/>
                            <path d="M7 11V7h6v4"/>
                            <circle cx="7.5" cy="17.5" r="1.5"/>
                            <circle cx="16.5" cy="17.5" r="1.5"/>
                        </svg>
                    </div>
                    Vehicle
                </a></li>
                <li><a href="#profile" class="menu-link" data-section="profile">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    Profile
                </a></li>
                <li><a href="#analytics" class="menu-link" data-section="analytics">
                    <div class="menu-icon">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="20" x2="12" y2="10"/>
                            <line x1="18" y1="20" x2="18" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    Analytics
                </a></li>
            </ul>
        </aside>

        <main class="main-content">
    
            <div id="dashboard-section" class="content-section">
                <h1 style="margin-bottom: 20px; font-size: 2rem;">Dashboard Overview</h1>
                <p style="color: #666; font-size: 1rem; margin-bottom: 36px;">
                    Welcome back, <span id="welcomeUserName" style="font-weight: 600; color: #000000ff;"><?php echo isset($username) ? htmlspecialchars($username) : 'Transporter'; ?></span>! Here's what's happening with your deliveries.
                </p>
                
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
                                <button class="btn btn-outline" style="width: 100%; padding: 14px;" onclick="showSection('available')">
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
                            <button class="btn btn-primary" onclick="showSection('available')" style="margin: 0;">Find Deliveries</button>
                            <button class="btn btn-secondary" onclick="showSection('mydeliveries')" style="margin: 0;">My Deliveries</button>
                            <button class="btn btn-outline" onclick="showSection('schedule')" style="margin: 0;">View Schedule</button>
                            <button class="btn btn-outline" onclick="showSection('vehicle')" style="margin: 0;">Vehicle Info</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Deliveries Section -->
            <div id="available-section" class="content-section" style="display: none;">
                <div class="content-header">
                    <h1 class="content-title">Available Deliveries</h1>
                    <button class="btn btn-outline btn-sm" onclick="refreshDeliveries()">🔄 Refresh</button>
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
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <h3 class="card-title">Weekly Schedule</h3>
                            <div style="display: flex; gap: 12px;">
                                <button class="btn btn-sm btn-secondary" onclick="previousWeek()">← Previous</button>
                                <button class="btn btn-sm btn-secondary" onclick="nextWeek()">Next →</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="scheduleCalendar" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px;">
                            <!-- Populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="content-card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">📋 Today's Deliveries</h3>
                    </div>
                    <div class="card-content">
                        <div id="todaySchedule">
                            <!-- Populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>


            <div id="earnings-section" class="content-section" style="display: none;">
                <h1 style="margin-bottom: 24px;">Earnings Overview</h1>

                <div class="dashboard-stats" style="margin-bottom: 24px;">
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

                <div class="grid grid-2" style="margin-top: 24px; gap: 20px;">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">📊 Earnings Breakdown</h3>
                        </div>
                        <div class="card-content" style="padding: 20px;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Base Delivery Fee:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 8,500</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Distance Bonus:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 2,300</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Express Delivery:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 1,150</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Rating Bonus:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 500</span>
                                </div>
                                <hr style="margin: 16px 0; border: none; border-top: 2px solid #e0e0e0;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #e8f5e9; border-radius: 8px;">
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Total This Month:</span>
                                    <span style="font-weight: 700; font-size: 1.2rem; color: #65b57c;">Rs. 12,450</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">📈 Performance Metrics</h3>
                        </div>
                        <div class="card-content" style="padding: 20px;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Deliveries Completed:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">23</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Average Rating:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">⭐ 4.8/5</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">On-Time Delivery:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">95%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Customer Satisfaction:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">98%</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                                    <span style="font-size: 0.95rem; color: #2c3e50;">Earnings per Delivery:</span>
                                    <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">Rs. 541</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-card" style="margin-top: 20px;">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title">💳 Payment History</h3>
                        <button class="btn btn-secondary" onclick="exportPaymentHistory()">📥 Export CSV</button>
                    </div>
                    <div style="padding: var(--spacing-lg);">
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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h1> Vehicle Management</h1>
                    <button class="btn btn-primary" data-modal="addVehicleModal">➕ Add Vehicle</button>
                </div>

                <div id="myVehiclesContainer">

                </div>

                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div style="padding: var(--spacing-lg); border-bottom: 1px solid var(--medium-gray);">
                        <h3>  All Vehicles</h3>
                    </div>
                    <div style="padding: var(--spacing-lg);">
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

            <div id="profile-section" class="content-section" style="display: none;">
                <h1 style="margin-bottom: 24px;">Transporter Profile</h1>

                <div class="grid grid-2" style="gap: 20px;">
 
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Personal Information</h3>
                        </div>
                        <div class="card-content" style="padding: 20px;">
                            <form id="personalInfoForm">
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="profileName">Full Name</label>
                                    <input type="text" id="profileName" name="name" class="form-control" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="profileEmail">Email</label>
                                    <input type="email" id="profileEmail" name="email" class="form-control" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="profilePhone">Phone</label>
                                    <input type="tel" id="profilePhone" name="phone" class="form-control" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="profileNIC">NIC Number</label>
                                    <input type="text" id="profileNIC" name="nic" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Personal Info</button>
                            </form>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Business Information</h3>
                        </div>
                        <div class="card-content" style="padding: 20px;">
                            <form id="businessInfoForm">
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="businessName">Business Name</label>
                                    <input type="text" id="businessName" name="businessName" class="form-control">
                                </div>
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="businessType">Business Type</label>
                                    <select id="businessType" name="businessType" class="form-control">
                                        <option value="individual">Individual</option>
                                        <option value="company">Company</option>
                                        <option value="cooperative">Cooperative</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="serviceAreas">Service Areas</label>
                                    <input type="text" id="serviceAreas" name="serviceAreas" class="form-control" placeholder="e.g., Colombo, Kandy, Galle">
                                </div>
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="baseLocation">Base Location</label>
                                    <input type="text" id="baseLocation" name="baseLocation" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">Update Business Info</button>
                            </form>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Bank Information</h3>
                        </div>
                        <div class="card-content" style="padding: 20px;">
                            <form id="bankInfoForm">
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="bankName">Bank Name</label>
                                    <input type="text" id="bankName" name="bankName" class="form-control">
                                </div>
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="accountNumber">Account Number</label>
                                    <input type="text" id="accountNumber" name="accountNumber" class="form-control">
                                </div>
                                <div class="form-group" style="margin-bottom: 16px;">
                                    <label for="accountHolder">Account Holder Name</label>
                                    <input type="text" id="accountHolder" name="accountHolder" class="form-control">
                                </div>
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="branchCode">Branch Code</label>
                                    <input type="text" id="branchCode" name="branchCode" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">Update Bank Info</button>
                            </form>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Preferences</h3>
                        </div>
                        <div class="card-content" style="padding: 20px;">
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label for="maxDeliveryDistance">Maximum Delivery Distance (km)</label>
                                <input type="number" id="maxDeliveryDistance" class="form-control" value="50">
                            </div>
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label for="preferredWorkingHours">Preferred Working Hours</label>
                                <select id="preferredWorkingHours" class="form-control">
                                    <option value="full-time">Full-time (8AM-8PM)</option>
                                    <option value="morning">Morning (6AM-12PM)</option>
                                    <option value="afternoon">Afternoon (12PM-6PM)</option>
                                    <option value="evening">Evening (6PM-10PM)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 16px;">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" id="emailNotifications"> Email Notifications
                                </label>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" id="smsNotifications"> SMS Notifications
                                </label>
                            </div>
                            <button class="btn btn-primary">Save Preferences</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="analytics-section" class="content-section" style="display: none;">
                <h1 style="margin-bottom: 32px; font-size: 2rem;">Analytics & Performance</h1>

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
                    <div class="card">
                        <div style="padding: 24px; border-bottom: 1px solid var(--medium-gray);">
                            <h3> Monthly Performance</h3>
                        </div>
                        <div style="padding: 32px; text-align: center; color: var(--dark-gray);">
                            <div style="font-size: 4rem; margin-bottom: 20px;">📈</div>
                            <p>Monthly delivery and earnings chart</p>
                        </div>
                    </div>

                    <div class="card">
                        <div style="padding: 24px; border-bottom: 1px solid var(--medium-gray);">
                            <h3> Popular Routes</h3>
                        </div>
                        <div style="padding: 32px; text-align: center; color: var(--dark-gray);">
                            <div style="font-size: 4rem; margin-bottom: 20px;">🗺️</div>
                            <p>Most frequent delivery routes</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="addVehicleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>➕ Add New Vehicle</h3>
                <button class="modal-close">&times;</button>
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
                            <select id="vehicleFuelType" name="fuelType" class="form-control">
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

    <script src="assets/js/main.js"></script>
    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                localStorage.removeItem('user_id');
                localStorage.removeItem('user_email');
                localStorage.removeItem('user_role');
                localStorage.removeItem('user_name');
                localStorage.removeItem('business_name');
                window.location.href = 'auth/logout.php';
            }
        }
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
                    <span class="badge badge-success">Completed</span>
                </div>
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-002</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Galle → Matara - Rs. 650</div>
                    <span class="badge badge-warning">In Progress</span>
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
            `;
        }

        function loadSchedule() {
            const calendar = document.getElementById('scheduleCalendar');
            const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            
            calendar.innerHTML = days.map(day => `
                <div style="text-align: center; padding: 20px; background: #ffffff; border: 2px solid #e0e0e0; border-radius: 12px;">
                    <div style="font-weight: 600; margin-bottom: 8px; color: #2c3e50; font-size: 1.1rem;">${day}</div>
                    <div style="font-size: 0.9rem; color: #666;">2 deliveries</div>
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
            if (user) {
                document.getElementById('profileName').value = user.name || '';
                document.getElementById('profileEmail').value = user.email || '';
                document.getElementById('profilePhone').value = user.phone || '';
            }
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
            fetch('<?php echo ROOT; ?>/TransporterDashboard/getVehicles')
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
                            <span class="badge badge-${statusClass}">${statusText}</span>
                        </div>
                        <div class="card-content">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                <div>
                                    <div style="margin-bottom: 20px; line-height: 2;">
                                        <strong>Vehicle Type:</strong> ${vehicleTypeName}<br>
                                        <strong>Registration:</strong> ${escapeHtml(vehicle.registration)}<br>
                                        <strong>Capacity:</strong> ${escapeHtml(vehicle.capacity)}kg<br>
                                        <strong>Fuel Type:</strong> ${escapeHtml(vehicle.fuel_type || 'N/A')}<br>
                                        <strong>Status:</strong> <span class="badge badge-${statusClass}">${statusText}</span>
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
                                        <div style="margin-top: 16px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                                            <span class="badge badge-${statusClass}">${statusText.toUpperCase()}</span>
                                            ${vehicle.status === 'active' ? '<span class="badge badge-info">GPS ENABLED</span>' : ''}
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
                        <td><span class="badge badge-${statusClass}">${statusText}</span></td>
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
                    
                    fetch('<?php echo ROOT; ?>/TransporterDashboard/addVehicle', {
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
            fetch('<?php echo ROOT; ?>/TransporterDashboard/getVehicles')
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
                <div id="editVehicleModal" class="modal" style="display: block;" onclick="closeModalOnBackdrop(event, 'editVehicleModal')">
                    <div class="modal-content" onclick="event.stopPropagation()">
                        <div class="modal-header">
                            <h3>✏️ Edit Vehicle</h3>
                            <button class="modal-close" onclick="closeEditModal()">&times;</button>
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
                                
                                <div style="display: flex; gap: var(--spacing-md); margin-top: var(--spacing-lg);">
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
            
            fetch('<?php echo ROOT; ?>/TransporterDashboard/editVehicle/' + vehicleId, {
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
                fetch('<?php echo ROOT; ?>/TransporterDashboard/setActiveVehicle/' + vehicleId, {
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
                fetch('<?php echo ROOT; ?>/TransporterDashboard/deleteVehicle/' + vehicleId, {
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
