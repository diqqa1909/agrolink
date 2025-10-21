// Buyer Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeBuyerDashboard();
});

// Initialize Dashboard
function initializeBuyerDashboard() {
    // Show dashboard section by default
    showSection('dashboard');
    
    // Add click handlers to menu links
    document.querySelectorAll('.menu-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Check if it's the cart link (external)
            if (this.getAttribute('href') !== '#') {
                return; // Allow default behavior for cart link
            }
            
            e.preventDefault();
            const section = this.dataset.section;
            if (section) {
                showSection(section);
            }
        });
    });
}

// Show Section Function
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected section
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
    
    // Scroll to top smoothly
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show Notification (uses main.js if available)
function showNotification(message, type = 'info') {
    // Check if main.js notification exists
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
        return;
    }
    
    // Fallback notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : type === 'warning' ? '#ff9800' : '#2196F3'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        font-weight: 600;
        animation: slideInRight 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animation for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
    
    .profile-photo-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        color: white;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }
`;
document.head.appendChild(style);

// Filter products based on search and filters
function filterProducts() {
    const searchInput = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const categoryFilter = document.getElementById('categoryFilter')?.value.toLowerCase() || '';
    const locationFilter = document.getElementById('locationFilter')?.value.toLowerCase() || '';
    const priceFilter = document.getElementById('priceFilter')?.value || '';
    
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const farmer = card.getAttribute('data-farmer') || '';
        const category = card.getAttribute('data-category') || '';
        const location = card.getAttribute('data-location') || '';
        const price = parseFloat(card.getAttribute('data-price')) || 0;
        
        // Search filter
        const matchesSearch = searchInput === '' || 
                             name.includes(searchInput) || 
                             farmer.includes(searchInput);
        
        // Category filter
        const matchesCategory = categoryFilter === '' || category === categoryFilter;
        
        // Location filter
        const matchesLocation = locationFilter === '' || location.includes(locationFilter);
        
        // Price filter
        let matchesPrice = true;
        if (priceFilter) {
            if (priceFilter === '0-100') {
                matchesPrice = price < 100;
            } else if (priceFilter === '100-200') {
                matchesPrice = price >= 100 && price <= 200;
            } else if (priceFilter === '200-500') {
                matchesPrice = price >= 200 && price <= 500;
            } else if (priceFilter === '500+') {
                matchesPrice = price > 500;
            }
        }
        
        // Show/hide based on all filters
        if (matchesSearch && matchesCategory && matchesLocation && matchesPrice) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Check if no products match
    const visibleCards = document.querySelectorAll('.product-card[style="display: block;"]');
    const productsGrid = document.getElementById('productsGrid');
    
    if (visibleCards.length === 0 && productsGrid) {
        const existingMessage = productsGrid.querySelector('.no-results-message');
        if (!existingMessage) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results-message';
            noResults.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 60px; color: #999;';
            noResults.innerHTML = `
                <div style="font-size: 3rem; margin-bottom: 20px;">🔍</div>
                <h3>No products found</h3>
                <p>Try adjusting your search or filters</p>
            `;
            productsGrid.appendChild(noResults);
        }
    } else {
        const message = productsGrid?.querySelector('.no-results-message');
        if (message) message.remove();
    }
}

// Add to cart function
function addToCart(productId, productName, price, maxQuantity) {
    // Get existing cart from localStorage
    let cart = JSON.parse(localStorage.getItem('buyerCart')) || [];
    
    // Check if product already exists in cart
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        if (existingItem.quantity < maxQuantity) {
            existingItem.quantity++;
            showNotification(`${productName} quantity increased!`, 'success');
        } else {
            showNotification(`Maximum available quantity (${maxQuantity}kg) already in cart`, 'warning');
            return;
        }
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: price,
            quantity: 1,
            maxQuantity: maxQuantity
        });
        showNotification(`${productName} added to cart!`, 'success');
    }
    
    // Save to localStorage
    localStorage.setItem('buyerCart', JSON.stringify(cart));
    
    // Update cart badge
    updateCartBadge();
}

// Update cart badge count
function updateCartBadge() {
    const cart = JSON.parse(localStorage.getItem('buyerCart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const badge = document.querySelector('.cart-badge');
    if (badge) {
        badge.textContent = totalItems;
        badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }
}

// Initialize cart badge on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeBuyerDashboard();
    updateCartBadge();
});

// Export functions to window
window.showSection = showSection;
window.showNotification = showNotification;
window.filterProducts = filterProducts;
window.addToCart = addToCart;
window.updateCartBadge = updateCartBadge;