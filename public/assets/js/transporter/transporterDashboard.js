// Transporter Dashboard Specific Functionality
(function () {
'use strict';

let availableRequests = [];
let myDeliveries = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeTransporterDashboard();
    
    // Listen for hash changes
    window.addEventListener('hashchange', function() {
        const hash = window.location.hash.substring(1);
        if (hash && document.getElementById(hash + '-section')) {
            showSection(hash);
        }
    });
    
    // Check for section parameter in URL
    setTimeout(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const section = urlParams.get('section');
        if (section && document.getElementById(section + '-section')) {
            showSection(section);
        }
    }, 100);
});

function initializeTransporterDashboard() {
    loadDashboardData();
    loadVehicleTypes(); // Load vehicle types for the form
    if (typeof window.loadVehicles === 'function') {
        window.loadVehicles();
    }
    loadAvailableRequests();
    loadMyDeliveries(); // Load my deliveries on initialization
    loadRecentDeliveries(); // Load recent deliveries
    initializeEventListeners();
    
    // Initialize with default section
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section');
    if (section && document.getElementById(section + '-section')) {
        showSection(section);
    } else {
        showSection('dashboard');
    }
}

// Get base URL (use the APP_ROOT defined in the template)
function getBaseUrl() {
    const origin = String(window.location.origin || '').replace(/\/+$/, '');
    const path = String(window.location.pathname || '');

    // Prefer deriving from current URL when /public is present.
    const publicMatch = path.match(/^(.*\/public)(?:\/|$)/i);
    if (publicMatch && publicMatch[1]) {
        const baseUrl = origin + publicMatch[1];
        console.log('Constructed base URL from pathname:', baseUrl);
        return baseUrl;
    }

    // Next, use APP_ROOT if available.
    if (window.APP_ROOT) {
        const appRoot = String(window.APP_ROOT).replace(/\/+$/, '');
        console.log('Using APP_ROOT:', appRoot);
        return appRoot;
    }
    
    // Last resort: try to detect from script tags
    const scripts = document.querySelectorAll('script[src*="/assets/"]');
    if (scripts.length > 0) {
        const scriptSrc = scripts[0].getAttribute('src');
        const baseUrl = scriptSrc.substring(0, scriptSrc.indexOf('/assets')).replace(/\/+$/, '');
        console.log('Detected base URL from scripts:', baseUrl);
        return baseUrl;
    }
    
    console.error('Could not determine base URL!');
    return '';
}

// Load vehicle types from database
function loadVehicleTypes() {
    fetch(getBaseUrl() + '/transporterdashboard/getVehicleTypes', { credentials: 'include' })
        .then(r => r.json())
        .then(res => {
            const types = res.types || res.vehicleTypes || [];
            if (res.success && Array.isArray(types)) {
                const select = document.getElementById('vehicleType');
                if (select) {
                    select.innerHTML = '<option value="">Select Type...</option>' +
                        types.map(t => {
                            const slug = String(t.vehicle_name || '').toLowerCase().replace(/\s+/g, '');
                            return `<option value="${slug}" data-min="${t.min_weight_kg}" data-max="${t.max_weight_kg}" data-type-id="${t.id}">${escapeHtml(t.vehicle_name)}</option>`;
                        }
                        ).join('');
                }
            }
        })
        .catch(err => console.error('Error loading vehicle types:', err));
}

// Utility function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text || '').replace(/[&<>"']/g, m => map[m]);
}

function formatCurrencyValue(amount) {
    return `Rs. ${parseFloat(amount || 0).toFixed(2)}`;
}

function getLocationLabel(primaryTown, fallbackCity, districtName) {
    return primaryTown || fallbackCity || districtName || 'N/A';
}

function getRouteLabel(delivery) {
    const from = getLocationLabel(delivery.farmer_town_name, delivery.farmer_city, delivery.farmer_district_name);
    const to = getLocationLabel(delivery.buyer_town_name, delivery.buyer_city, delivery.buyer_district_name);
    return { from, to };
}

function normalizeDeliveryStatus(status) {
    if (status === 'completed') return 'delivered';
    if (status === 'in-progress') return 'in_transit';
    return status;
}

function formatVehicleRegistrationInput(value) {
    const raw = String(value || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
    const match = raw.match(/^([A-Z]{0,3})(\d{0,4})/);
    if (!match) return '';
    const letters = match[1] || '';
    const numbers = match[2] || '';
    return numbers ? `${letters} ${numbers}`.trim() : letters;
}

function attachVehicleRegistrationFormatter(input) {
    if (!input || input.dataset.formatted === '1') return;
    input.dataset.formatted = '1';
    input.addEventListener('input', function () {
        this.value = formatVehicleRegistrationInput(this.value);
    });
    input.value = formatVehicleRegistrationInput(input.value);
}

function applyPerformanceMetrics(performance) {
    if (!performance) return;

    const completed = document.getElementById('metricCompletedDeliveries');
    const average = document.getElementById('metricAverageRating');
    const onTime = document.getElementById('metricOnTimeDelivery');
    const satisfaction = document.getElementById('metricCustomerSatisfaction');
    const earningsPerDelivery = document.getElementById('metricEarningsPerDelivery');
    const weeklyRating = document.getElementById('weeklyRating');

    if (completed) completed.textContent = Number(performance.completed_deliveries || 0);
    if (average) average.textContent = `${parseFloat(performance.average_rating || 0).toFixed(1)}/5`;
    if (onTime) onTime.textContent = `${Math.round(parseFloat(performance.on_time_delivery_rate || 0))}%`;
    if (satisfaction) satisfaction.textContent = `${Math.round(parseFloat(performance.customer_satisfaction_rate || 0))}%`;
    if (earningsPerDelivery) earningsPerDelivery.textContent = formatCurrencyValue(performance.earnings_per_delivery || 0);
    if (weeklyRating) {
        weeklyRating.textContent = Number(performance.feedback_count || 0) > 0
            ? `${parseFloat(performance.average_rating || 0).toFixed(1)}/5 average rating`
            : 'No rating yet';
    }
}

function getAvailableRequestFilters() {
    return {
        location: document.getElementById('locationFilter')?.value || '',
        distance: parseFloat(document.getElementById('distanceFilter')?.value || ''),
        weight: parseFloat(document.getElementById('weightFilter')?.value || ''),
        payment: parseFloat(document.getElementById('paymentFilter')?.value || '')
    };
}

function matchesAvailableRequestFilters(request, filters) {
    const pickupLocation = getLocationLabel(request.farmer_town_name, request.farmer_city, request.farmer_district_name);
    const distance = parseFloat(request.distance_km || 0);
    const weight = parseFloat(request.total_weight_kg || 0);
    const payment = parseFloat(request.shipping_fee || 0);

    if (filters.location && pickupLocation.toLowerCase() !== filters.location.toLowerCase()) {
        return false;
    }
    if (!Number.isNaN(filters.distance) && filters.distance > 0 && distance > filters.distance) {
        return false;
    }
    if (!Number.isNaN(filters.weight) && filters.weight > 0 && weight > filters.weight) {
        return false;
    }
    if (!Number.isNaN(filters.payment) && filters.payment > 0 && payment < filters.payment) {
        return false;
    }
    return true;
}

function applyAvailableRequestFilters() {
    displayAvailableRequests(availableRequests, null);
}

function populatePickupLocationFilter(requests) {
    const select = document.getElementById('locationFilter');
    if (!select) return;

    const currentValue = select.value;
    const locations = Array.from(new Set((requests || []).map(request => getLocationLabel(request.farmer_town_name, request.farmer_city, request.farmer_district_name)).filter(Boolean))).sort();
    select.innerHTML = '<option value="">All Locations</option>' + locations.map(location => `<option value="${escapeHtml(location)}">${escapeHtml(location)}</option>`).join('');
    if (locations.includes(currentValue)) {
        select.value = currentValue;
    }
}

// Load dashboard data
function loadDashboardData() {
    // Load earnings summary
    const url = getBaseUrl() + '/transporterdashboard/getEarnings';
    console.log('Loading earnings from:', url);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.earnings) {
                updateDashboardStats(data.earnings);
                applyPerformanceMetrics(data.performance || null);
            }
        })
        .catch(error => console.error('Error loading earnings:', error));
}

