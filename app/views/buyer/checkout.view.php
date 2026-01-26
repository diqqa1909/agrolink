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
                                        <?php 
                                        $availableQty = $item->available_quantity ?? 0;
                                        $maxQuantity = min($availableQty, 100); // Cap at 100 for UI, but respect available quantity
                                        $currentQuantity = min($item->quantity, $maxQuantity);
                                        
                                        if ($maxQuantity <= 0): 
                                        ?>
                                            <span style="color: #d32f2f; font-weight: 500;">Out of Stock</span>
                                        <?php else: ?>
                                            <select class="quantity-select" data-product-id="<?= $item->product_id ?>" 
                                                    data-max-quantity="<?= $availableQty ?>"
                                                    onchange="updateCheckoutQuantity(<?= $item->product_id ?>, this.value, <?= $availableQty ?>)">
                                                <?php for ($i = 1; $i <= $maxQuantity; $i++): ?>
                                                    <option value="<?= $i ?>" <?= $currentQuantity == $i ? 'selected' : '' ?>>
                                                        <?= $i ?> <?= $i == $maxQuantity && $maxQuantity == $availableQty ? '(Max)' : '' ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <span style="font-size: 0.85rem; color: #666; margin-left: 8px;">
                                                (<?= $availableQty ?> kg available)
                                            </span>
                                        <?php endif; ?>
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

                <?php if ($shippingCalculation && $shippingCalculation['success']): ?>
                    <div class="order-summary-shipping-details" style="font-size: 0.85rem; color: #666; padding: 8px 0; border-top: 1px solid #e0e0e0;">
                        <?php 
                        // Handle both single calculation and multiple calculations (multiple farmers)
                        $calc = $shippingCalculation['calculation'];
                        if (isset($calc['calculations']) && is_array($calc['calculations']) && !empty($calc['calculations'])) {
                            // Multiple farmers - show summary
                            $firstCalc = $calc['calculations'][0];
                            ?>
                            <div style="margin-top: 8px;">
                                <span>Distance: <?= $firstCalc['total_distance_km'] ?? 'N/A' ?> km</span>
                            </div>
                            <div style="margin-top: 4px;">
                                <span>Vehicle: <?= htmlspecialchars($firstCalc['selected_vehicle']['name'] ?? 'N/A') ?></span>
                            </div>
                            <?php if ($calc['multiple_farmers'] ?? false): ?>
                                <div style="margin-top: 4px; font-size: 0.8rem; color: #999;">
                                    (<?= $calc['farmer_count'] ?> farmers)
                                </div>
                            <?php endif; ?>
                        <?php } else {
                            // Single calculation structure
                            ?>
                            <div style="margin-top: 8px;">
                                <span>Distance: <?= $calc['total_distance_km'] ?? 'N/A' ?> km</span>
                            </div>
                            <div style="margin-top: 4px;">
                                <span>Vehicle: <?= htmlspecialchars($calc['selected_vehicle']['name'] ?? 'N/A') ?></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php endif; ?>

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

                <!-- Payment Method Section (shown after clicking Confirm and pay) -->
                <div id="paymentMethodSection" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 12px;">Select a payment method</h4>
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" id="payment-cash" name="payment_method" value="cash_on_delivery" checked>
                            <label for="payment-cash" class="payment-label">
                                <span class="payment-icon">💵</span>
                                <span>Cash on Delivery</span>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="payment-bank" name="payment_method" value="bank_transfer">
                            <label for="payment-bank" class="payment-label">
                                <span class="payment-icon">🏦</span>
                                <span>Bank Transfer</span>
                            </label>
                        </div>
                    </div>
                    <button id="finalConfirmBtn" class="btn btn-primary btn-large" style="width: 100%; margin-top: 16px;" onclick="finalConfirmOrder()">
                        Complete Order
                    </button>
                </div>

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
            formData.append('address2', document.getElementById('address2')?.value || '');
            formData.append('zipCode', document.getElementById('zipCode')?.value || '');
            formData.append('state', document.getElementById('state')?.value || '');
            
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
function updateCheckoutQuantity(productId, quantity, maxQuantity) {
    // Validate quantity doesn't exceed available stock
    if (maxQuantity && quantity > maxQuantity) {
        alert('Cannot select more than ' + maxQuantity + ' kg. Only ' + maxQuantity + ' kg available.');
        // Reset to max available
        const select = document.querySelector(`select[data-product-id="${productId}"]`);
        if (select) {
            select.value = maxQuantity;
            quantity = maxQuantity;
        }
        return;
    }
    
    if (quantity <= 0) {
        alert('Quantity must be at least 1');
        return;
    }
    
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
            alert('Failed to update quantity: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating quantity');
    });
}

// Confirm payment - show payment method selection
function confirmPayment() {
    // Hide confirm button and show payment method section
    const confirmBtn = document.getElementById('confirmPayBtn');
    const paymentSection = document.getElementById('paymentMethodSection');
    
    if (confirmBtn && paymentSection) {
        confirmBtn.style.display = 'none';
        paymentSection.style.display = 'block';
        // Scroll to payment section
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Final order confirmation
function finalConfirmOrder() {
    if (!confirm('Are you sure you want to place this order?')) {
        return;
    }
    
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash_on_delivery';
    
    // Show loading
    const btn = document.getElementById('finalConfirmBtn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
    const formData = new FormData();
    formData.append('payment_method', paymentMethod);
    
    fetch(window.APP_ROOT + '/Checkout/placeOrder', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Order placed successfully!', 'success');
            // Redirect to order confirmation page or orders list
            setTimeout(() => {
                window.location.href = window.APP_ROOT + '/buyerDashboard#orders';
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to place order', 'error');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while placing order: ' + error.message, 'error');
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

// Export functions to window
window.updateCheckoutQuantity = updateCheckoutQuantity;
window.confirmPayment = confirmPayment;
window.finalConfirmOrder = finalConfirmOrder;
</script>
