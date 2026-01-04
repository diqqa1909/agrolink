<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
</head>

<body>
    <main class="main-content">
        <div class="content-section">
            <!-- Profile Header with Photo -->
            <div class="profile-header-modern">
                <div class="profile-banner">
                    <div class="banner-pattern"></div>
                </div>
                <div class="profile-header-content">
                    <div class="profile-photo-section">
                        <div id="profilePhotoWrapper" class="profile-photo-wrapper">
                            <img id="profilePhotoDisplay" src="<?= $photoUrl ?>" alt="Profile Photo" class="profile-photo">
                            <!-- Photo Overlay Actions -->
                            <div id="photoOverlay">
                                <div>
                                    <button type="button">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                                        </svg>
                                    </button>
                                    <span></span>
                                    <button type="button" onclick="removeProfilePhoto()">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="profile-header-info">
                            <h2 id="profileDisplayName"><?= esc($username ?? '') ?></h2>
                            <p id="profileDisplayEmail"><?= esc($_SESSION['USER']->email ?? '') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="profile-form-modern">
                    <div class="form-section-header">
                        <h2>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Personal Information
                        </h2>
                    </div>

                    <form id="profileForm" class="profile-form-grid-modern">
                        <!-- Name (Editable, from auth) -->
                        <div class="form-group-modern">
                            <label for="profileName" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Full Name *</label>
                            <input type="text" id="profileName" name="name" value="<?= esc($username ?? '') ?>" class="form-control" placeholder="Enter your full name" style="text-align: left;">
                        </div>

                        <!-- Email (Read-only, from auth) -->
                        <div class="form-group-modern">
                            <label for="profileEmail" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Email Address</label>
                            <input type="email" id="profileEmail" value="<?= esc($_SESSION['USER']->email ?? '') ?>" class="form-control" readonly style="background-color: #f5f5f5; cursor: not-allowed; text-align: left;">
                            <small style="color: #999; display: block; text-align: left; margin-top: 5px;">From authentication. Contact admin to change.</small>
                        </div>

                        <!-- Phone -->
                        <div class="form-group-modern">
                            <label for="profilePhone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Phone Number *</label>
                            <input type="tel" id="profilePhone" name="phone" class="form-control" placeholder="e.g., +94 71 123 4567" value="<?= esc($profile->phone ?? '') ?>" style="text-align: left;">
                            <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Sri Lankan format: +94XXXXXXXXX or 0XXXXXXXXX</small>
                            <span class="error-message" id="error-phone"></span>
                        </div>

                        <!-- District -->
                        <div class="form-group-modern">
                            <label for="profileDistrict" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">District *</label>
                            <select id="profileDistrict" name="district" class="form-control" style="text-align: left;">
                                <option value="">-- Select District --</option>
                                <option value="Ampara" <?= ($profile->district ?? '') === 'Ampara' ? 'selected' : '' ?>>Ampara</option>
                                <option value="Anuradhapura" <?= ($profile->district ?? '') === 'Anuradhapura' ? 'selected' : '' ?>>Anuradhapura</option>
                                <option value="Badulla" <?= ($profile->district ?? '') === 'Badulla' ? 'selected' : '' ?>>Badulla</option>
                                <option value="Batticaloa" <?= ($profile->district ?? '') === 'Batticaloa' ? 'selected' : '' ?>>Batticaloa</option>
                                <option value="Colombo" <?= ($profile->district ?? '') === 'Colombo' ? 'selected' : '' ?>>Colombo</option>
                                <option value="Galle" <?= ($profile->district ?? '') === 'Galle' ? 'selected' : '' ?>>Galle</option>
                                <option value="Gampaha" <?= ($profile->district ?? '') === 'Gampaha' ? 'selected' : '' ?>>Gampaha</option>
                                <option value="Jaffna" <?= ($profile->district ?? '') === 'Jaffna' ? 'selected' : '' ?>>Jaffna</option>
                                <option value="Kalutara" <?= ($profile->district ?? '') === 'Kalutara' ? 'selected' : '' ?>>Kalutara</option>
                                <option value="Kandy" <?= ($profile->district ?? '') === 'Kandy' ? 'selected' : '' ?>>Kandy</option>
                                <option value="Kegalle" <?= ($profile->district ?? '') === 'Kegalle' ? 'selected' : '' ?>>Kegalle</option>
                                <option value="Kilinochchi" <?= ($profile->district ?? '') === 'Kilinochchi' ? 'selected' : '' ?>>Kilinochchi</option>
                                <option value="Kurunegala" <?= ($profile->district ?? '') === 'Kurunegala' ? 'selected' : '' ?>>Kurunegala</option>
                                <option value="Mannar" <?= ($profile->district ?? '') === 'Mannar' ? 'selected' : '' ?>>Mannar</option>
                                <option value="Matale" <?= ($profile->district ?? '') === 'Matale' ? 'selected' : '' ?>>Matale</option>
                                <option value="Matara" <?= ($profile->district ?? '') === 'Matara' ? 'selected' : '' ?>>Matara</option>
                                <option value="Mullaitivu" <?= ($profile->district ?? '') === 'Mullaitivu' ? 'selected' : '' ?>>Mullaitivu</option>
                                <option value="Nuwara Eliya" <?= ($profile->district ?? '') === 'Nuwara Eliya' ? 'selected' : '' ?>>Nuwara Eliya</option>
                                <option value="Polonnaruwa" <?= ($profile->district ?? '') === 'Polonnaruwa' ? 'selected' : '' ?>>Polonnaruwa</option>
                                <option value="Puttalam" <?= ($profile->district ?? '') === 'Puttalam' ? 'selected' : '' ?>>Puttalam</option>
                                <option value="Ratnapura" <?= ($profile->district ?? '') === 'Ratnapura' ? 'selected' : '' ?>>Ratnapura</option>
                                <option value="Trincomalee" <?= ($profile->district ?? '') === 'Trincomalee' ? 'selected' : '' ?>>Trincomalee</option>
                                <option value="Vavuniya" <?= ($profile->district ?? '') === 'Vavuniya' ? 'selected' : '' ?>>Vavuniya</option>
                            </select>
                            <span class="error-message" id="error-district"></span>
                        </div>

                        <!-- Crops Selling -->
                        <div class="form-group-modern full-width">
                            <label for="profileCrops" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Crops You're Selling *</label>
                            <input type="text" id="profileCrops" name="crops_selling" class="form-control" placeholder="e.g., Tomatoes, Carrots, Potatoes" value="<?= esc($profile->crops_selling ?? '') ?>" style="text-align: left;">
                            <small style="color: #666; display: block; text-align: left; margin-top: 5px;">List the main crops/products you sell</small>
                            <span class="error-message" id="error-crops_selling"></span>
                        </div>

                        <!-- Full Address -->
                        <div class="form-group-modern full-width">
                            <label for="profileAddress" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Farm Address *</label>
                            <textarea id="profileAddress" name="full_address" class="form-control" placeholder="Enter your complete farm address" rows="2" style="text-align: left;"><?= esc($profile->full_address ?? '') ?></textarea>
                            <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Street, area, postal code</small>
                            <span class="error-message" id="error-full_address"></span>
                        </div>
                    </form>

                    <div class="profile-actions-modern">
                        <button type="button" class="btn btn-save-profile" onclick="saveProfileData()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>
                            Save Changes
                        </button>
                        <button type="button" class="btn btn-reset-profile" onclick="resetProfileForm()" style="background-color: #fff; color: #344054; border: 1px solid #d0d5dd;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 4 23 10 17 10" />
                                <path d="M20.49 15a9 9 0 1 1-2-8.12" />
                            </svg>
                            Reset
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="openChangePasswordModal()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Change Password
                        </button>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="profile-stats-modern">
                    <div class="stats-header" style="text-align: left;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 8px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20m10-10H2" />
                            </svg>
                            Profile Statistics
                        </h3>
                        <p style="margin: 6px 0 0 0; text-align: left;">Your farming journey at a glance</p>
                    </div>
                    <div class="stats-grid-modern">
                        <div class="stat-card-modern stat-primary">
                            <div class="stat-value" id="statTotalProducts">0</div>
                            <div class="stat-label">Active Products</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1" />
                                    <circle cx="20" cy="21" r="1" />
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-success">
                            <div class="stat-value" id="statTotalOrders">0</div>
                            <div class="stat-label">Total Orders</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 11l3 3L22 4" />
                                    <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-info">
                            <div class="stat-value" id="statTotalEarnings">Rs. 0</div>
                            <div class="stat-label">Total Earnings</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 6v6l4 2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password Modal -->
                <div id="changePasswordModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Change Password</h3>
                        </div>
                        <div class="modal-body">
                            <form id="changePasswordForm">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password *</label>
                                    <input type="password" id="currentPassword" name="currentPassword" class="form-control" required placeholder="Enter your current password">
                                    <span class="error-message" id="error-current"></span>
                                </div>

                                <div class="form-group">
                                    <label for="newPassword">New Password *</label>
                                    <input type="password" id="newPassword" name="newPassword" class="form-control" required placeholder="Enter new password (min 8 characters)">
                                    <small style="color: #666; margin-top: 5px; display: block;">At least 8 characters long</small>
                                    <span class="error-message" id="error-new"></span>
                                </div>

                                <div class="form-group">
                                    <label for="confirmPassword">Confirm Password *</label>
                                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required placeholder="Confirm new password">
                                    <span class="error-message" id="error-confirm"></span>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="closeChangePasswordModal()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <!-- Make ROOT available to JS -->
    <script>
        window.APP_ROOT = "<?= ROOT ?>";
        window.USER_NAME = <?= json_encode($_SESSION['USER']->name ?? '') ?>;
        window.USER_EMAIL = <?= json_encode($_SESSION['USER']->email ?? '') ?>;
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/farmerDashboard.js"></script>
</body>

</html>