// Load recent/completed deliveries
function loadRecentDeliveries() {
    const url = getBaseUrl() + '/transporterdashboard/getMyRequests?status=delivered';
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const deliveries = data.requests || [];
                displayRecentDeliveries(deliveries.slice(0, 5)); // Show last 5
                displayPaymentHistory(deliveries); // Show all in payment history
            }
        })
        .catch(error => console.error('Error loading recent deliveries:', error));
}

// Display recent deliveries in dashboard
function displayRecentDeliveries(deliveries) {
    const container = document.getElementById('recentDeliveries');
    if (!container) return;
    
    if (!deliveries || deliveries.length === 0) {
        container.innerHTML = '<div style="padding: 18px; text-align: center; color: #666;">No recent deliveries</div>';
        return;
    }
    
    container.innerHTML = deliveries.map(delivery => {
        const route = getRouteLabel(delivery);
        return `
        <div style="padding: 12px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">Order #${delivery.order_id}</div>
                <div style="font-size: 0.85rem; color: #666;">${delivery.farmer_city || delivery.farmer_district_name} → ${delivery.buyer_city || delivery.buyer_district_name}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 700; color: #4caf50;">${formatCurrencyValue(delivery.shipping_fee)}</div>
                <div style="font-size: 0.8rem; color: #666;">${new Date(delivery.updated_at).toLocaleDateString()}</div>
            </div>
        </div>
    `;
    }).join('');
}

