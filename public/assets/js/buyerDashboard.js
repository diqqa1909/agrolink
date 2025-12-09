// Buyer Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeBuyerDashboard();
    updateCartBadge();
    // Initialize profile defaults for modern profile UI
    loadProfileData();
    loadWishlist();
    
    // Listen for hash changes (when navigating from external pages)
    window.addEventListener('hashchange', function() {
        const hash = window.location.hash.substring(1);
        if (hash && document.getElementById(hash + '-section')) {
            showSection(hash);
        }
    });
    
    // Also check hash after a short delay (for external navigation)
    setTimeout(function() {
        const hash = window.location.hash.substring(1);
        if (hash && document.getElementById(hash + '-section')) {
            showSection(hash);
        }
    }, 100);
});

// Initialize Dashboard
function initializeBuyerDashboard() {
    // Check for hash fragment in URL
    const hash = window.location.hash.substring(1); // Remove the # symbol
    
    if (hash && document.getElementById(hash + '-section')) {
        // Show the section specified in the hash
        showSection(hash);
    } else {
        // Show dashboard section by default
        if (document.getElementById('dashboard-section')) {
            showSection('dashboard');
        }
    }
    
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
    
    if (sectionName === 'wishlist') {
        loadWishlist();
    }
}

// Show Notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification').forEach(n => n.remove());
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('notification-hide');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Profile: populate defaults and avatar (modern profile UI)
function loadProfileData() {
    const profilePhotoEl = document.getElementById('profilePhoto');
    const profileNameEl = document.getElementById('profileName');
    const profileEmailEl = document.getElementById('profileEmail');
    const profilePhoneEl = document.getElementById('profilePhone');
    const profileLocationEl = document.getElementById('profileLocation');
    const profileAddressEl = document.getElementById('profileAddress');

    const uname = (window.USER_NAME || 'Buyer').trim() || 'Buyer';
    const uemail = (window.USER_EMAIL || '').trim();

    if (profilePhotoEl) {
        const encoded = encodeURIComponent(uname || 'Buyer');
        profilePhotoEl.src = `https://ui-avatars.com/api/?name=${encoded}&background=4CAF50&color=fff&size=150`;
        profilePhotoEl.alt = 'Buyer Profile';
    }

    if (profileNameEl && !profileNameEl.value) profileNameEl.value = uname;
    if (profileEmailEl && !profileEmailEl.value) profileEmailEl.value = uemail || 'buyer@example.com';
    if (profilePhoneEl && !profilePhoneEl.value) profilePhoneEl.value = '+94 77 123 4567';
    if (profileLocationEl && !profileLocationEl.value) profileLocationEl.value = 'Colombo';
    if (profileAddressEl && !profileAddressEl.value) profileAddressEl.value = '123, Main Street, Colombo 07, Sri Lanka';
}

// Profile: simple client-side validation and save feedback
function updateProfile() {
    const name = document.getElementById('profileName')?.value?.trim();
    const email = document.getElementById('profileEmail')?.value?.trim();
    const phone = document.getElementById('profilePhone')?.value?.trim();
    const city = document.getElementById('profileLocation')?.value?.trim();
    const address = document.getElementById('profileAddress')?.value?.trim();

    if (!name || !email || !phone || !city || !address) {
        showNotification('Please fill all required fields', 'error');
        return;
    }
    showNotification('Profile updated successfully!', 'success');
}

// Profile: upload photo button handler
function uploadPhoto() {
    let input = document.getElementById('photoUploadInput');
    if (!input) {
        input = document.createElement('input');
        input.type = 'file';
        input.id = 'photoUploadInput';
        input.accept = 'image/*';
        input.style.display = 'none';
        document.body.appendChild(input);
    }

    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                showNotification('Please select a valid image file', 'error');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                showNotification('Image size should be less than 5MB', 'error');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(ev) {
                const profilePhoto = document.getElementById('profilePhoto');
                if (profilePhoto) {
                    profilePhoto.src = ev.target.result;
                    showNotification('Photo uploaded successfully!', 'success');
                }
            };
            reader.onerror = function() {
                showNotification('Failed to read image file', 'error');
            };
            reader.readAsDataURL(file);
        }
    };

    input.click();
}

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

