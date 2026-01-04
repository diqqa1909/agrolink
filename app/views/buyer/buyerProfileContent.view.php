<div class="content-section">
    <!-- Profile Header -->
    <div class="profile-header-modern">
        <div class="profile-banner">
            <div class="banner-pattern"></div>
        </div>
        <div class="profile-header-content">
            <div class="profile-photo-section">
                <div class="profile-photo-wrapper-modern">
                    <img id="profilePhoto" src="" alt="Profile">
                    <button class="photo-edit-btn" onclick="uploadPhoto()" title="Change Photo">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="profile-header-info">
                <h1 class="profile-name"><?= htmlspecialchars($username ?? 'Buyer') ?></h1>
                <p class="profile-email"><?= htmlspecialchars($_SESSION['USER']->email ?? '') ?></p>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="profile-form-modern">
        <div class="form-section-header">
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Personal Information
            </h2>
            <p style="color: #999; font-size: 0.9rem; margin: 5px 0 0 0;">Update your contact and delivery information</p>
        </div>
        <form id="profileForm" class="profile-form-grid-modern">
            <div class="form-group-modern">
                <label class="form-label-modern" for="profileName">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Full Name <span class="required">*</span>
                </label>
                <input type="text" id="profileName" class="form-input-modern" value="<?= htmlspecialchars($_SESSION['USER']->name ?? '') ?>" placeholder="Enter your full name" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                <small style="color: #999; font-size: 0.85rem;">From authentication. Contact admin to change.</small>
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern" for="profileEmail">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    Email Address <span class="required">*</span>
                </label>
                <input type="email" id="profileEmail" class="form-input-modern" value="<?= htmlspecialchars($_SESSION['USER']->email ?? '') ?>" placeholder="your.email@example.com" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                <small style="color: #999; font-size: 0.85rem;">From authentication. Contact admin to change.</small>
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern" for="profilePhone">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    Phone Number <span class="required">*</span>
                </label>
                <input type="tel" id="profilePhone" name="phone" class="form-input-modern" placeholder="+94 77 123 4567" value="<?= htmlspecialchars($profile->phone ?? '') ?>">
                <small style="color: #666; font-size: 0.85rem;">Sri Lankan format: +94XXXXXXXXX or 0XXXXXXXXX</small>
                <span class="error-message" id="error-phone"></span>
            </div>
            <div class="form-group-modern">
                <label class="form-label-modern" for="profileLocation">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    City <span class="required">*</span>
                </label>
                <input type="text" id="profileLocation" name="city" class="form-input-modern" placeholder="City" value="<?= htmlspecialchars($profile->city ?? '') ?>">
                <span class="error-message" id="error-city"></span>
            </div>
            <div class="form-group-modern full-width">
                <label class="form-label-modern" for="profileAddress">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Delivery Address <span class="required">*</span>
                </label>
                <textarea id="profileAddress" name="delivery_address" class="form-input-modern" rows="3" placeholder="Enter your full delivery address"><?= htmlspecialchars($profile->delivery_address ?? '') ?></textarea>
                <span class="error-message" id="error-delivery_address"></span>
            </div>
        </form>

        <div class="profile-actions-modern">
            <button class="btn btn-save-profile" onclick="saveBuyerProfile()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Save Changes
            </button>
            <button class="btn btn-reset-profile" onclick="loadBuyerProfileData()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                </svg>
                Reset
            </button>
        </div>
    </div>

    <!-- Account Statistics -->
    <div class="profile-stats-modern">
        <div class="stats-header">
            <h3>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Account Overview
            </h3>
            <p>Your activity at a glance</p>
        </div>
        <div class="stats-grid-modern">
            <div class="stat-card-modern stat-primary">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label-modern">Member Since</div>
                    <div class="stat-value-modern"><?= date('M Y', strtotime($_SESSION['USER']->created_at ?? 'now')) ?></div>
                </div>
            </div>
            <div class="stat-card-modern stat-success">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label-modern">Total Orders</div>
                    <div class="stat-value-modern">0</div>
                </div>
            </div>
            <div class="stat-card-modern stat-info">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label-modern">Wishlist Items</div>
                    <div class="stat-value-modern">0</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load profile data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBuyerProfileData();
});

// Load buyer profile data
function loadBuyerProfileData() {
    const profilePhotoEl = document.getElementById('profilePhoto');
    const profileNameEl = document.getElementById('profileName');
    const profileEmailEl = document.getElementById('profileEmail');
    const profilePhoneEl = document.getElementById('profilePhone');
    const profileLocationEl = document.getElementById('profileLocation');
    const profileAddressEl = document.getElementById('profileAddress');

    const uname = (window.USER_NAME || '<?= htmlspecialchars($username ?? 'Buyer') ?>').trim() || 'Buyer';
    const uemail = (window.USER_EMAIL || '<?= htmlspecialchars($_SESSION['USER']->email ?? '') ?>').trim();

    // Set profile photo
    if (profilePhotoEl) {
        const encoded = encodeURIComponent(uname || 'Buyer');
        profilePhotoEl.src = `https://ui-avatars.com/api/?name=${encoded}&background=4CAF50&color=fff&size=150`;
        profilePhotoEl.alt = 'Buyer Profile';
    }

    // Load profile data from server
    fetch(window.APP_ROOT + '/BuyerProfile?ajax=1', {
        method: 'GET',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.profile) {
            const profile = data.profile;
            if (profilePhoneEl && profile.phone) profilePhoneEl.value = profile.phone;
            if (profileLocationEl && profile.city) profileLocationEl.value = profile.city;
            if (profileAddressEl && profile.delivery_address) profileAddressEl.value = profile.delivery_address;
        }
    })
    .catch(error => {
        console.error('Error loading profile:', error);
    });
}

// Save buyer profile
function saveBuyerProfile() {
    const phone = document.getElementById('profilePhone')?.value?.trim() || '';
    const city = document.getElementById('profileLocation')?.value?.trim() || '';
    const deliveryAddress = document.getElementById('profileAddress')?.value?.trim() || '';

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // Validation
    if (!phone || !city || !deliveryAddress) {
        showNotification('Please fill in all required fields', 'error');
        if (!phone) document.getElementById('error-phone').textContent = 'Phone number is required';
        if (!city) document.getElementById('error-city').textContent = 'City is required';
        if (!deliveryAddress) document.getElementById('error-delivery_address').textContent = 'Delivery address is required';
        return;
    }

    // Show loading
    const btn = event?.target;
    const originalText = btn?.textContent;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg> Saving...';
    }

    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('city', city);
    formData.append('delivery_address', deliveryAddress);

    fetch(window.APP_ROOT + '/BuyerProfile/saveProfile', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Profile save response:', data);
        if (data.success) {
            showNotification(data.message || 'Profile updated successfully!', 'success');
            // Reload profile data to show updated values
            setTimeout(() => {
                loadBuyerProfileData();
            }, 500);
        } else {
            showNotification(data.error || 'Failed to update profile', 'error');
            console.error('Profile save error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while saving profile: ' + error.message, 'error');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}

// Upload photo (placeholder - can be implemented later)
function uploadPhoto() {
    showNotification('Photo upload feature coming soon', 'info');
}
</script>

