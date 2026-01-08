// Farmer Earnings Management

// Load Earnings Data
function loadEarningsData() {
    const todayEarningsEl = document.getElementById('todayEarnings');
    const weekEarningsEl = document.getElementById('weekEarnings');
    const monthEarningsDetailEl = document.getElementById('monthEarningsDetail');
    const yearEarningsEl = document.getElementById('yearEarnings');
    
    if (todayEarningsEl) todayEarningsEl.textContent = 'Rs. 0';
    if (weekEarningsEl) weekEarningsEl.textContent = 'Rs. 0';
    if (monthEarningsDetailEl) monthEarningsDetailEl.textContent = 'Rs. 0';
    if (yearEarningsEl) yearEarningsEl.textContent = 'Rs. 0';
}

// Detailed Earnings Table Data
function loadEarningsDetailsData() {
    const tbody = document.getElementById('earningsTableBody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No earnings data yet</td></tr>';
}

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        loadEarningsData();
        loadEarningsDetailsData();
    });
} else {
    loadEarningsData();
    loadEarningsDetailsData();
}