// Add to cart function - AJAX call to backend
function addToCart(productId, productName, price, maxQuantity) {
    // Show loading
    const btn = event?.target;
    const originalText = btn?.textContent;
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Adding...';
    }
    
    // Get product details from the card (prefer by id for reliability)
    const productCard = document.querySelector(`.product-card[data-id="${productId}"]`) ||
                        document.querySelector(`.product-card[data-name="${productName.toLowerCase()}"]`);
    // Determine image filename to send to server
    let imageFile = '';
    if (productCard) {
        // 1) Prefer explicit data-image from markup
        imageFile = productCard.getAttribute('data-image') || '';
        // 2) Otherwise, try parsing the img src basename if present
        if (!imageFile) {
            const imgEl = productCard.querySelector('.product-image img');
            const src = imgEl?.getAttribute('src') || '';
            if (src && !/default-product\.svg$/i.test(src)) {
                try {
                    imageFile = src.split('/').pop();
                } catch (e) {
                    imageFile = '';
                }
            }
        }
    }
    // 3) Fallback to emoji if no image available
    const fallbackEmoji = productCard?.querySelector('.product-placeholder')?.textContent || '🌱';
    
    // Prepare data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', price);
    formData.append('quantity', 1);
    formData.append('product_image', imageFile || fallbackEmoji);
    
    // Send AJAX request
    fetch(window.APP_ROOT + '/Cart/add', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => {
        // Log response for debugging
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification(data.message, 'success');
            updateCartBadge(data.cartItemCount);
        } else {
            showNotification(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showNotification('An error occurred: ' + error.message, 'error');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
}

// Update cart badge count
function updateCartBadge(count) {
    // If count is provided, use it
    if (count !== undefined) {
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        });
        return;
    }
    
    // Otherwise, fetch from server
    fetch(window.APP_ROOT + '/Cart/getData', {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const badges = document.querySelectorAll('.cart-badge');
            badges.forEach(badge => {
                badge.textContent = data.cartItemCount;
                badge.style.display = data.cartItemCount > 0 ? 'inline-block' : 'none';
            });
        }
    })
    .catch(error => {
        console.error('Error fetching cart data:', error);
    });
}

// Show loading overlay
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
    }
}

// Hide loading overlay
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// Update quantity in cart - NO REFRESH
function updateQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }

    showLoading();
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', newQuantity);
    
    fetch(window.APP_ROOT + '/Cart/update', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        return response.json();
    })
    .then(data => {
        console.log('Update response:', data);
        hideLoading();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Update the UI without refreshing
            const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
            if (cartItem) {
                // Update quantity display
                const quantityDisplay = cartItem.querySelector('.quantity-display');
                if (quantityDisplay) {
                    quantityDisplay.textContent = newQuantity;
                }
                
                // Update total price for this item
                const priceElement = cartItem.querySelector('.cart-item-total-price');
                const unitPriceText = cartItem.querySelector('.cart-item-unit-price')?.textContent || '0';
                const unitPrice = parseFloat(unitPriceText.replace(/[^\d.]/g, '')) || 0;
                if (priceElement) {
                    priceElement.textContent = 'Rs. ' + (unitPrice * newQuantity).toFixed(2);
                }
                
                // Update cart badge
                updateCartBadge(data.cartItemCount);
                
                // Recalculate cart total
                recalculateCartTotal();
            }
        } else {
            showNotification(data.message || 'Failed to update cart', 'error');
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        hideLoading();
        showNotification('An error occurred: ' + error.message, 'error');
    });
}

// Remove from cart - NO REFRESH
function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    showLoading();
    
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch(window.APP_ROOT + '/Cart/remove', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }
        return response.json();
    })
    .then(data => {
        console.log('Remove response:', data);
        hideLoading();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Remove the item from DOM with animation
            const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
            if (cartItem) {
                cartItem.style.transition = 'all 0.3s ease';
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(-100px)';
                
                setTimeout(() => {
                    cartItem.remove();
                    
                    // Update cart badge
                    updateCartBadge(data.cartItemCount);
                    
                    // Recalculate cart total
                    recalculateCartTotal();
                    
                    // Check if cart is empty
                    const remainingItems = document.querySelectorAll('.cart-item');
                    if (remainingItems.length === 0) {
                        location.reload(); // Reload to show empty cart message
                    }
                }, 300);
            }
        } else {
            showNotification(data.message || 'Failed to remove item', 'error');
        }
    })
    .catch(error => {
        console.error('Remove error:', error);
        hideLoading();
        showNotification('An error occurred: ' + error.message, 'error');
    });
}

// Clear cart - WITH REFRESH
function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }

    showLoading();
    
    fetch(window.APP_ROOT + '/Cart/clear', {
        method: 'POST',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showNotification('An error occurred while clearing cart', 'error');
        console.error('Error:', error);
    });
}

// Recalculate cart total (helper function)
function recalculateCartTotal() {
    let total = 0;
    let itemCount = 0;
    
    document.querySelectorAll('.cart-item').forEach(item => {
        const priceText = item.querySelector('.cart-item-total-price')?.textContent || '0';
        const price = parseFloat(priceText.replace(/[^\d.]/g, '')) || 0;
        const quantity = parseInt(item.querySelector('.quantity-display')?.textContent) || 0;
        
        total += price;
        itemCount += quantity;
    });
    
    // Update summary
    const summaryValue = document.querySelector('.cart-summary-value');
    if (summaryValue) {
        summaryValue.textContent = itemCount;
    }
    
    const subtotal = document.querySelectorAll('.cart-summary-row .cart-summary-value')[1];
    if (subtotal) {
        subtotal.textContent = 'Rs. ' + total.toFixed(2);
    }
    
    const totalAmount = document.querySelector('.cart-summary-total-amount');
    if (totalAmount) {
        totalAmount.textContent = 'Rs. ' + total.toFixed(2);
    }
}

