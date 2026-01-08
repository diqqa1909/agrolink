// Farmer Orders Management

// Load Orders Data
function loadOrdersData() {
    const ordersTableBody = document.getElementById('ordersTableBody');
    if (!ordersTableBody) return;
    
    ordersTableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No orders yet</td></tr>';
}

// Order Actions
function viewOrder(id) {
    showNotification(`Viewing order ${id}`, 'info');
}

function markAsReady(id) {
    showNotification(`Order ${id} marked as ready for pickup`, 'success');
    setTimeout(() => loadOrdersData(), 1000);
}

function trackOrder(id) {
    showNotification(`Tracking order ${id}`, 'info');
}

// Export functions
window.viewOrder = viewOrder;
window.markAsReady = markAsReady;
window.trackOrder = trackOrder;

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadOrdersData);
} else {
    loadOrdersData();
}
