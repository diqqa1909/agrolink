// Farmer Dashboard - Overview Section

// Load Dashboard Data
function loadDashboardData() {
    // Stats will be populated from backend
    const totalProductsEl = document.getElementById('totalProducts');
    const pendingOrdersEl = document.getElementById('pendingOrders');
    const monthlyEarningsEl = document.getElementById('monthlyEarnings');
    const totalEarningsEl = document.getElementById('totalEarnings');
    
    if (totalProductsEl) totalProductsEl.textContent = '0';
    if (pendingOrdersEl) pendingOrdersEl.textContent = '0';
    if (monthlyEarningsEl) monthlyEarningsEl.textContent = 'Rs. 0';
    if (totalEarningsEl) totalEarningsEl.textContent = 'Rs. 0';
    
    // Recent Orders - empty state
    const recentOrdersEl = document.getElementById('recentOrders');
    if (recentOrdersEl) {
        recentOrdersEl.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">No recent orders</div>';
    }
    
    // Top Products - empty state
    const topProductsEl = document.getElementById('topProducts');
    if (topProductsEl) {
        topProductsEl.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">No products data</div>';
    }
}

// Initialize dashboard when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadDashboardData);
} else {
    loadDashboardData();
}
