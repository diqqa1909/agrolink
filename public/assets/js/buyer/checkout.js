document.addEventListener('DOMContentLoaded', function() {
    const deliveryForm = document.getElementById('deliveryForm');

    if (deliveryForm) {
        deliveryForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const phone = document.getElementById('phone').value.trim();
            const city = document.getElementById('city').value.trim();
            const deliveryAddress = document.getElementById('delivery_address').value.trim();
            const district = document.getElementById('state').value.trim();

            if (!phone || !city || !deliveryAddress || !district) {
                alert('Please fill in all required fields');
                return;
            }

            const formData = new FormData();
            formData.append('phone', phone);
            formData.append('city', city);
            formData.append('delivery_address', deliveryAddress);
            formData.append('address2', document.getElementById('address2')?.value || '');
            formData.append('zipCode', document.getElementById('zipCode')?.value || '');
            formData.append('state', district);

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
                    if (data.success) {
                        document.getElementById('delivery-section')?.classList.add('checkout-hidden');
                        document.getElementById('review-order-section')?.classList.remove('checkout-hidden');
                        document.getElementById('confirmPayBtn')?.classList.remove('checkout-hidden');
                        document.querySelector('.payment-message')?.classList.add('checkout-hidden');

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

function updateCheckoutQuantity(productId, quantity, maxQuantity) {
    if (maxQuantity && quantity > maxQuantity) {
        alert('Cannot select more than ' + maxQuantity + ' kg. Only ' + maxQuantity + ' kg available.');
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

function confirmPayment() {
    const confirmBtn = document.getElementById('confirmPayBtn');
    const paymentSection = document.getElementById('paymentMethodSection');

    if (confirmBtn && paymentSection) {
        confirmBtn.classList.add('checkout-hidden');
        paymentSection.classList.remove('checkout-hidden');
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function finalConfirmOrder() {
    if (!confirm('Are you sure you want to place this order?')) {
        return;
    }

    const btn = document.getElementById('finalConfirmBtn');
    const spinner = document.getElementById('checkoutGatewaySpinner');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Processing payment...';
    if (spinner) spinner.classList.remove('checkout-hidden');

    const formData = new FormData();

    fetch(window.APP_ROOT + '/Checkout/placeOrder', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const redirectUrl = data.redirect || null;
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                    return;
                }
                window.location.href = window.APP_ROOT + '/buyerorders';
            } else {
                showNotification(data.message || 'Failed to place order', 'error');
                btn.disabled = false;
                btn.textContent = originalText;
                if (spinner) spinner.classList.add('checkout-hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while placing order: ' + error.message, 'error');
            btn.disabled = false;
            btn.textContent = originalText;
            if (spinner) spinner.classList.add('checkout-hidden');
        });
}

window.updateCheckoutQuantity = updateCheckoutQuantity;
window.confirmPayment = confirmPayment;
window.finalConfirmOrder = finalConfirmOrder;
