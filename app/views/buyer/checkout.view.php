<div class="content-header">
    <h1 class="content-title">Checkout</h1>
    <p class="content-subtitle">Review your order and complete your purchase</p>
</div>

<style>
/* Force checkout layout */
.checkout-layout {
    display: grid !important;
    grid-template-columns: 1fr 360px !important;
    gap: 14px !important;
    align-items: flex-start !important;
}

.order-item-image-small {
    width: 50px !important;
    height: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    flex-shrink: 0 !important;
}

.order-item-image-small img {
    width: 100% !important;
    height: 100% !important;
    max-width: 50px !important;
    max-height: 50px !important;
    object-fit: cover !important;
}

.checkout-sidebar {
    position: sticky !important;
    top: 20px !important;
}

.order-summary-card {
    background: white !important;
    border: 1px solid #d5d9d9 !important;
}

.order-summary-total-amount {
    color: #B12704 !important;
}
</style>

<div class="checkout-container">
    <div class="checkout-layout">
        <!-- Left Column: Delivery & Review Order -->
        <div class="checkout-main">
            <!-- Delivery Information Form (shown if buyer hasn't added details) -->
            <?php if (!$hasDeliveryDetails): ?>
                <div class="checkout-section" id="delivery-section">
                    <h2 class="checkout-section-title">Add a shipping address</h2>
                    <p class="checkout-section-subtitle">To continue, we need your contact information.</p>
                    
                    <form id="deliveryForm" class="delivery-form">
                        <div class="form-group">
                            <label for="country" class="form-label">Country or region</label>
                            <select id="country" name="country" class="form-control" required>
                                <option value="Sri Lanka" selected>Sri Lanka</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="delivery_address" class="form-label">Street Address <span class="required">*</span></label>
                            <input type="text" id="delivery_address" name="delivery_address" class="form-control" 
                                   placeholder="Enter your street address" required>
                        </div>

                        <div class="form-group">
                            <label for="address2" class="form-label">Street Address 2 (Optional)</label>
                            <input type="text" id="address2" name="address2" class="form-control" 
                                   placeholder="Apartment, suite, etc. (optional)">
                        </div>

                        <div class="form-group">
                            <label for="city" class="form-label">City <span class="required">*</span></label>
                            <input type="text" id="city" name="city" class="form-control" 
                                   placeholder="Enter your city" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="state" class="form-label">State or Province or Region</label>
                                <input type="text" id="state" name="state" class="form-control" 
                                       placeholder="State/Province">
                            </div>
                            <div class="form-group">
                                <label for="zipCode" class="form-label">Zip Code</label>
                                <input type="text" id="zipCode" name="zipCode" class="form-control" 
                                       placeholder="Postal code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone number <span class="required">*</span></label>
                            <div class="phone-input-group">
                                <select id="countryCode" class="form-control phone-code" disabled>
                                    <option value="+94" selected>🇱🇰 +94</option>
                                </select>
                                <input type="tel" id="phone" name="phone" class="form-control phone-input" 
                                       placeholder="Enter your phone number" required>
                            </div>
                            <div class="form-checkbox">
                                <input type="checkbox" id="isLandline" name="isLandline">
                                <label for="isLandline">Phone number is a landline</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-large btn-continue"
                                style="background-color: #28a745 !important; color: #ffffff !important; border: none !important; font-weight: 600 !important; font-size: 16px !important; padding: 12px 24px !important; border-radius: 4px !important; cursor: pointer !important; display: flex !important; justify-content: center !important; align-items: center !important; width: 100% !important;">
                            Continue
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Ship To Section (if delivery details exist) -->
                <div class="checkout-section" id="ship-to-section">
                    <h2 class="checkout-section-title">Ship to</h2>
                    <div class="ship-to-info">
                        <div class="ship-to-name"><?= htmlspecialchars($_SESSION['USER']->name ?? 'Buyer') ?></div>
                        <div class="ship-to-address">
                            <?= htmlspecialchars($buyerProfile->delivery_address ?? '') ?><br>
                            <?= htmlspecialchars($buyerProfile->city ?? '') ?>
                            <?php if (!empty($buyerProfile->city)): ?>
                                , Sri Lanka
                            <?php endif; ?>
                        </div>
                        <div class="ship-to-phone">
                            <?= htmlspecialchars($buyerProfile->phone ?? '') ?>
                        </div>
                        <a href="<?= ROOT ?>/buyerprofile" class="change-link">Change</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Review Order Section -->
            <div class="checkout-section" id="review-order-section" style="<?= !$hasDeliveryDetails ? 'display: none;' : '' ?>">
                <h2 class="checkout-section-title">Review order</h2>
                
                <?php 
                // Group items by farmer
                $itemsByFarmer = [];
                foreach ($cartItems as $item) {
                    $farmerName = $item->farmer_name ?? 'Unknown Farmer';
                    if (!isset($itemsByFarmer[$farmerName])) {
                        $itemsByFarmer[$farmerName] = [];
                    }
                    $itemsByFarmer[$farmerName][] = $item;
                }
                ?>

                <?php foreach ($itemsByFarmer as $farmerName => $farmerItems): ?>
                    <div class="order-seller-block">
                        <div class="seller-info">
                            <span class="seller-name"><?= htmlspecialchars($farmerName) ?></span>
                            <span class="seller-feedback">99.6% positive feedback</span>
                            <a href="#" class="seller-note-link">Add note for seller</a>
                        </div>

                        <?php foreach ($farmerItems as $item): ?>
                            <div class="order-item">
                                <div class="order-item-image-small" style="width: 50px; height: 50px; min-width: 50px; max-width: 50px; flex-shrink: 0;">
                                    <?php
                                    $imgFile = '';
                                    if (!empty($item->product_image) && strlen($item->product_image) > 2 && strpos($item->product_image, '.') !== false) {
                                        $imgFile = $item->product_image;
                                    } elseif (!empty($item->product_image_db)) {
                                        $imgFile = $item->product_image_db;
                                    }
                                    ?>
                                    <?php if (!empty($imgFile) && file_exists("assets/images/products/" . $imgFile)): ?>
                                        <img src="<?= ROOT ?>/assets/images/products/<?= htmlspecialchars($imgFile) ?>" 
                                             alt="<?= htmlspecialchars($item->product_name) ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                    <?php else: ?>
                                        <img src="<?= ROOT ?>/assets/images/default-product.svg" 
                                             alt="<?= htmlspecialchars($item->product_name) ?>" style="width: 100%; height: 100%; object-fit: cover; display: block; opacity: 0.6;">
                                    <?php endif; ?>
                                </div>
                                <div class="order-item-details">
                                    <h4 class="order-item-name"><?= htmlspecialchars($item->product_name) ?></h4>
                                    <div class="order-item-quantity">
                                        <label>Quantity:</label>
                                        <select class="quantity-select" data-product-id="<?= $item->product_id ?>" 
                                                onchange="updateCheckoutQuantity(<?= $item->product_id ?>, this.value)">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?= $i ?>" <?= $item->quantity == $i ? 'selected' : '' ?>>
                                                    <?= $i ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="order-item-delivery">
                                        <div class="delivery-info">
                                            <span class="delivery-label">Estimated delivery:</span>
                                            <span class="delivery-date"><?= date('M d', strtotime('+3 days')) ?> - <?= date('M d', strtotime('+7 days')) ?></span>
                                        </div>
                                        <div class="shipping-method">Standard Shipping</div>
                                        <div class="returns-policy">30 days returns accepted</div>
                                    </div>
                                    <div class="order-item-price">
                                        Rs. <?= number_format($item->product_price * $item->quantity, 2) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right Column: Order Summary (Sticky) -->
        <div class="checkout-sidebar">
            <div class="order-summary-card">
                <h3 class="order-summary-title">Order Summary</h3>
                
                <div class="order-summary-row">
                    <span>Item (<?= $cartItemCount ?>):</span>
                    <span class="order-summary-value">Rs. <?= number_format($cartTotal, 2) ?></span>
                </div>
                
                <div class="order-summary-row">
                    <span>Shipping:</span>
                    <span class="order-summary-value">Rs. <?= number_format($deliveryFee, 2) ?></span>
                </div>

                <div class="order-summary-total">
                    <span class="order-summary-total-label">Order total:</span>
                    <span class="order-summary-total-amount">Rs. <?= number_format($orderTotal, 2) ?></span>
                </div>

                <div class="order-summary-note">
                    <p>With this purchase you agree to the AgroLink <a href="#">Terms and Conditions</a>.</p>
                </div>

                <button id="confirmPayBtn" class="btn btn-primary btn-large btn-confirm-pay" 
                        style="<?= !$hasDeliveryDetails ? 'display: none;' : '' ?>"
                        onclick="confirmPayment()">
                    Confirm and pay
                </button>

                <p class="payment-message" style="<?= !$hasDeliveryDetails ? 'display: block;' : 'display: none;' ?>">
                    Please complete delivery information to proceed
                </p>

                <div class="order-summary-guarantee">
                    <span class="guarantee-icon"></span>
                    <span>Purchase protected by <a href="#">AgroLink Money Back Guarantee</a></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle delivery form submission
document.addEventListener('DOMContentLoaded', function() {
    const deliveryForm = document.getElementById('deliveryForm');
    if (deliveryForm) {
        deliveryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const phone = document.getElementById('phone').value.trim();
            const city = document.getElementById('city').value.trim();
            const deliveryAddress = document.getElementById('delivery_address').value.trim();
            
            // Validate
            if (!phone || !city || !deliveryAddress) {
                alert('Please fill in all required fields');
                return;
            }
            
            const formData = new FormData();
            formData.append('phone', phone);
            formData.append('city', city);
            formData.append('delivery_address', deliveryAddress);
            
            // Show loading
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';
            
            fetch(window.APP_ROOT + '/Checkout/saveDeliveryDetails', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Save response:', data);
                if (data.success) {
                    // Hide delivery form, show review section
                    document.getElementById('delivery-section').style.display = 'none';
                    document.getElementById('review-order-section').style.display = 'block';
                    document.getElementById('confirmPayBtn').style.display = 'block';
                    const paymentMsg = document.querySelector('.payment-message');
                    if (paymentMsg) paymentMsg.style.display = 'none';
                    
                    // Reload page to show ship-to section
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert(data.message || 'Failed to save delivery details');
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving delivery details: ' + error.message);
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    }
});

// Update quantity in checkout
function updateCheckoutQuantity(productId, quantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch(window.APP_ROOT + '/Cart/update', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to update totals
            window.location.reload();
        } else {
            alert('Failed to update quantity');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Confirm payment
function confirmPayment() {
    if (!confirm('Are you sure you want to confirm and pay for this order?')) {
        return;
    }
    
    // TODO: Implement order placement and payment method selection
    alert('Order placement functionality will be implemented next.');
}

// Export functions to window
window.updateCheckoutQuantity = updateCheckoutQuantity;
window.confirmPayment = confirmPayment;
</script>
