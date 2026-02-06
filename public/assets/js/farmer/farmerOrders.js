// Farmer Orders Page JavaScript
(function() {
    'use strict';

    const APP_ROOT = document.body.getAttribute('data-app-root') || '';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initOrderDetailsButtons();
        initModal();
    });

    /**
     * Initialize view details buttons
     */
    function initOrderDetailsButtons() {
        const viewButtons = document.querySelectorAll('.btn-view-details');
        
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                if (orderId) {
                    loadOrderDetails(orderId);
                }
            });
        });
    }

    /**
     * Load order details via AJAX
     */
    function loadOrderDetails(orderId) {
        const modal = document.getElementById('orderDetailsModal');
        const contentDiv = document.getElementById('orderDetailsContent');
        
        // Show modal with loading state
        modal.classList.add('active');
        contentDiv.innerHTML = '<div class="loading">Loading order details...</div>';
        
        // Fetch order details
        fetch(`${APP_ROOT}/farmerorders/getOrderDetails?order_id=${orderId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load order details');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayOrderDetails(data.order, data.items);
            } else {
                throw new Error(data.error || 'Failed to load order details');
            }
        })
        .catch(error => {
            console.error('Error loading order details:', error);
            contentDiv.innerHTML = `
                <div class="error-message">
                    <p>Failed to load order details. Please try again.</p>
                    <button class="btn-retry" onclick="location.reload()">Retry</button>
                </div>
            `;
        });
    }

    /**
     * Display order details in modal
     */
    function displayOrderDetails(order, items) {
        const contentDiv = document.getElementById('orderDetailsContent');
        
        let itemsHtml = '';
        let total = 0;
        
        items.forEach(item => {
            const itemTotal = parseFloat(item.product_price) * parseInt(item.quantity);
            total += itemTotal;
            
            const imageUrl = item.product_image 
                ? `${APP_ROOT}/assets/images/products/${item.product_image}` 
                : `${APP_ROOT}/assets/images/placeholder.jpg`;
            
            itemsHtml += `
                <div class="order-item">
                    <img src="${imageUrl}" alt="${escapeHtml(item.product_name)}" class="item-image" 
                         onerror="this.src='${APP_ROOT}/assets/images/placeholder.jpg'">
                    <div class="item-details">
                        <div class="item-name">${escapeHtml(item.product_name)}</div>
                        <div class="item-meta">
                            <span>Price: LKR ${parseFloat(item.product_price).toFixed(2)}</span>
                            <span>Quantity: ${parseInt(item.quantity)}</span>
                        </div>
                    </div>
                    <div class="item-total">
                        LKR ${itemTotal.toFixed(2)}
                    </div>
                </div>
            `;
        });
        
        const html = `
            <div class="order-details-section">
                <div class="section-title">Order Information</div>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Order ID:</span>
                        <span class="detail-value">#${order.id}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${formatDate(order.created_at)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-${order.status}">
                                ${order.status.replace('_', ' ').toUpperCase()}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Buyer:</span>
                        <span class="detail-value">${escapeHtml(order.buyer_name)}</span>
                    </div>
                </div>
            </div>
            
            <div class="order-details-section">
                <div class="section-title">Delivery Information</div>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">${escapeHtml(order.delivery_address)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">City:</span>
                        <span class="detail-value">${escapeHtml(order.delivery_city)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">District:</span>
                        <span class="detail-value">${escapeHtml(order.district_name || 'N/A')}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">${escapeHtml(order.delivery_phone)}</span>
                    </div>
                </div>
            </div>
            
            <div class="order-details-section">
                <div class="section-title">Your Items (${items.length})</div>
                <div class="order-items-list">
                    ${itemsHtml}
                </div>
                <div class="order-total-section">
                    <div class="total-row">
                        <span class="total-label">Your Total Earnings:</span>
                        <span class="total-value">LKR ${total.toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `;
        
        contentDiv.innerHTML = html;
    }

    /**
     * Initialize modal functionality
     */
    function initModal() {
        const modal = document.getElementById('orderDetailsModal');
        const closeBtn = modal.querySelector('.modal-close');
        
        // Close on X button
        closeBtn.addEventListener('click', function() {
            modal.classList.remove('active');
        });
        
        // Close on outside click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                modal.classList.remove('active');
            }
        });
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Format date
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return date.toLocaleDateString('en-US', options);
    }

})();