// Proceed to checkout
function proceedToCheckout() {
    showNotification('Proceeding to checkout...', 'info');
    // TODO: Implement checkout page
    // window.location.href = window.APP_ROOT + '/checkout';
}

// ==================== WISHLIST FUNCTIONS ====================

function addToWishlist(productId, evt) {
    const btn = evt?.currentTarget || evt?.target || null;
    const originalText = btn ? btn.textContent : null;
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Adding...';
    }

    const formData = new FormData();
    formData.append('product_id', productId);

    fetch(window.APP_ROOT + '/Wishlist/add', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Added to wishlist', 'success');
            loadWishlist();
        } else {
            showNotification(data.error || 'Failed to add to wishlist', 'error');
        }
    })
    .catch(error => {
        console.error('Wishlist add error:', error);
        showNotification('An error occurred while adding to wishlist', 'error');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
}

function removeFromWishlist(productId) {
    if (!confirm('Remove this product from your wishlist?')) {
        return;
    }

    fetch(window.APP_ROOT + '/Wishlist/remove/' + productId, {
        method: 'POST',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Removed from wishlist', 'success');
            loadWishlist();
        } else {
            showNotification(data.error || 'Failed to remove product', 'error');
        }
    })
    .catch(error => {
        console.error('Wishlist remove error:', error);
        showNotification('An error occurred while removing product', 'error');
    });
}

function loadWishlist() {
    const container = document.getElementById('wishlist-list');
    if (!container) return;

    fetch(window.APP_ROOT + '/Wishlist/index', {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderWishlist(data.items || []);
        } else {
            container.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                    <div style="font-size: 3rem; margin-bottom: 20px;">⚠️</div>
                    <h3>${escapeHtml(data.error || 'Failed to load wishlist')}</h3>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Load wishlist error:', error);
        container.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                <div style="font-size: 3rem; margin-bottom: 20px;">⚠️</div>
                <h3>Unable to load wishlist</h3>
                <p>${escapeHtml(error.message)}</p>
            </div>
        `;
    });
}

function renderWishlist(items) {
    const container = document.getElementById('wishlist-list');
    if (!container) return;

    if (!items.length) {
        container.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                <div style="font-size: 3rem; margin-bottom: 20px;">❤️</div>
                <h3>Your wishlist is empty</h3>
                <p>Browse products and click “Wishlist” to save them here.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = items.map(item => {
        const image = item.image 
            ? `${window.APP_ROOT}/assets/images/products/${escapeHtml(item.image)}`
            : `${window.APP_ROOT}/assets/images/default-product.svg`;

        const price = (item.price !== null && item.price !== undefined)
            ? `Rs. ${Number(item.price).toFixed(2)}/kg`
            : 'Price unavailable';

        const stock = (item.available_quantity !== null && item.available_quantity !== undefined)
            ? `${escapeHtml(item.available_quantity)}kg available`
            : '';

        return `
            <div class="product-card" data-wishlist-product="${item.product_id}">
                <div class="product-image">
                    <img src="${image}" alt="${escapeHtml(item.name || 'Product')}" ${item.image ? '' : 'style="opacity:0.6;"'}>
                </div>
                <div class="product-info">
                    <h3 class="product-name">${escapeHtml(item.name || 'Product unavailable')}</h3>
                    <div class="product-price">${price}</div>
                    <div class="product-stock">${stock}</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 8px;">
                        <button class="btn btn-primary btn-sm"
                            onclick="addToCart(${item.product_id}, '${escapeHtml(item.name || 'Product')}', ${item.price || 0}, ${item.available_quantity || 0})">
                            🛒 Add to Cart
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="removeFromWishlist(${item.product_id})">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function escapeHtml(text = '') {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Export functions to window
window.showSection = showSection;
window.showNotification = showNotification;
window.filterProducts = filterProducts;
window.addToCart = addToCart;
window.updateCartBadge = updateCartBadge;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.updateQuantity = updateQuantity;
window.removeFromCart = removeFromCart;
window.clearCart = clearCart;
window.recalculateCartTotal = recalculateCartTotal;
window.proceedToCheckout = proceedToCheckout;
window.loadProfileData = loadProfileData;
window.updateProfile = updateProfile;
window.uploadPhoto = uploadPhoto;
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.loadWishlist = loadWishlist;