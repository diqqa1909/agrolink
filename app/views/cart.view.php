<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - AgroLink</title>
    <meta name="description" content="Review items in your cart before checkout on AgroLink.">
    <link rel="stylesheet" href="assets/css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="logo-section">
            <img src="assets/img/Logo.png" alt="AgroLink">
        </div>
        
        <div class="user-section">
            <div class="user-info">
                <div class="user-avatar" id="userAvatar"><?php echo strtoupper(substr($username, 0, 2)); ?></div>
                <div class="user-details">
                    <div class="user-name" id="userName"><?php echo htmlspecialchars($username); ?></div>
                    <div class="user-role">Buyer</div>
                </div>
            </div>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="<?php echo ROOT; ?>/buyerDashboard" class="menu-link">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </div>
                Dashboard
            </a></li>
            <li><a href="<?php echo ROOT; ?>/cart" class="menu-link active">
                <div class="menu-icon">
                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                Cart <span class="cart-count" id="cartCount"><?php echo $cartItemCount; ?></span>
            </a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-header">
            <h1 class="content-title">Shopping Cart</h1>
            <p class="content-subtitle">Review items before checkout</p>
        </div>

        <div id="cartContainer">
            <?php if(empty($cartItems) || !is_array($cartItems)): ?>
                <!-- Empty Cart -->
                <div class="content-card" style="text-align: center; padding: 50px;">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🛒</div>
                    <h3>Your cart is empty</h3>
                    <p class="text-muted">Add some products to get started!</p>
                    <a href="<?php echo ROOT; ?>/buyerDashboard" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <!-- Cart Items -->
                <div class="grid grid-2">
                    <div>
                        <?php foreach($cartItems as $item): ?>
                            <div class="content-card cart-item" data-product-id="<?php echo htmlspecialchars($item->product_id); ?>">
                                <div style="display: flex; align-items: center; padding: 20px;">
                                    <div style="font-size: 3rem; margin-right: 20px;">
                                        <?php echo htmlspecialchars($item->product_image); ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <h4><?php echo htmlspecialchars($item->product_name); ?></h4>
                                        <p class="text-muted"><?php echo htmlspecialchars($item->farmer_name); ?> (<?php echo htmlspecialchars($item->farmer_location); ?>)</p>
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                                            <div style="font-size: 1.25rem; font-weight: bold; color: #4CAF50;">
                                                Rs. <?php echo number_format($item->product_price * $item->quantity, 2); ?>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <button class="btn btn-sm" onclick="updateQuantity('<?php echo $item->product_id; ?>', <?php echo $item->quantity - 1; ?>)">-</button>
                                                <span class="quantity-display"><?php echo $item->quantity; ?></span>
                                                <button class="btn btn-sm" onclick="updateQuantity('<?php echo $item->product_id; ?>', <?php echo $item->quantity + 1; ?>)">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-left: 20px;">
                                        <button class="btn btn-danger" onclick="removeFromCart('<?php echo $item->product_id; ?>')">Remove</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Cart Summary -->
                    <div>
                        <div class="content-card" style="background: #4CAF50; color: white;">
                            <div style="padding: 30px; text-align: center;">
                                <h3 style="margin-bottom: 20px;">Cart Summary</h3>
                                <div style="font-size: 2rem; font-weight: bold; margin-bottom: 20px;">
                                    Total: Rs. <?php echo number_format($cartTotal, 2); ?>
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <p>Items: <?php echo $cartItemCount; ?></p>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <a href="<?php echo ROOT; ?>/buyerDashboard" class="btn btn-secondary btn-large">Continue Shopping</a>
                                    <button class="btn btn-primary btn-large" onclick="proceedToCheckout()">Proceed to Checkout</button>
                                    <button class="btn btn-outline btn-large" onclick="clearCart()">Clear Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <div style="margin-bottom: 10px;">Loading...</div>
            <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #4CAF50; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto;"></div>
        </div>
    </div>

    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .cart-count {
            background: #4CAF50;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
        .cart-item {
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .quantity-display {
            padding: 0 15px;
            font-weight: bold;
            min-width: 30px;
            text-align: center;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }
        
        .btn-large {
            padding: 15px 30px;
            font-size: 1.1rem;
        }
        
        .text-muted {
            color: #666;
        }
    </style>

    <script>
        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            Object.assign(notification.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '1rem 2rem',
                borderRadius: '8px',
                color: 'white',
                backgroundColor: type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : type === 'error' ? '#ef4444' : '#3b82f6',
                zIndex: '1000',
                fontSize: '14px',
                fontWeight: 'bold',
                boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)'
            });
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Update quantity
        function updateQuantity(productId, newQuantity) {
            if (newQuantity <= 0) {
                removeFromCart(productId);
                return;
            }

            showLoading();
            
            fetch('<?php echo ROOT; ?>/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${newQuantity}`
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartDisplay(data);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                showNotification('An error occurred while updating cart', 'error');
                console.error('Error:', error);
            });
        }

        // Remove from cart
        function removeFromCart(productId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }

            showLoading();
            
            fetch('<?php echo ROOT; ?>/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartDisplay(data);
                    // Remove the item from DOM
                    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
                    if (cartItem) {
                        cartItem.remove();
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                showNotification('An error occurred while removing item', 'error');
                console.error('Error:', error);
            });
        }

        // Clear cart
        function clearCart() {
            if (!confirm('Are you sure you want to clear your entire cart?')) {
                return;
            }

            showLoading();
            
            fetch('<?php echo ROOT; ?>/cart/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showNotification(data.message, 'success');
                    location.reload(); // Reload page to show empty cart
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

        // Update cart display
        function updateCartDisplay(data) {
            // Update cart count in sidebar
            const cartCount = document.getElementById('cartCount');
            if (cartCount) {
                cartCount.textContent = data.cartItemCount || 0;
            }

            // Update total if provided
            if (data.cartTotal !== undefined) {
                const totalElement = document.querySelector('.content-card h3');
                if (totalElement) {
                    totalElement.innerHTML = `Total: Rs. ${parseFloat(data.cartTotal).toFixed(2)}`;
                }
            }
        }

        // Proceed to checkout
        function proceedToCheckout() {
            showNotification('Proceeding to checkout...', 'info');
            // In a real application, this would redirect to checkout page
            // window.location.href = '<?php echo ROOT; ?>/checkout';
        }

        // Logout function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '<?php echo ROOT; ?>/login/logout';
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Update cart count on page load
            const cartCount = document.getElementById('cartCount');
            if (cartCount) {
                cartCount.textContent = '<?php echo $cartItemCount; ?>';
            }
        });
    </script>
</body>
</html>
