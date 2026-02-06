// Farmer Earnings Page JavaScript
(function() {
    'use strict';

    const APP_ROOT = document.body.getAttribute('data-app-root') || '';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Farmer Earnings page loaded');
        // Add any interactive features here
    });

    /**
     * Format currency
     */
    function formatCurrency(amount) {
        return 'Rs. ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    /**
     * Format date
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

})();