// Display payment history in earnings section
function displayPaymentHistory(deliveries) {
    const tbody = document.getElementById('paymentHistoryBody');
    if (!tbody) return;
    
    if (!deliveries || deliveries.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                    No payment history yet
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = deliveries.map(delivery => {
        const date = new Date(delivery.updated_at);
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        return `
            <tr>
                <td>${formattedDate}</td>
                <td>#${delivery.order_id}</td>
                <td>
                    <div style="font-size: 0.9rem;">${delivery.farmer_city || delivery.farmer_district_name || 'N/A'}</div>
                    <div style="font-size: 0.85rem; color: #666;">to ${delivery.buyer_city || delivery.buyer_district_name || 'N/A'}</div>
                </td>
                <td>Rs. ${parseFloat(delivery.shipping_fee).toFixed(2)}</td>
                <td>
                    <span style="display: inline-block; padding: 4px 12px; background-color: #4caf5020; color: #4caf50; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">
                        Completed
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

// Update dashboard statistics
function updateDashboardStats(earnings) {
    // Dashboard section
    const weeklyEarnings = document.getElementById('weeklyEarnings');
    if (weeklyEarnings && earnings.week_earnings) {
        weeklyEarnings.textContent = `Rs. ${parseFloat(earnings.week_earnings).toFixed(2)}`;
    }
    
    const activeDeliveries = document.getElementById('activeDeliveries');
    if (activeDeliveries && earnings.active_deliveries !== undefined) {
        activeDeliveries.textContent = earnings.active_deliveries;
    }
    
    const completedDeliveries = document.getElementById('completedDeliveries');
    if (completedDeliveries && earnings.completed_deliveries !== undefined) {
        completedDeliveries.textContent = earnings.completed_deliveries;
    }
    
    const monthlyEarnings = document.getElementById('monthlyEarnings');
    if (monthlyEarnings && earnings.month_earnings) {
        monthlyEarnings.textContent = `Rs. ${parseFloat(earnings.month_earnings).toFixed(2)}`;
    }
    
    // Earnings section - detailed stats
    const todayEarnings = document.getElementById('todayEarnings');
    if (todayEarnings && earnings.today_earnings !== undefined) {
        todayEarnings.textContent = `Rs. ${parseFloat(earnings.today_earnings).toFixed(2)}`;
    }
    
    const weekEarnings = document.getElementById('weekEarnings');
    if (weekEarnings && earnings.week_earnings !== undefined) {
        weekEarnings.textContent = `Rs. ${parseFloat(earnings.week_earnings).toFixed(2)}`;
    }
    
    const monthEarningsDetail = document.getElementById('monthEarningsDetail');
    if (monthEarningsDetail && earnings.month_earnings !== undefined) {
        monthEarningsDetail.textContent = `Rs. ${parseFloat(earnings.month_earnings).toFixed(2)}`;
    }
    
    const totalEarningsDetail = document.getElementById('totalEarningsDetail');
    if (totalEarningsDetail && earnings.total_earnings !== undefined) {
        totalEarningsDetail.textContent = `Rs. ${parseFloat(earnings.total_earnings).toFixed(2)}`;
    }
    
    // Weekly summary details on dashboard
    const weeklyCompletedDeliveries = document.getElementById('weeklyCompletedDeliveries');
    if (weeklyCompletedDeliveries && earnings.completed_deliveries !== undefined) {
        const count = earnings.completed_deliveries || 0;
        weeklyCompletedDeliveries.textContent = `${count} ${count === 1 ? 'delivery' : 'deliveries'} completed`;
    }
    
    const weeklyPendingDeliveries = document.getElementById('weeklyPendingDeliveries');
    if (weeklyPendingDeliveries && earnings.active_deliveries !== undefined) {
        const count = earnings.active_deliveries || 0;
        weeklyPendingDeliveries.textContent = `${count} ${count === 1 ? 'delivery' : 'deliveries'} pending`;
    }
}

// Load available delivery requests
function loadAvailableRequests() {
    const container = document.getElementById('availableDeliveriesList');
    if (container) {
        container.innerHTML = '<div style="width: 100%; padding: 40px; text-align: center; color: #666;"><p>Loading delivery requests...</p></div>';
    }

    const url = getBaseUrl() + '/transporterdashboard/getAvailableRequests';
    console.log('Loading available requests from:', url);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Available requests response:', data);
            if (data.success) {
                availableRequests = data.requests || [];
                populatePickupLocationFilter(availableRequests);
                applyAvailableRequestFilters();
                const counter = document.getElementById('availableDeliveries');
                if (counter) {
                    counter.textContent = availableRequests.length;
                }
            } else {
                console.error('Failed to load requests:', data);
                if (container) {
                    container.innerHTML = `<div style="width: 100%; padding: 40px; text-align: center; color: ${data.missingFields ? '#7c5a03' : '#f44336'};"><p>${escapeHtml(data.message || 'Failed to load delivery requests')}</p></div>`;
                }
            }
        })
        .catch(error => {
            console.error('Error loading available requests:', error);
            if (container) {
                container.innerHTML = '<div style="width: 100%; padding: 40px; text-align: center; color: #f44336;"><p>Error loading delivery requests. Please try again.</p></div>';
            }
        });
}

// Display available delivery requests
function displayAvailableRequests(requests, debug) {
    const container = document.getElementById('availableDeliveriesList');
    if (!container) return;

    const filters = getAvailableRequestFilters();
    const filteredRequests = (requests || []).filter(request => matchesAvailableRequestFilters(request, filters));

    if (!filteredRequests || filteredRequests.length === 0) {
        container.innerHTML = `
            <div style="width: 100%; padding: 40px; text-align: center; color: #666; background: #f8f9fa; border-radius: 12px;">
                <p style="margin: 0; font-size: 1.1rem;">No delivery requests match the selected filters</p>
            </div>
        `;
        return;
    }

    container.innerHTML = filteredRequests.map(request => `
        <div class="delivery-card" style="min-width: 320px; max-width: 380px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px; flex-shrink: 0;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div>
                    <h4 style="margin: 0 0 4px 0; color: #2c3e50; font-size: 1.1rem;">Order #${request.order_id}</h4>
                    <span style="display: inline-block; padding: 4px 12px; background: #e3f2fd; color: #1976d2; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">${request.required_vehicle_type || 'Any Vehicle'}</span>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 1.4rem; font-weight: 700; color: #4caf50;">${formatCurrencyValue(request.shipping_fee)}</div>
                    <div style="font-size: 0.85rem; color: #666;">${parseFloat(request.total_weight_kg).toFixed(1)} kg</div>
                </div>
            </div>

            <div style="margin-bottom: 16px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                <div style="margin-bottom: 8px;">
                    <strong style="color: #2c3e50; font-size: 0.9rem;">📍 Pickup (Farmer)</strong>
                    <div style="color: #666; font-size: 0.9rem; margin-top: 4px;">${request.farmer_name}</div>
                    <div style="color: #666; font-size: 0.85rem;">${request.farmer_address || getLocationLabel(request.farmer_town_name, request.farmer_city, request.farmer_district_name)}</div>
                    ${request.farmer_phone ? `<div style="color: #666; font-size: 0.85rem;">📞 ${request.farmer_phone}</div>` : ''}
                </div>
            </div>

            <div style="margin-bottom: 16px; padding: 12px; background: #fff3e0; border-radius: 8px;">
                <div>
                    <strong style="color: #2c3e50; font-size: 0.9rem;">🎯 Delivery (Buyer)</strong>
                    <div style="color: #666; font-size: 0.9rem; margin-top: 4px;">${request.buyer_name}</div>
                    <div style="color: #666; font-size: 0.85rem;">${request.buyer_address || getLocationLabel(request.buyer_town_name, request.buyer_city, request.buyer_district_name)}</div>
                    ${request.buyer_phone ? `<div style="color: #666; font-size: 0.85rem;">📞 ${request.buyer_phone}</div>` : ''}
                </div>
            </div>

            ${request.distance_km ? `<div style="margin-bottom: 12px; color: #666; font-size: 0.9rem;">📏 Distance: ~${parseFloat(request.distance_km).toFixed(1)} km</div>` : ''}

            <button onclick="TransporterDashboard.acceptDeliveryRequest(${request.id})" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 600;">
                Accept Delivery
            </button>
        </div>
    `).join('');
}

// Accept delivery request
function acceptDeliveryRequest(requestId) {
    console.log('Accept button clicked for request:', requestId);
    console.log('Base URL:', getBaseUrl());
    
    if (!confirm('Are you sure you want to accept this delivery request?')) {
        return;
    }

    const url = getBaseUrl() + `/transporterdashboard/acceptRequest/${requestId}`;
    console.log('Sending request to:', url);

    fetch(url, {
        method: 'POST'
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showNotification(data.message, 'success');
            // Refresh the data without full page reload
            loadAvailableRequests(); // Refresh available requests
            loadMyDeliveries(); // Refresh my deliveries
            loadDashboardData(); // Update dashboard stats
            loadRecentDeliveries();
            if (typeof window.loadCurrentTransporterStatus === 'function') {
                window.loadCurrentTransporterStatus();
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error accepting request:', error);
        showNotification('Failed to accept delivery request', 'error');
    });
}

// Load my deliveries
function loadMyDeliveries(status = null) {
    const normalizedStatus = normalizeDeliveryStatus(status);
    const url = getBaseUrl() + (normalizedStatus ? `/transporterdashboard/getMyRequests?status=${normalizedStatus}` : '/transporterdashboard/getMyRequests');
    console.log('Loading my deliveries from:', url);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                myDeliveries = data.requests || [];
                displayMyDeliveries(myDeliveries);
            }
        })
        .catch(error => console.error('Error loading my deliveries:', error));
}

// Display my deliveries
function displayMyDeliveries(deliveries) {
    const tbody = document.getElementById('myDeliveriesTableBody');
    if (!tbody) return;

    if (!deliveries || deliveries.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                    No deliveries found
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = deliveries.map(delivery => {
        const statusColors = {
            'pending': '#ff9800',
            'accepted': '#2196f3',
            'in_transit': '#9c27b0',
            'delivered': '#4caf50',
            'cancelled': '#f44336'
        };
        const statusColor = statusColors[delivery.status] || '#666';
        const route = getRouteLabel(delivery);
        const deadlineText = delivery.expected_delivery_at
            ? new Date(delivery.expected_delivery_at).toLocaleDateString()
            : (delivery.created_at ? new Date(delivery.created_at).toLocaleDateString() : 'N/A');
        
        return `
            <tr>
                <td>#${delivery.order_id}</td>
                <td>
                    <div style="font-size: 0.9rem;">${escapeHtml(route.from)}</div>
                    <div style="font-size: 0.85rem; color: #666;">to ${escapeHtml(route.to)}</div>
                </td>
                <td>${delivery.distance_km ? `${parseFloat(delivery.distance_km).toFixed(1)} km` : 'N/A'}</td>
                <td>${parseFloat(delivery.total_weight_kg).toFixed(1)} kg</td>
                <td>${formatCurrencyValue(delivery.shipping_fee)}</td>
                <td>
                    <span style="display: inline-block; padding: 4px 12px; background-color: ${statusColor}20; color: ${statusColor}; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">
                        ${delivery.status.replace('_', ' ').toUpperCase()}
                    </span>
                </td>
                <td>${deadlineText}</td>
                <td>
                    ${delivery.status === 'accepted' ? `
                        <button onclick="TransporterDashboard.updateDeliveryStatus(${delivery.id}, 'in_transit')" class="btn btn-sm btn-primary">Start Transit</button>
                    ` : ''}
                    ${delivery.status === 'in_transit' ? `
                        <button onclick="TransporterDashboard.updateDeliveryStatus(${delivery.id}, 'delivered')" class="btn btn-sm btn-success">Mark Delivered</button>
                    ` : ''}
                    ${delivery.status === 'delivered' ? `
                        <span style="color: #4caf50; font-weight: 500;">✓ Completed</span>
                    ` : ''}
                </td>
            </tr>
        `;
    }).join('');
}

// Update delivery status
function updateDeliveryStatus(deliveryId, newStatus) {
    const confirmMessages = {
        'in_transit': 'Mark this delivery as in transit?',
        'delivered': 'Mark this delivery as delivered?',
        'cancelled': 'Cancel this delivery?'
    };

    if (!confirm(confirmMessages[newStatus] || 'Update delivery status?')) {
        return;
    }

    fetch(getBaseUrl() + `/transporterdashboard/updateDeliveryStatus/${deliveryId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadMyDeliveries(); // Refresh list
            loadDashboardData(); // Update earnings
            loadRecentDeliveries(); // Update recent deliveries
            loadAvailableRequests();
            if (typeof window.loadCurrentTransporterStatus === 'function') {
                window.loadCurrentTransporterStatus();
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        showNotification('Failed to update delivery status', 'error');
    });
}

// Filter my deliveries by status
function filterMyDeliveries(status) {
    // Update active tab
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const selectedBtn = document.querySelector(`.tab-btn[data-status="${status}"]`);
    if (selectedBtn) {
        selectedBtn.classList.add('active');
    }

    // Load deliveries with filter
    const filterStatus = status === 'all' ? null : normalizeDeliveryStatus(status);
    loadMyDeliveries(filterStatus);
}

// Refresh available deliveries
function refreshDeliveries() {
    showNotification('Refreshing delivery requests...', 'info');
    loadAvailableRequests();
}

// Initialize event listeners for dashboard
function initializeEventListeners() {
    const sectionLinks = document.querySelectorAll('.menu-link');

    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Check if link has a section to show
            const section = this.dataset.section;
            if (section) {
                e.preventDefault();
                showSection(section);
            }
        });
    });

    // Vehicle type selection - show weight range
    const vehicleTypeSelect = document.getElementById('vehicleType');
    if (vehicleTypeSelect) {
        vehicleTypeSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const display = document.getElementById('weightRangeDisplay');
            const text = document.getElementById('weightRangeText');

            if (selectedOption.value) {
                const min = selectedOption.dataset.min;
                const max = selectedOption.dataset.max;
                text.textContent = `${min}-${max}kg`;
                display.style.display = 'block';
            } else {
                display.style.display = 'none';
            }
        });
    }

    ['locationFilter', 'distanceFilter', 'weightFilter', 'paymentFilter'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', applyAvailableRequestFilters);
        }
    });

    attachVehicleRegistrationFormatter(document.getElementById('vehicleRegistration'));
}

