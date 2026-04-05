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
    if (typeof loadVehicles === 'function') {
        loadVehicles();
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
    return text.replace(/[&<>"']/g, m => map[m]);
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
    
    container.innerHTML = deliveries.map(delivery => `
        <div style="padding: 12px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 600; color: #2c3e50; margin-bottom: 4px;">Order #${delivery.order_id}</div>
                <div style="font-size: 0.85rem; color: #666;">${delivery.farmer_city || delivery.farmer_district_name} → ${delivery.buyer_city || delivery.buyer_district_name}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 700; color: #4caf50;">Rs. ${parseFloat(delivery.shipping_fee).toFixed(2)}</div>
                <div style="font-size: 0.8rem; color: #666;">${new Date(delivery.updated_at).toLocaleDateString()}</div>
            </div>
        </div>
    `).join('');
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
                displayAvailableRequests(availableRequests, data.debug);
            } else {
                console.error('Failed to load requests:', data);
                if (container) {
                    container.innerHTML = '<div style="width: 100%; padding: 40px; text-align: center; color: #f44336;"><p>Failed to load delivery requests</p></div>';
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

    if (!requests || requests.length === 0) {
        container.innerHTML = `
            <div style="width: 100%; padding: 40px; text-align: center; color: #666; background: #f8f9fa; border-radius: 12px;">
                <p style="margin: 0; font-size: 1.1rem;">No delivery requests available at the moment</p>
            </div>
        `;
        return;
    }

    container.innerHTML = requests.map(request => `
        <div class="delivery-card" style="min-width: 320px; max-width: 380px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px; flex-shrink: 0;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div>
                    <h4 style="margin: 0 0 4px 0; color: #2c3e50; font-size: 1.1rem;">Order #${request.order_id}</h4>
                    <span style="display: inline-block; padding: 4px 12px; background: #e3f2fd; color: #1976d2; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">${request.required_vehicle_type || 'Any Vehicle'}</span>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 1.4rem; font-weight: 700; color: #4caf50;">Rs. ${parseFloat(request.shipping_fee).toFixed(2)}</div>
                    <div style="font-size: 0.85rem; color: #666;">${parseFloat(request.total_weight_kg).toFixed(1)} kg</div>
                </div>
            </div>

            <div style="margin-bottom: 16px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                <div style="margin-bottom: 8px;">
                    <strong style="color: #2c3e50; font-size: 0.9rem;">📍 Pickup (Farmer)</strong>
                    <div style="color: #666; font-size: 0.9rem; margin-top: 4px;">${request.farmer_name}</div>
                    <div style="color: #666; font-size: 0.85rem;">${request.farmer_address || request.farmer_city || request.farmer_district_name || 'N/A'}</div>
                    ${request.farmer_phone ? `<div style="color: #666; font-size: 0.85rem;">📞 ${request.farmer_phone}</div>` : ''}
                </div>
            </div>

            <div style="margin-bottom: 16px; padding: 12px; background: #fff3e0; border-radius: 8px;">
                <div>
                    <strong style="color: #2c3e50; font-size: 0.9rem;">🎯 Delivery (Buyer)</strong>
                    <div style="color: #666; font-size: 0.9rem; margin-top: 4px;">${request.buyer_name}</div>
                    <div style="color: #666; font-size: 0.85rem;">${request.buyer_address || request.buyer_city || request.buyer_district_name || 'N/A'}</div>
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
    const url = getBaseUrl() + (status ? `/transporterdashboard/getMyRequests?status=${status}` : '/transporterdashboard/getMyRequests');
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
        
        return `
            <tr>
                <td>#${delivery.order_id}</td>
                <td>
                    <div style="font-size: 0.9rem;">${delivery.farmer_city || delivery.farmer_district_name || 'N/A'}</div>
                    <div style="font-size: 0.85rem; color: #666;">to ${delivery.buyer_city || delivery.buyer_district_name || 'N/A'}</div>
                </td>
                <td>${delivery.distance_km ? `${parseFloat(delivery.distance_km).toFixed(1)} km` : 'N/A'}</td>
                <td>${parseFloat(delivery.total_weight_kg).toFixed(1)} kg</td>
                <td>Rs. ${parseFloat(delivery.shipping_fee).toFixed(2)}</td>
                <td>
                    <span style="display: inline-block; padding: 4px 12px; background-color: ${statusColor}20; color: ${statusColor}; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">
                        ${delivery.status.replace('_', ' ').toUpperCase()}
                    </span>
                </td>
                <td>${delivery.created_at ? new Date(delivery.created_at).toLocaleDateString() : 'N/A'}</td>
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
    const filterStatus = status === 'all' ? null : status.replace('-', '_');
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
    } else if (sectionName === 'earnings') {
        loadDashboardData(); // Load earnings data
        loadRecentDeliveries(); // Load payment history
    } else if (sectionName === 'feedback' && typeof loadFeedbackReviews === 'function') {
        loadFeedbackReviews();
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
    const rows = deliveries.map(d => [
        new Date(d.updated_at).toLocaleDateString(),
        d.order_id,
        d.farmer_city || d.farmer_district_name || 'N/A',
        d.buyer_city || d.buyer_district_name || 'N/A',
        parseFloat(d.shipping_fee).toFixed(2),
        'Completed'
    ]);
    
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
