// Farmer Deliveries Management

// Load Deliveries Data
function loadDeliveriesData() {
    const pendingDeliveriesEl = document.getElementById('pendingDeliveries');
    const inTransitDeliveriesEl = document.getElementById('inTransitDeliveries');
    const completedDeliveriesEl = document.getElementById('completedDeliveries');
    const avgDeliveryTimeEl = document.getElementById('avgDeliveryTime');
    
    if (pendingDeliveriesEl) pendingDeliveriesEl.textContent = '0';
    if (inTransitDeliveriesEl) inTransitDeliveriesEl.textContent = '0';
    if (completedDeliveriesEl) completedDeliveriesEl.textContent = '0';
    if (avgDeliveryTimeEl) avgDeliveryTimeEl.textContent = '-';
}

// Detailed Pending Deliveries List
let _pendingDeliveriesData = [];

function renderPendingDeliveriesList(items) {
    const list = document.getElementById('deliveriesList');
    if (!list) return;
    
    if (items.length === 0) {
        list.innerHTML = `
            <div class="delivery-empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h13v10H3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 10h4l3 3v4h-7z"/>
                    <circle stroke-linecap="round" stroke-linejoin="round" cx="7.5" cy="17.5" r="1.5"/>
                    <circle stroke-linecap="round" stroke-linejoin="round" cx="18.5" cy="17.5" r="1.5"/>
                </svg>
                <h3>No deliveries found</h3>
                <p>No matching deliveries at this time</p>
            </div>
        `;
        return;
    }
    
    list.innerHTML = items.map(i => {
        const statusMap = {
            'Out for pickup': 'awaiting',
            'Pending assignment': 'awaiting',
            'Awaiting pickup': 'awaiting',
            'In transit': 'in-transit',
            'Scheduled': 'ready'
        };
        const statusClass = statusMap[i.status] || 'awaiting';
        
        const products = i.products || [
            { name: 'Fresh Tomatoes', qty: '50 kg' },
            { name: 'Red Rice', qty: '100 kg' }
        ];
        
        return `
        <div class="delivery-item">
            <div class="delivery-header">
                <div class="delivery-main-info">
                    <div class="delivery-order-id">Delivery ${i.id}</div>
                    <div class="delivery-buyer">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        ${escapeHtml(i.buyer)}
                    </div>
                </div>
                <span class="delivery-status-badge ${statusClass}">${escapeHtml(i.status)}</span>
            </div>
            
            <div class="delivery-body">
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Order ID</span>
                    <span class="delivery-info-value highlight">#${escapeHtml(i.order)}</span>
                </div>
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Route</span>
                    <span class="delivery-info-value">${escapeHtml(i.route)}</span>
                </div>
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Driver</span>
                    <span class="delivery-info-value">${escapeHtml(i.driver)}</span>
                </div>
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Contact</span>
                    <span class="delivery-info-value">${escapeHtml(i.contact)}</span>
                </div>
            </div>
            
            <div class="delivery-product-list">
                ${products.map(p => `
                    <div class="delivery-product-item">
                        <span class="delivery-product-name">${escapeHtml(p.name)}</span>
                        <span class="delivery-product-qty">${escapeHtml(p.qty)}</span>
                    </div>
                `).join('')}
            </div>
            
            <div class="delivery-footer">
                <div class="delivery-actions">
                    <button class="btn btn-outline" onclick="viewDeliveryDetails('${i.id}')">View Details</button>
                    <button class="btn btn-primary" onclick="trackDelivery('${i.id}')">Track</button>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

function loadPendingDeliveriesData() {
    _pendingDeliveriesData = [];
    applyDeliveriesFilters();
}

function initializeDeliveriesFilters() {
    const search = document.getElementById('deliveriesSearch');
    const sort = document.getElementById('deliveriesSort');
    if (search) search.addEventListener('input', applyDeliveriesFilters);
    if (sort) sort.addEventListener('change', applyDeliveriesFilters);
}

function applyDeliveriesFilters() {
    const search = (document.getElementById('deliveriesSearch')?.value || '').toLowerCase();
    const sort = document.getElementById('deliveriesSort')?.value || 'status';
    let items = _pendingDeliveriesData.slice();

    if (search) {
        items = items.filter(i => 
            i.buyer.toLowerCase().includes(search) ||
            i.order.toLowerCase().includes(search) ||
            i.id.toLowerCase().includes(search)
        );
    }

    if (sort === 'status') {
        items.sort((a,b) => a.status.localeCompare(b.status));
    }

    renderPendingDeliveriesList(items);
}

// Delivery action functions
function viewDeliveryDetails(deliveryId) {
    showNotification(`Viewing details for delivery ${deliveryId}`, 'info');
}

function trackDelivery(deliveryId) {
    showNotification(`Tracking delivery ${deliveryId}`, 'info');
}

// Export functions
window.viewDeliveryDetails = viewDeliveryDetails;
window.trackDelivery = trackDelivery;

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        loadDeliveriesData();
        loadPendingDeliveriesData();
        initializeDeliveriesFilters();
    });
} else {
    loadDeliveriesData();
    loadPendingDeliveriesData();
    initializeDeliveriesFilters();
}