// Show specific section
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show target section
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.style.display = 'block';
    }
    
    // Update active menu link
    document.querySelectorAll('.menu-link').forEach(link => {
        link.classList.remove('active');
        if (link.dataset.section === sectionName) {
            link.classList.add('active');
        }
    });
    
    // Scroll to top
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    
    // Load section-specific data
    if (sectionName === 'mydeliveries') {
        loadMyDeliveries();
    } else if (sectionName === 'available-deliveries') {
        loadAvailableRequests();
    } else if (sectionName === 'vehicle' && typeof window.loadVehicles === 'function') {
        window.loadVehicles();
    } else if (sectionName === 'schedule' && typeof window.loadSchedule === 'function') {
        window.loadSchedule();
    } else if (sectionName === 'earnings') {
        loadDashboardData(); // Load earnings data
        loadRecentDeliveries(); // Load payment history
    } else if (sectionName === 'feedback' && typeof window.loadFeedbackReviews === 'function') {
        window.loadFeedbackReviews();
    } else if (sectionName === 'dashboard') {
        loadRecentDeliveries(); // Refresh recent deliveries
    }
}

// Show notification
function showNotification(message, type = 'info') {
    console.log('Showing notification:', message, type);
    
    // Try to use the existing notification element from the page
    const notification = document.getElementById('notification');
    if (notification) {
        notification.textContent = message;
        notification.className = `notification ${type} show`;

        setTimeout(() => {
            notification.classList.remove('show');
        }, 4000);
    } else {
        // Fallback: create floating notification
        const notificationDiv = document.createElement('div');
        notificationDiv.className = `notification ${type} show`;
        notificationDiv.textContent = message;
        notificationDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; padding: 15px 20px; background: ' + 
            (type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3') + 
            '; color: white; border-radius: 4px; z-index: 10000; box-shadow: 0 2px 5px rgba(0,0,0,0.2);';
        document.body.appendChild(notificationDiv);
        
        setTimeout(() => {
            notificationDiv.remove();
        }, 4000);
    }
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-LK', {
        style: 'currency',
        currency: 'LKR'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    return new Intl.DateTimeFormat('en-KE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(dateString));
}

// Export CSV function for payment history
function exportPaymentHistory() {
    fetch(getBaseUrl() + '/transporterdashboard/getMyRequests?status=delivered')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.requests && data.requests.length > 0) {
                const csv = convertToCSV(data.requests);
                downloadCSV(csv, 'payment_history.csv');
                showNotification('Payment history exported successfully', 'success');
            } else {
                showNotification('No payment history to export', 'info');
            }
        })
        .catch(error => {
            console.error('Error exporting:', error);
            showNotification('Failed to export payment history', 'error');
        });
}

