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
                        <a href="<?= ROOT ?>/buyerDashboard" class="btn btn-primary" style="margin-top: 20px;">Browse Products</a>
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
                                                        onclick="updateQuantity('<?= $item->product_id ?>', <?= $item->quantity - 1 ?>)">
                                                        -
                                                    </button>
                                                    <span class="quantity-display"><?= $item->quantity ?></span>
                                                    <button class="quantity-btn quantity-increase"
                                                        onclick="updateQuantity('<?= $item->product_id ?>', <?= $item->quantity + 1 ?>)">
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
            window.location.href = '<?= ROOT ?>/buyerDashboard';
            // After navigation, scroll to products section
            setTimeout(() => {
                if (window.showSection) {
                    window.showSection('products');
                }
            }, 100);
        }
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/buyerDashboard.js"></script>
</body>

</html>