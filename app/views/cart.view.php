<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - AgroLink</title>
    <meta name="description" content="Review items in your cart before checkout on AgroLink.">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Top Navigation Bar -->
    <?php
    $username = $_SESSION['USER']->name ?? 'Buyer';
    $role = $_SESSION['USER']->role ?? 'buyer';
    include '../app/views/components/dashboardNavBar.view.php';
    ?>

    <!-- Dashboard Layout -->
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard" class="menu-link">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#products" class="menu-link" onclick="scrollToProducts(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </div>
                        Products
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#orders" class="menu-link" onclick="scrollToOrders(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </div>
                        Orders
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#tracking" class="menu-link" onclick="scrollToTracking(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        Tracking
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#wishlist" class="menu-link" onclick="scrollToWishlist(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        Wishlist
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/cart" class="menu-link active">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                        </div>
                        Cart
                        <span class="cart-badge"><?= $cartItemCount ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#requests" class="menu-link" onclick="scrollToRequests(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        Requests
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#reviews" class="menu-link" onclick="scrollToReviews(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        Reviews
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#notifications" class="menu-link" onclick="scrollToNotifications(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        Notifications
                        <span class="badge">5</span>
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerDashboard#profile" class="menu-link" onclick="scrollToProfile(event)">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        Profile
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1 class="content-title">Shopping Cart</h1>
                <p class="content-subtitle">Review items before checkout</p>
            </div>

            <div id="cartContainer">
                <?php if (empty($cartItems) || !is_array($cartItems)): ?>
                    <!-- Empty Cart -->
                    <div class="content-card" style="text-align: center; padding: 60px;">
                        <div style="font-size: 4rem; margin-bottom: 20px;">🛒</div>
                        <h3>Your cart is empty</h3>
                        <p style="color: #666; margin: 16px 0;">Add some products to get started!</p>
                        <a href="<?= ROOT ?>/buyerDashboard#products" class="btn btn-primary" style="margin-top: 20px;" onclick="window.location.href='<?= ROOT ?>/buyerDashboard'; setTimeout(() => document.querySelector('[data-section=products]').click(), 100);">Browse Products</a>
                    </div>
                <?php else: ?>
                    <!-- Cart Items Grid -->
                    <div class="cart-layout">
                        <!-- Cart Items -->
                        <div class="cart-items-container">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="content-card cart-item" data-product-id="<?= htmlspecialchars($item->product_id) ?>">
                                    <div class="cart-item-content">
                                        <!-- Product Image -->
                                        <div class="cart-item-image">
                                            <?= htmlspecialchars($item->product_image) ?>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="cart-item-info">
                                            <h3 class="cart-item-name"><?= htmlspecialchars($item->product_name) ?></h3>
                                            <p class="cart-item-farmer">
                                                <?= htmlspecialchars($item->farmer_name) ?>
                                                <?php if (!empty($item->farmer_location)): ?>
                                                    (<?= htmlspecialchars($item->farmer_location) ?>)
                                                <?php endif; ?>
                                            </p>

                                            <div class="cart-item-pricing">
                                                <!-- Price -->
                                                <div>
                                                    <div class="cart-item-unit-price">Rs. <?= number_format($item->product_price, 2) ?>/kg</div>
                                                    <div class="cart-item-total-price">
                                                        Rs. <?= number_format($item->product_price * $item->quantity, 2) ?>
                                                    </div>
                                                </div>

                                                <!-- Quantity Controls -->
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn quantity-decrease"
                                                        onclick="updateCartQuantity('<?= $item->product_id ?>', -1)">
                                                        -
                                                    </button>
                                                    <span class="quantity-display"><?= $item->quantity ?></span>
                                                    <button class="quantity-btn quantity-increase"
                                                        onclick="updateCartQuantity('<?= $item->product_id ?>', 1)">
                                                        +
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remove Button -->
                                        <div class="cart-item-remove">
                                            <button class="btn btn-danger btn-sm"
                                                onclick="removeFromCart('<?= $item->product_id ?>')">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Cart Summary - Sticky -->
                        <div class="cart-summary-sticky">
                            <div class="cart-summary">
                                <h3 class="cart-summary-title">Cart Summary</h3>

                                <div class="cart-summary-items">
                                    <div class="cart-summary-row">
                                        <span>Items:</span>
                                        <span class="cart-summary-value"><?= $cartItemCount ?></span>
                                    </div>
                                    <div class="cart-summary-row">
                                        <span>Subtotal:</span>
                                        <span class="cart-summary-value">Rs. <?= number_format($cartTotal, 2) ?></span>
                                    </div>
                                    <div class="cart-summary-row cart-summary-delivery">
                                        <span>Delivery:</span>
                                        <span>TBD at checkout</span>
                                    </div>
                                </div>

                                <div class="cart-summary-total">
                                    <span class="cart-summary-total-label">Total:</span>
                                    <span class="cart-summary-total-amount">Rs. <?= number_format($cartTotal, 2) ?></span>
                                </div>

                                <div class="cart-summary-actions">
                                    <button class="btn btn-primary btn-large btn-checkout" onclick="proceedToCheckout()">
                                        Proceed to Checkout
                                    </button>
                                    <a href="<?= ROOT ?>/buyerDashboard#products"
                                        class="btn btn-outline btn-large btn-continue"
                                        onclick="scrollToProducts(event)"
                                        style="background: transparent; color: white; border: 2px solid white; padding: 14px; text-align: center; text-decoration: none; font-weight: 600;">
                                        Continue Shopping
                                    </a>
                                    <button class="btn btn-danger btn-large" onclick="clearCart()">
                                        Clear Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-text">Processing...</div>
            <div class="loading-spinner"></div>
        </div>
    </div>

    <script>
        window.APP_ROOT = "<?= ROOT ?>";

        function scrollToProducts(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#products';
        }

        function scrollToOrders(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#orders';
        }

        function scrollToTracking(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#tracking';
        }

        function scrollToWishlist(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#wishlist';
        }

        function scrollToRequests(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#requests';
        }

        function scrollToReviews(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#reviews';
        }

        function scrollToNotifications(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#notifications';
        }

        function scrollToProfile(e) {
            e.preventDefault();
            window.location.href = '<?= ROOT ?>/buyerDashboard#profile';
        }
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/buyerDashboard.js"></script>
</body>

</html>