function convertToCSV(deliveries) {
    const headers = ['Date', 'Order ID', 'From', 'To', 'Payment', 'Status'];
    const rows = deliveries.map(d => {
        const route = getRouteLabel(d);
        return [
            new Date(d.updated_at).toLocaleDateString(),
            d.order_id,
            route.from,
            route.to,
            parseFloat(d.shipping_fee).toFixed(2),
            'Completed'
        ];
    });
    
    const csvContent = [
        headers.join(','),
        ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
    ].join('\n');
    
    return csvContent;
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Namespaced API for inline handlers
window.TransporterDashboard = {
    showSection,
    acceptDeliveryRequest,
    updateDeliveryStatus,
    filterMyDeliveries,
    refreshDeliveries,
    exportPaymentHistory
};

// Backward-compatible aliases (temporary, do not override existing globals)
if (typeof window.showSection !== 'function') window.showSection = window.TransporterDashboard.showSection;
if (typeof window.acceptDeliveryRequest !== 'function') window.acceptDeliveryRequest = window.TransporterDashboard.acceptDeliveryRequest;
if (typeof window.updateDeliveryStatus !== 'function') window.updateDeliveryStatus = window.TransporterDashboard.updateDeliveryStatus;
if (typeof window.filterMyDeliveries !== 'function') window.filterMyDeliveries = window.TransporterDashboard.filterMyDeliveries;
if (typeof window.refreshDeliveries !== 'function') window.refreshDeliveries = window.TransporterDashboard.refreshDeliveries;
if (typeof window.exportPaymentHistory !== 'function') window.exportPaymentHistory = window.TransporterDashboard.exportPaymentHistory;
})();


