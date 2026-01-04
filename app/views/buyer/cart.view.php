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
                                            <?php
                                            // Prefer cart-stored image, else fallback to product's image from join
                                            $imgFile = '';
                                            if (!empty($item->product_image) && strlen($item->product_image) > 2 && strpos($item->product_image, '.') !== false) {
                                                $imgFile = $item->product_image;
                                            } elseif (!empty($item->product_image_db)) {
                                                $imgFile = $item->product_image_db;
                                            }
                                            ?>
                                            <?php if (!empty($imgFile) && file_exists("assets/images/products/" . $imgFile)): ?>
                                                <img src="<?= ROOT ?>/assets/images/products/<?= htmlspecialchars($imgFile) ?>" alt="<?= htmlspecialchars($item->product_name) ?>" style="width: 72px; height: 72px; object-fit: cover; border-radius: 10px;">
                                            <?php else: ?>
                                                <div style="width:72px;height:72px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:rgba(255,255,255,0.06);font-size:32px;">
                                                    <?= htmlspecialchars(!empty($item->product_image) && strlen($item->product_image) <= 8 ? $item->product_image : '🌱') ?>
                                                </div>
                                            <?php endif; ?>
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

