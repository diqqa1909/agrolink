// Transporter Dashboard Specific Functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeTransporterDashboard();
});

function initializeTransporterDashboard() {
    loadDashboardData();
    initializeEventListeners();
}

// Load dashboard data
function loadDashboardData() {
    // This function would typically fetch data from the server
    // For now, it displays placeholder data
    console.log('Dashboard loaded for transporter');
}

// Initialize event listeners for dashboard
function initializeEventListeners() {
    const sectionLinks = document.querySelectorAll('.menu-link');
    
    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // If it's a real link (has href), don't prevent default
            if (this.getAttribute('href') && this.getAttribute('href') !== '#') {
                return;
            }
            e.preventDefault();
        });
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.textContent = message;
        notification.className = `notification ${type} show`;

        setTimeout(() => {
            notification.classList.remove('show');
        }, 4000);
    }
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES'
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