// Transporter dashboard extras extracted from inline view script.
(function () {
'use strict';

let vehicleTypesData = [];

function notify(message, type) {
    const text = String(message || '').trim() || 'Notification';

    if (typeof window.showNotification === 'function') {
        window.showNotification(text, type || 'info');
        return;
    }

    const notification = document.getElementById('notification');
    if (notification) {
        notification.textContent = text;
        notification.className = `notification ${type || 'info'} show`;
        setTimeout(() => notification.classList.remove('show'), 2200);
        return;
    }

    alert(text);
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

function parseJsonResponse(response) {
    return response.text().then((raw) => {
        try {
            return JSON.parse(raw);
        } catch (error) {
            throw new Error('Invalid JSON response');
        }
    });
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, (m) => map[m]);
}

function closeModalSafe(modalId) {
    if (!modalId) return;

    if (typeof window.closeModal === 'function') {
        window.closeModal(modalId);
        return;
    }

    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function toggleAvailability() {
    const btn = document.getElementById('availabilityBtn');
    const status = document.getElementById('currentStatus');
    const indicator = document.getElementById('statusIndicator');

    if (!btn || !status || !indicator) return;

    if (status.textContent === 'Available') {
        status.textContent = 'Offline';
        btn.textContent = 'Go Online';
        indicator.style.background = '#f44336';
        notify('You are now offline', 'info');
    } else {
        status.textContent = 'Available';
        btn.textContent = 'Go Offline';
        indicator.style.background = '#4CAF50';
        notify('You are now available for deliveries', 'success');
    }
}

function updateLocation() {
    notify('Location updated successfully', 'success');
}

function loadSchedule() {
    const calendar = document.getElementById('scheduleCalendar');
    const todaySchedule = document.getElementById('todaySchedule');
    if (!calendar || !todaySchedule) return;

    const today = new Date();
    const options = { weekday: 'short', month: 'short', day: 'numeric' };

    const next3Days = [0, 1, 2].map((offset) => {
        const d = new Date(today);
        d.setDate(today.getDate() + offset);
        return d;
    });

    calendar.innerHTML = next3Days.map((date, idx) => `
        <div style="padding: 16px; background: #ffffff; border: 2px solid #e0e0e0; border-radius: 12px;">
            <div style="font-weight: 700; margin-bottom: 10px; color: #2c3e50;">${date.toLocaleDateString(undefined, options)}</div>
            <div style="display: grid; gap: 10px;">
                <div style="padding: 10px; background:#f8f9fa; border-radius:8px;">
                    <div style="font-weight:600; color:#2c3e50;">08:30 AM - Pickup</div>
                    <div style="color:#666; font-size:0.9rem;">Order #ORD-2025-00${7 + idx}</div>
                </div>
                <div style="padding: 10px; background:#f8f9fa; border-radius:8px;">
                    <div style="font-weight:600; color:#2c3e50;">01:45 PM - Delivery</div>
                    <div style="color:#666; font-size:0.9rem;">Order #ORD-2025-00${6 + idx}</div>
                </div>
            </div>
        </div>
    `).join('');

    todaySchedule.innerHTML = `
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

function loadFeedbackReviews() {
    fetch(transporterApi('getFeedbackReviews'), { credentials: 'include' })
        .then(parseJsonResponse)
        .then((data) => {
            const reviews = (data.success && Array.isArray(data.reviews)) ? data.reviews : [];
            const unifiedEl = document.getElementById('feedbackUnifiedList');
            const totalEl = document.getElementById('feedbackTotalCount');
            const avgEl = document.getElementById('feedbackAvgRating');
            const complaintsCountEl = document.getElementById('feedbackComplaintCount');

            const complaints = reviews.filter((r) => Number(r.rating) <= 2);
            const avg = reviews.length
                ? (reviews.reduce((sum, r) => sum + Number(r.rating || 0), 0) / reviews.length)
                : 0;

            if (totalEl) totalEl.textContent = String(reviews.length);
            if (avgEl) avgEl.textContent = avg.toFixed(1);
            if (complaintsCountEl) complaintsCountEl.textContent = String(complaints.length);

            const renderCard = (item) => {
                const rating = Number(item.rating || 0);
                const isComplaint = rating <= 2;
                const boundedRating = Math.min(Math.max(rating, 0), 5);
                const stars = `${'&#9733;'.repeat(boundedRating)}${'&#9734;'.repeat(Math.max(0, 5 - boundedRating))}`;
                const buyerName = escapeHtml(item.buyer_name || 'Buyer');
                const buyerInitial = buyerName.charAt(0).toUpperCase();
                const created = item.created_at ? new Date(item.created_at).toLocaleDateString() : '-';
                const orderText = item.order_id ? `#${item.order_id}` : '-';
                const productText = escapeHtml(item.product_name || 'Order Item');
                const deliveryLabel = String(item.delivery_id || item.delivery_request_id || '').trim();
                const deliveryText = deliveryLabel !== '' ? `#${escapeHtml(deliveryLabel)}` : '-';
                const routeFrom = escapeHtml(item.farmer_city || item.pickup_city || item.farmer_district_name || 'Pickup unavailable');
                const routeTo = escapeHtml(item.buyer_city || item.dropoff_city || item.buyer_district_name || 'Dropoff unavailable');
                const deliveryStatus = escapeHtml(String(item.delivery_status || item.order_status || 'unknown').replace('_', ' '));
                const reviewedQty = Number(item.reviewed_quantity || 0);
                const quantityText = Number.isFinite(reviewedQty)
                    ? reviewedQty.toLocaleString(undefined, { maximumFractionDigits: 2 })
                    : '-';

                return `
                    <div class="review-card transporter-review-card">
                        <div class="transporter-review-header">
                            <div class="transporter-review-person">
                                <div class="buyer-avatar">${buyerInitial}</div>
                                <div class="transporter-review-meta">
                                    <h4>Order ${orderText}</h4>
                                    <p>Feedback by <strong>${buyerName}</strong></p>
                                    <p class="transporter-review-date">${created}</p>
                                </div>
                            </div>
                            <div class="transporter-review-rating">
                                <div class="star-rating">${stars}</div>
                                ${isComplaint ? '<span class="feedback-badge negative">Complaint</span>' : ''}
                            </div>
                        </div>

                        <div class="transporter-review-facts">
                            <div class="transporter-review-fact">
                                <span class="transporter-review-fact-label">Order Number</span>
                                <span class="transporter-review-fact-value">${orderText}</span>
                            </div>
                            <div class="transporter-review-fact">
                                <span class="transporter-review-fact-label">Products</span>
                                <span class="transporter-review-fact-value">${productText}</span>
                            </div>
                            <div class="transporter-review-fact">
                                <span class="transporter-review-fact-label">Quantities</span>
                                <span class="transporter-review-fact-value">${quantityText}</span>
                            </div>
                            <div class="transporter-review-fact">
                                <span class="transporter-review-fact-label">Delivery ID</span>
                                <span class="transporter-review-fact-value">${deliveryText}</span>
                            </div>
                            <div class="transporter-review-fact">
                                <span class="transporter-review-fact-label">Route</span>
                                <span class="transporter-review-fact-value">${routeFrom} - ${routeTo}</span>
                            </div>
                            <div class="transporter-review-fact">
                                <span class="transporter-review-fact-label">Status</span>
                                <span class="transporter-review-fact-value">${deliveryStatus}</span>
                            </div>
                        </div>

                        <div class="transporter-review-body ${isComplaint ? 'is-complaint' : ''}">
                            <p>${escapeHtml(item.comment || '')}</p>
                        </div>
                    </div>
                `;
            };

            if (unifiedEl) {
                unifiedEl.innerHTML = reviews.length
                    ? `<div class="reviews-grid transporter-feedback-grid">${reviews.map(renderCard).join('')}</div>`
                    : '<div class="transporter-feedback-empty">No feedback yet</div>';
            }
        })
        .catch((error) => {
            console.error('Error loading feedback reviews:', error);
        });
}

function vehicleNameToSlug(name) {
    return String(name || '').toLowerCase().replace(/\s+/g, '');
}

function getVehicleTypeName(slug) {
    if (!slug) return '';
    const normalized = String(slug).toLowerCase();

    const matched = vehicleTypesData.find((vt) => vehicleNameToSlug(vt.vehicle_name) === normalized);
    if (matched) {
        return matched.vehicle_name;
    }

    return normalized.charAt(0).toUpperCase() + normalized.slice(1).replace(/([a-z])([A-Z])/g, '$1 $2');
}

function getVehicleIcon(type) {
    const slug = String(type || '').toLowerCase();
    if (slug.includes('bike') || slug.includes('motor')) return 'Bike';
    if (slug.includes('three') || slug.includes('wheel')) return 'Three Wheeler';
    if (slug.includes('car')) return 'Car';
    if (slug.includes('van')) return 'Van';
    if (slug.includes('lorry') || slug.includes('truck')) return 'Truck';
    return 'Vehicle';
}

