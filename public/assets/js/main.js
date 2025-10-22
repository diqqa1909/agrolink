// AgroLink - Common Utilities & Shared Functions

// Global state
let cart = JSON.parse(localStorage.getItem('agrolink_cart')) || [];
let currentUser = JSON.parse(localStorage.getItem('agrolink_user')) || null;

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCommon();
});

// Initialize common functionality
function initializeCommon() {
    initNavigation();
    initModals();
    updateCartUI();
    updateUserState();
}

// Navigation functionality
function initNavigation() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
    
    // Active navigation links
    const currentPage = getPageName();
    const navItems = document.querySelectorAll('.nav-links a, .sidebar-menu a');
    
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.includes(currentPage)) {
            item.classList.add('active');
        }
    });
}

// Get current page name
function getPageName() {
    const path = window.location.pathname;
    const page = path.split('/').pop().replace('.html', '') || 'index';
    return page;
}

// Modal functionality
function initModals() {
    // Modal triggers
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            openModal(modalId);
        });
    });
    
    // Modal close buttons
    const closeButtons = document.querySelectorAll('.modal-close, [data-modal-close]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });
    
    // Close modal on backdrop click
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
    
    // Prevent modal content click from closing modal
    document.querySelectorAll('.modal-content').forEach(content => {
        content.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active', 'show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active', 'show');
        document.body.style.overflow = 'auto';
    }
}

// Cart functions
function addToCart(productId) {
    const product = getProductById(productId);
    if (!product) return;
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: product.name,
            price: product.price,
            image: product.image,
            farmer: product.farmer,
            quantity: 1
        });
    }
    
    localStorage.setItem('agrolink_cart', JSON.stringify(cart));
    updateCartUI();
    showNotification('Product added to cart', 'success');
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('agrolink_cart', JSON.stringify(cart));
    updateCartUI();
    showNotification('Product removed from cart', 'success');
}

function updateCartQuantity(productId, change) {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) loadingOverlay.style.display = 'flex';

    fetch(`${window.APP_ROOT}/cart/update/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `quantity=${Math.max(1, parseInt(document.querySelector(`[data-product-id="${productId}"] .quantity-display`).textContent) + change)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the page to update cart
            window.location.reload();
        } else {
            showNotification(data.message || 'Failed to update cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update cart', 'error');
    })
    .finally(() => {
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    });
}

function updateCartUI() {
    const cartCount = document.querySelector('.cart-count');
    const cartTotal = document.querySelector('.cart-total');
    
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'inline' : 'none';
    }
    
    if (cartTotal) {
        cartTotal.textContent = `Rs. ${totalPrice.toFixed(2)}`;
    }
}

// Form validation
function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    
    clearError(field);
    
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Please enter a valid email address');
        return false;
    }
    
    if (field.type === 'password' && value && value.length < 6) {
        showFieldError(field, 'Password must be at least 6 characters');
        return false;
    }
    
    if (field.name === 'confirmPassword') {
        const passwordField = field.form.querySelector('[name="password"]');
        if (passwordField && value !== passwordField.value) {
            showFieldError(field, 'Passwords do not match');
            return false;
        }
    }
    
    return true;
}

function showFieldError(field, message) {
    field.classList.add('error');
    
    let errorElement = field.parentNode.querySelector('.form-text.error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-text error';
        field.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

function clearError(field) {
    if (typeof field === 'object' && field.target) {
        field = field.target;
    }
    
    field.classList.remove('error');
    const errorElement = field.parentNode.querySelector('.form-text.error');
    if (errorElement) {
        errorElement.remove();
    }
}

// User state management
function updateUserState() {
    const user = getCurrentUser();
    const userInfo = document.querySelector('.user-info');
    const loginLinks = document.querySelectorAll('.login-link');
    const logoutLinks = document.querySelectorAll('.logout-link');
    
    if (user) {
        if (userInfo) {
            userInfo.innerHTML = `<span>Welcome, ${user.name}</span>`;
        }
        
        loginLinks.forEach(link => link.style.display = 'none');
        logoutLinks.forEach(link => link.style.display = 'inline-block');
    } else {
        if (userInfo) {
            userInfo.innerHTML = '';
        }
        
        loginLinks.forEach(link => link.style.display = 'inline-block');
        logoutLinks.forEach(link => link.style.display = 'none');
    }
}

function getCurrentUser() {
    return JSON.parse(localStorage.getItem('agrolink_user'));
}

function requireAuth(allowedRoles = []) {
    const user = getCurrentUser();
    
    if (!user) {
        showNotification('Please login to access this page', 'error');
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 1500);
        return false;
    }
    
    if (allowedRoles.length > 0 && !allowedRoles.includes(user.role)) {
        showNotification('Access denied', 'error');
        setTimeout(() => {
            redirectToDashboard(user.role);
        }, 1500);
        return false;
    }
    
    return true;
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        localStorage.removeItem('agrolink_user');
        localStorage.removeItem('agrolink_cart');
        showNotification('Logged out successfully', 'success');
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1000);
    }
}

function redirectToDashboard(role) {
    const dashboardUrls = {
        farmer: 'dashboard_farmer.html',
        buyer: 'dashboard_buyer.html',
        transporter: 'dashboard_transporter.html',
        admin: 'dashboard_admin.html'
    };
    
    const url = dashboardUrls[role] || 'index.html';
    window.location.href = url;
}

// Utility functions
function generateId() {
    return 'id_' + Math.random().toString(36).substr(2, 9);
}

function getNameFromEmail(email) {
    return email.split('@')[0].replace(/[._]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.insertBefore(notification, document.body.firstChild);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Table sorting function
function sortTable(table, column) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const columnIndex = Array.from(table.querySelectorAll('th')).findIndex(th => th.getAttribute('data-sort') === column);
    
    if (columnIndex === -1) return;
    
    const currentDirection = table.getAttribute('data-sort-direction') || 'asc';
    const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
    table.setAttribute('data-sort-direction', newDirection);
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return newDirection === 'asc' ? aNum - bNum : bNum - aNum;
        } else {
            return newDirection === 'asc' ? 
                aValue.localeCompare(bValue) : 
                bValue.localeCompare(aValue);
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
    
    table.querySelectorAll('th').forEach(th => th.classList.remove('sorted-asc', 'sorted-desc'));
    table.querySelector(`th[data-sort="${column}"]`).classList.add(`sorted-${newDirection}`);
}

// Mock data functions (replace with actual API calls)
function getProductById(id) {
    const products = getMockProducts();
    return products.find(p => p.id === id);
}

function getMockProducts() {
    return [
        {
            id: '1',
            name: 'Fresh Tomatoes',
            price: 120,
            image: null,
            farmer: 'Ranjith Fernando',
            location: 'Matale',
            category: 'vegetables',
            description: 'Fresh organic tomatoes from Matale region'
        },
        {
            id: '2',
            name: 'Green Beans',
            price: 180,
            image: null,
            farmer: 'Kumari Silva',
            location: 'Kandy',
            category: 'vegetables',
            description: 'Premium quality green beans'
        },
        {
            id: '3',
            name: 'Red Rice',
            price: 95,
            image: null,
            farmer: 'Sunil Perera',
            location: 'Anuradhapura',
            category: 'grains',
            description: 'Traditional red rice variety'
        }
    ];
}

// Export functions for global access
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.openModal = openModal;
window.closeModal = closeModal;
window.logout = logout;
window.showNotification = showNotification;
window.validateField = validateField;
window.clearError = clearError;
window.getCurrentUser = getCurrentUser;
window.requireAuth = requireAuth;
window.sortTable = sortTable;
