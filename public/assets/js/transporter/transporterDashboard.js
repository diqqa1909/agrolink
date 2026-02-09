// Transporter Dashboard Specific Functionality

document.addEventListener('DOMContentLoaded', function () {
    initializeTransporterDashboard();
});

function initializeTransporterDashboard() {
    loadDashboardData();
    loadVehicleTypes(); // Load vehicle types for the form
    initializeEventListeners();
}

// Load vehicle types from database
function loadVehicleTypes() {
    fetch(`${window.APP_ROOT}/transporterDashboard/getVehicleTypes`, { credentials: 'include' })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.types) {
                const select = document.getElementById('vehicleType');
                if (select) {
                    select.innerHTML = '<option value="">Select Type...</option>' +
                        res.types.map(t =>
                            `<option value="${t.id}" data-min="${t.min_weight_kg}" data-max="${t.max_weight_kg}">${escapeHtml(t.vehicle_name)}</option>`
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
    // This function would typically fetch data from the server
    // For now, it displays placeholder data
    console.log('Dashboard loaded for transporter');
}

// Initialize event listeners for dashboard
function initializeEventListeners() {
    const sectionLinks = document.querySelectorAll('.menu-link');

    sectionLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            //If it's a real link (has href), don't prevent default
            if (this.getAttribute('href') && this.getAttribute('href') !== '#') {
                return;
            }
            e.preventDefault();
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