function generateVehicleTypeOptions(selectedType) {
    const selectedSlug = String(selectedType || '').toLowerCase();
    let options = '<option value="">Select Type</option>';

    vehicleTypesData.forEach((vt) => {
        const slug = vehicleNameToSlug(vt.vehicle_name);
        const selected = (slug === selectedSlug) ? 'selected' : '';
        options += `<option value="${slug}" data-min="${vt.min_weight_kg}" data-max="${vt.max_weight_kg}" data-type-id="${vt.id}" ${selected}>${vt.vehicle_name} (${vt.min_weight_kg}-${vt.max_weight_kg}kg)</option>`;
    });

    return options;
}

function loadVehicleTypes() {
    fetch(transporterApi('getVehicleTypes'), { credentials: 'include' })
        .then(parseJsonResponse)
        .then((data) => {
            const types = data.types || data.vehicleTypes || [];
            if (!data.success || !Array.isArray(types)) return;

            vehicleTypesData = types;

            const select = document.getElementById('vehicleType');
            if (select && select.options.length <= 1) {
                select.innerHTML = '<option value="">Select Type...</option>' + types.map((t) => {
                    const slug = String(t.vehicle_name || '').toLowerCase().replace(/\s+/g, '');
                    return `<option value="${slug}" data-min="${t.min_weight_kg}" data-max="${t.max_weight_kg}" data-type-id="${t.id}">${escapeHtml(t.vehicle_name)} (${t.min_weight_kg}-${t.max_weight_kg}kg)</option>`;
                }).join('');
            }
        })
        .catch((error) => {
            console.error('Error loading vehicle types:', error);
        });
}

function updateCurrentStatus(vehicles) {
    const activeVehicleSpan = document.getElementById('activeVehicle');
    if (!activeVehicleSpan) return;

    if (!vehicles || vehicles.length === 0) {
        activeVehicleSpan.textContent = 'No vehicles added';
        activeVehicleSpan.style.color = '#666';
        return;
    }

    const activeVehicle = vehicles.find((v) => v.status === 'active');
    if (activeVehicle) {
        const vehicleName = activeVehicle.model || getVehicleTypeName(activeVehicle.type);
        activeVehicleSpan.textContent = `${vehicleName} (${activeVehicle.registration})`;
        activeVehicleSpan.style.color = '#65b57c';
        activeVehicleSpan.style.fontWeight = '700';
        return;
    }

    const firstVehicle = vehicles[0];
    const firstName = firstVehicle.model || getVehicleTypeName(firstVehicle.type);
    activeVehicleSpan.textContent = `${firstName} (${firstVehicle.registration}) - ${firstVehicle.status}`;
    activeVehicleSpan.style.color = '#666';
}

function displayVehicles(vehicles) {
    const container = document.getElementById('myVehiclesContainer');
    const tbody = document.getElementById('vehiclesTableBody');
    if (!container || !tbody) return;

    if (!vehicles || vehicles.length === 0) {
        container.innerHTML = `
            <div class="content-card">
                <div style="padding: 60px 20px; text-align: center; color: #666;">
                    <div style="font-size: 1.4rem; margin-bottom: 20px;">No vehicles yet</div>
                    <p>Click "Add Vehicle" to add your first vehicle.</p>
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

    container.innerHTML = vehicles.map((vehicle) => {
        const statusText = String(vehicle.status || '').charAt(0).toUpperCase() + String(vehicle.status || '').slice(1);
        const vehicleTypeName = getVehicleTypeName(vehicle.type);

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
                                <strong>Capacity:</strong> ${escapeHtml(vehicle.capacity)}kg<br>
                                <strong>Fuel Type:</strong> ${escapeHtml(vehicle.fuel_type || 'N/A')}<br>
                                <strong>Status:</strong> <span class="badge">${statusText}</span>
                            </div>
                            <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 20px;">
                                ${vehicle.status !== 'active' ? `<button class="btn btn-primary" onclick="setActiveVehicle(${vehicle.id})">Set as Active</button>` : ''}
                                <button class="btn btn-outline" onclick="editVehicleModal(${vehicle.id})">Edit</button>
                                <button class="btn btn-outline" onclick="deleteVehicleConfirm(${vehicle.id})">Delete</button>
                            </div>
                        </div>
                        <div>
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 24px; text-align: center;">
                                <div style="font-size: 1.2rem; margin-bottom: 20px;">${getVehicleIcon(vehicle.type)}</div>
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

    tbody.innerHTML = vehicles.map((vehicle) => {
        const statusText = String(vehicle.status || '').charAt(0).toUpperCase() + String(vehicle.status || '').slice(1);

        return `
            <tr>
                <td>${escapeHtml(vehicle.model || 'N/A')}</td>
                <td>${escapeHtml(vehicle.registration)}</td>
                <td>${getVehicleTypeName(vehicle.type)}</td>
                <td>${escapeHtml(vehicle.capacity)}kg</td>
                <td><span class="badge">${statusText}</span></td>
                <td>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        ${vehicle.status !== 'active' ? `<button class="btn btn-sm btn-primary" onclick="setActiveVehicle(${vehicle.id})">Set Active</button>` : ''}
                        <button class="btn btn-sm btn-outline" onclick="editVehicleModal(${vehicle.id})">Edit</button>
                        <button class="btn btn-sm btn-outline" onclick="deleteVehicleConfirm(${vehicle.id})">Delete</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function loadVehicles() {
    fetch(transporterApi('getVehicles'), { credentials: 'include' })
        .then(parseJsonResponse)
        .then((data) => {
            if (data.success && Array.isArray(data.vehicles)) {
                displayVehicles(data.vehicles);
                updateCurrentStatus(data.vehicles);
                return;
            }
            displayVehicles([]);
            updateCurrentStatus([]);
        })
        .catch((error) => {
            console.error('Error loading vehicles:', error);
            displayVehicles([]);
            updateCurrentStatus([]);
            notify('Failed to load vehicles', 'error');
        });
}

function setupAddVehicleForm() {
    const form = document.getElementById('addVehicleForm');
    if (!form || form.dataset.bound === '1') return;

    form.dataset.bound = '1';
    form.addEventListener('submit', function (e) {
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
            body: formData,
        })
            .then(parseJsonResponse)
            .then((data) => {
                if (data.success) {
                    notify(data.message || 'Vehicle added successfully', 'success');
                    form.reset();
                    closeModalSafe('addVehicleModal');
                    loadVehicles();
                    return;
                }

                if (data.errors && typeof data.errors === 'object') {
                    const firstError = Object.values(data.errors)[0];
                    notify(firstError || 'Validation failed', 'error');
                } else {
                    notify(data.message || 'Failed to add vehicle', 'error');
                }
            })
            .catch((error) => {
                console.error('Error adding vehicle:', error);
                notify('Failed to add vehicle. Please try again.', 'error');
            });
    });
}

function editVehicleModal(vehicleId) {
    fetch(transporterApi('getVehicles'), { credentials: 'include' })
        .then(parseJsonResponse)
        .then((data) => {
            if (!data.success || !Array.isArray(data.vehicles)) return;
            const vehicle = data.vehicles.find((v) => Number(v.id) === Number(vehicleId));
            if (vehicle) {
                showEditVehicleModal(vehicle);
            }
        })
        .catch((error) => {
            console.error('Error loading vehicle for edit:', error);
            notify('Failed to load vehicle data', 'error');
        });
}

function showEditVehicleModal(vehicle) {
    fetch(transporterApi('getVehicleTypes'), { credentials: 'include' })
        .then(parseJsonResponse)
        .then((res) => {
            const types = res.types || res.vehicleTypes || [];
            if (!res.success || !types.length) {
                notify('Failed to load vehicle types', 'error');
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
                            <form id="editVehicleForm" onsubmit="submitEditVehicle(event, ${Number(vehicle.id)})">
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
            if (existingModal) existingModal.remove();
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const typeSelect = document.getElementById('editVehicleType');
            if (!typeSelect) return;

            const syncWeightRange = () => {
                const option = typeSelect.options[typeSelect.selectedIndex];
                const display = document.getElementById('editWeightRangeDisplay');
                const text = document.getElementById('editWeightRangeText');
                if (!display || !text) return;

                if (option && option.value) {
                    text.textContent = `${option.dataset.min}-${option.dataset.max}kg`;
                    display.style.display = 'block';
                } else {
                    display.style.display = 'none';
                }
            };

            syncWeightRange();
            typeSelect.addEventListener('change', syncWeightRange);
        })
        .catch((error) => {
            console.error('Error loading vehicle types:', error);
            notify('Failed to load vehicle types', 'error');
        });
}

function closeEditModal() {
    const modal = document.getElementById('editVehicleModal');
    if (modal) modal.remove();
}

function closeModalOnBackdrop(event, modalId) {
    if (event.target.id === modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.remove();
    }
}

function submitEditVehicle(event, vehicleId) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const typeSelect = document.getElementById('editVehicleType');
    if (typeSelect) {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const selectedTypeId = selectedOption ? selectedOption.dataset.typeId : '';
        if (selectedTypeId) {
            formData.set('vehicle_type_id', selectedTypeId);
        }
    }

    fetch(transporterApi(`editVehicle/${vehicleId}`), {
        method: 'POST',
        credentials: 'include',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
        .then(parseJsonResponse)
        .then((data) => {
            if (data.success) {
                notify(data.message || 'Vehicle updated successfully', 'success');
                closeEditModal();
                loadVehicles();
                return;
            }

            if (data.errors && typeof data.errors === 'object') {
                const firstError = Object.values(data.errors)[0];
                notify(firstError || 'Validation failed', 'error');
            } else {
                notify(data.message || 'Failed to update vehicle', 'error');
            }
        })
        .catch((error) => {
            console.error('Error updating vehicle:', error);
            notify('Failed to update vehicle. Please try again.', 'error');
        });
}

function setActiveVehicle(vehicleId) {
    if (!confirm('Set this vehicle as active? This will deactivate all other vehicles.')) {
        return;
    }

    fetch(transporterApi(`setActiveVehicle/${vehicleId}`), {
        method: 'POST',
        credentials: 'include',
    })
        .then(parseJsonResponse)
        .then((data) => {
            if (data.success) {
                notify(data.message || 'Vehicle set as active', 'success');
                loadVehicles();
            } else {
                notify(data.message || 'Failed to set active vehicle', 'error');
            }
        })
        .catch((error) => {
            console.error('Error setting active vehicle:', error);
            notify('Failed to set active vehicle. Please try again.', 'error');
        });
}

function deleteVehicleConfirm(vehicleId) {
    if (!confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
        return;
    }

    fetch(transporterApi(`deleteVehicle/${vehicleId}`), {
        method: 'POST',
        credentials: 'include',
    })
        .then(parseJsonResponse)
        .then((data) => {
            if (data.success) {
                notify(data.message || 'Vehicle deleted', 'success');
                loadVehicles();
            } else {
                notify(data.message || 'Failed to delete vehicle', 'error');
            }
        })
        .catch((error) => {
            console.error('Error deleting vehicle:', error);
            notify('Failed to delete vehicle. Please try again.', 'error');
        });
}

window.toggleAvailability = toggleAvailability;
window.updateLocation = updateLocation;
window.loadSchedule = loadSchedule;
window.loadFeedbackReviews = loadFeedbackReviews;
window.loadVehicles = loadVehicles;
window.setupAddVehicleForm = setupAddVehicleForm;
window.editVehicleModal = editVehicleModal;
window.closeEditModal = closeEditModal;
window.closeModalOnBackdrop = closeModalOnBackdrop;
window.submitEditVehicle = submitEditVehicle;
window.setActiveVehicle = setActiveVehicle;
window.deleteVehicleConfirm = deleteVehicleConfirm;

document.addEventListener('DOMContentLoaded', function () {
    loadVehicleTypes();
    setupAddVehicleForm();

    const sectionParam = new URLSearchParams(window.location.search).get('section');
    if (sectionParam === 'feedback') {
        loadFeedbackReviews();
    }
    if (sectionParam === 'schedule') {
        loadSchedule();
    }

    if (document.getElementById('myVehiclesContainer')) {
        loadVehicles();
    }
});
})();
