<?php
// $districts and $towns are passed from TransporterProfileController::index()
// as DB objects from LocationModel (with ->id and ->district_name / ->town_name).
// Do NOT redefine $districts here.

$profileObj = is_object($profile ?? null) ? $profile : null;
$accountStatusRaw = strtolower(trim((string)($profileObj->status ?? 'active')));
$accountStatusLabel = ucfirst($accountStatusRaw !== '' ? $accountStatusRaw : 'active');
$memberSinceValue = !empty($profileObj->created_at) ? date('M d, Y', strtotime((string)$profileObj->created_at)) : '-';
$lastUpdatedValue = !empty($profileObj->updated_at) ? date('M d, Y h:i A', strtotime((string)$profileObj->updated_at)) : '-';
?>

<div class="content-section profile-modern farmer-profile-modern transporter-profile-modern">
    <div class="content-header">
        <h1 class="content-title">Profile</h1>
        <p class="content-subtitle">Manage your transporter details, account settings, and payouts.</p>
    </div>

    <div class="profile-hero-card">
        <div class="profile-hero-avatar-wrap">
            <div id="profilePhotoWrapper" class="profile-hero-avatar">
                <img id="profilePhotoDisplay" src="<?= esc($photoUrl ?? '') ?>" alt="Profile photo" class="<?= empty($photoUrl) ? 'is-hidden' : '' ?>">
                <div id="defaultProfileIcon" class="profile-default-icon <?= !empty($photoUrl) ? 'is-hidden' : '' ?>" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
            </div>

            <!-- Profile Form removed — using the Personal Information card below -->
            <div class="profile-form-modern" style="display:none;">

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
                        <input type="tel" id="profilePhone" name="phone" class="form-control" placeholder="07XXXXXXXX" value="<?= esc($profile->phone ?? '') ?>" maxlength="10" inputmode="numeric" style="text-align: left;">
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Exactly 10 digits</small>
                        <span class="error-message" id="error-phone"></span>
                    </div>

                    <!-- District -->
                    <div class="form-group-modern">
                        <label for="profileDistrict" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">District *</label>
                        <select id="profileDistrict" name="district_id" class="form-control" style="text-align: left;">
                            <option value="">-- Select District --</option>
                            <?php foreach (($districts ?? []) as $district): ?>
                                <option value="<?= (int)$district->id ?>" <?= ((int)($profile->district_id ?? 0) === (int)$district->id || (($profile->district ?? '') === $district->district_name)) ? 'selected' : '' ?>>
                                    <?= esc($district->district_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-message" id="error-district"></span>
                    </div>

                    <!-- Apartment Code -->
                    <div class="form-group-modern">
                        <label for="profileApartmentCode" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Apartment/Building Code</label>
                        <input type="text" id="profileApartmentCode" name="apartment_code" class="form-control" placeholder="e.g., A-101" value="<?= esc($profile->apartment_code ?? '') ?>" style="text-align: left;">
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Building or apartment number (if applicable)</small>
                    </div>

                    <!-- Street Name -->
                    <div class="form-group-modern">
                        <label for="profileStreetName" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Street Name *</label>
                        <input type="text" id="profileStreetName" name="street_name" class="form-control" placeholder="e.g., Galle Road" value="<?= esc($profile->street_name ?? '') ?>" style="text-align: left;">
                        <span class="error-message" id="error-street_name"></span>
                    </div>

                    <!-- City -->
                    <div class="form-group-modern">
                        <label for="profileCity" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Town / City *</label>
                        <select id="profileCity" name="town_id" class="form-control" style="text-align: left;" <?= empty($towns) ? 'disabled' : '' ?>>
                            <option value="">-- Select Town / City --</option>
                            <?php foreach (($towns ?? []) as $town): ?>
                                <option value="<?= (int)$town->id ?>" <?= ((int)($profile->town_id ?? 0) === (int)$town->id || (($profile->city ?? '') === $town->town_name)) ? 'selected' : '' ?>>
                                    <?= esc($town->town_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-message" id="error-city"></span>
                    </div>

                    <!-- Postal Code -->
                    <div class="form-group-modern">
                        <label for="profilePostalCode" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Postal Code *</label>
                        <input type="text" id="profilePostalCode" name="postal_code" class="form-control" placeholder="e.g., 10100" value="<?= esc($profile->postal_code ?? '') ?>" style="text-align: left;">
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">5-digit Sri Lankan postal code</small>
                        <span class="error-message" id="error-postal_code"></span>
                    </div>

                    <!-- Full Address -->
                    <div class="form-group-modern full-width">
                        <label for="profileFullAddress" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Additional Address Details</label>
                        <textarea id="profileFullAddress" name="full_address" class="form-control" placeholder="e.g., Near City Mall, Opposite Police Station" rows="2" style="text-align: left;"><?= esc($profile->full_address ?? '') ?></textarea>
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Landmarks or additional directions (optional)</small>
                    </div>

                    <!-- Company Name -->
                    <div class="form-group-modern">
                        <label for="profileCompanyName" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Company Name</label>
                        <input type="text" id="profileCompanyName" name="company_name" class="form-control" placeholder="Enter company name (if applicable)" value="<?= esc($profile->company_name ?? '') ?>" style="text-align: left;">
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Optional for individual transporters</small>
                    </div>

                    <!-- License Number -->
                    <div class="form-group-modern">
                        <label for="profileLicenseNumber" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">License Number *</label>
                        <input type="text" id="profileLicenseNumber" name="license_number" class="form-control" placeholder="e.g., B1234567" value="<?= esc($profile->license_number ?? '') ?>" style="text-align: left;">
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Your driver's license number</small>
                        <span class="error-message" id="error-license_number"></span>
                    </div>

                    <!-- Vehicle Type -->
                    <div class="form-group-modern">
                        <label for="profileVehicleType" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Primary Vehicle Type *</label>
                        <select id="profileVehicleType" name="vehicle_type" class="form-control" style="text-align: left;">
                            <option value="">-- Select Vehicle Type --</option>
                            <?php if (!empty($vehicleTypes)): ?>
                                <?php foreach ($vehicleTypes as $vType): 
                                    $slug = strtolower(str_replace(' ', '', $vType->vehicle_name));
                                    $selected = (($profile->vehicle_type ?? '') === $slug) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($slug) ?>" <?= $selected ?>><?= htmlspecialchars($vType->vehicle_name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Availability Status -->
                    <div class="form-group-modern">
                        <label for="profileAvailability" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Current Availability *</label>
                        <select id="profileAvailability" name="availability" class="form-control" style="text-align: left;">
                            <option value="">-- Select Availability --</option>
                            <option value="available" <?= ($profile->availability ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="not available" <?= ($profile->availability ?? '') === 'not available' ? 'selected' : '' ?>>Not Available</option>
                            <option value="busy" <?= ($profile->availability ?? '') === 'busy' ? 'selected' : '' ?>>Busy</option>
                        </select>
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Update your availability status</small>
                    </div>
                </form>

                <div class="profile-actions-modern">
                    <button type="button" class="btn btn-save-profile" onclick="TransporterProfile.saveProfileData()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Save Changes
                    </button>
                    <button type="button" class="btn btn-reset-profile" onclick="TransporterProfile.resetProfileForm()" style="background-color: #fff; color: #344054; border: 1px solid #d0d5dd;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 4 23 10 17 10" />
                            <path d="M20.49 15a9 9 0 1 1-2-8.12" />
                        </svg>
                        Reset
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="TransporterProfile.openChangePasswordModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        Change Password
                    </button>
                </div>
            </div>
        </div>

        <div class="profile-hero-meta">
            <h2 id="profileDisplayName"><?= esc($username ?? 'Transporter') ?></h2>
            <p id="profileDisplayEmail"><?= esc(authUserEmail()) ?></p>
            <span class="profile-role-badge">Transporter</span>
        </div>
    </div>

    <div class="content-card profile-card">
        <div class="profile-section-head">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Personal Information
            </h3>
        </div>

        <div class="profile-card-body">
            <form id="profileForm" class="profile-form-grid transporter-profile-form-grid">

                <div class="form-group">
                    <label for="profileName">Full Name *</label>
                    <input type="text" id="profileName" name="name" class="form-control" value="<?= esc($username ?? '') ?>" maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="profilePhone">Phone Number *</label>
                    <input type="tel" id="profilePhone" name="phone" class="form-control" value="<?= esc($profileObj->phone ?? '') ?>" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                    <small class="form-hint">Exactly 10 digits</small>
                    <span class="error-message" id="error-phone"></span>
                </div>

                <!-- District — cascading dropdown using DB objects -->
                <div class="form-group">
                    <label for="profileDistrict">District *</label>
                    <select id="profileDistrict" name="district_id" class="form-control" required>
                        <option value="">-- Select District --</option>
                        <?php foreach (($districts ?? []) as $dist): ?>
                            <option value="<?= (int)$dist->id ?>" <?= ((int)($profileObj->district_id ?? 0) === (int)$dist->id) ? 'selected' : '' ?>>
                                <?= esc($dist->district_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-message" id="error-district"></span>
                </div>

                <!-- City — populated via AJAX when district changes -->
                <div class="form-group">
                    <label for="profileCity">Town / City *</label>
                    <select id="profileCity" name="town_id" class="form-control" <?= empty($towns) ? 'disabled' : '' ?> required>
                        <option value="">-- Select Town / City --</option>
                        <?php foreach (($towns ?? []) as $town): ?>
                            <option value="<?= (int)$town->id ?>" <?= ((int)($profileObj->town_id ?? 0) === (int)$town->id) ? 'selected' : '' ?>>
                                <?= esc($town->town_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-message" id="error-city"></span>
                </div>

                <div class="form-group">
                    <label for="profileStreetName">Street Name *</label>
                    <input type="text" id="profileStreetName" name="street_name" class="form-control" value="<?= esc($profileObj->street_name ?? '') ?>" maxlength="150" required>
                    <span class="error-message" id="error-street_name"></span>
                </div>

                <div class="form-group">
                    <label for="profilePostalCode">Postal Code</label>
                    <input type="text" id="profilePostalCode" name="postal_code" class="form-control" value="<?= esc($profileObj->postal_code ?? '') ?>" maxlength="5" inputmode="numeric" pattern="[0-9]{5}">
                    <small class="form-hint">5-digit Sri Lankan postal code</small>
                    <span class="error-message" id="error-postal_code"></span>
                </div>

                <div class="form-group">
                    <label for="profileApartmentCode">Apartment / Building Code</label>
                    <input type="text" id="profileApartmentCode" name="apartment_code" class="form-control" value="<?= esc($profileObj->apartment_code ?? '') ?>" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="profileCompanyName">Company Name</label>
                    <input type="text" id="profileCompanyName" name="company_name" class="form-control" value="<?= esc($profileObj->company_name ?? '') ?>" maxlength="255">
                    <small class="form-hint">Optional for individual transporters</small>
                </div>

                <!-- License Number with verification status -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="profileLicenseNumber">Driver's License Number *</label>
                    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                        <input type="text" id="profileLicenseNumber" name="license_number" class="form-control" value="<?= esc($profileObj->license_number ?? '') ?>" maxlength="100" required style="flex:1; min-width:200px;">
                        <?php
                            $licVerified = !empty($profileObj->license_verified) && (int)$profileObj->license_verified === 1;
                            $licVerifiedAt = !empty($profileObj->license_verified_at) ? date('d M Y', strtotime($profileObj->license_verified_at)) : null;
                        ?>
                        <?php if ($licVerified): ?>
                            <span id="licenseVerifiedBadge" style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;background:#d1fae5;color:#065f46;border-radius:999px;font-size:0.82rem;font-weight:600;white-space:nowrap;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Verified <?= $licVerifiedAt ? 'on ' . $licVerifiedAt : '' ?>
                            </span>
                        <?php else: ?>
                            <span id="licenseVerifiedBadge" style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;background:#fef3c7;color:#92400e;border-radius:999px;font-size:0.82rem;font-weight:600;white-space:nowrap;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Not Verified
                            </span>
                            <button type="button" id="requestLicenseVerifyBtn" class="btn btn-outline btn-sm" onclick="requestLicenseVerification()" style="white-space:nowrap;">
                                Request Verification
                            </button>
                        <?php endif; ?>
                    </div>
                    <small class="form-hint">Your driver's license number. Submit for admin verification.</small>
                    <span class="error-message" id="error-license_number"></span>
                </div>

                <div class="form-group form-group-wide transporter-address-details-field" style="grid-column: 1 / -1;">
                    <label for="profileFullAddress">Additional Address Details</label>
                    <textarea id="profileFullAddress" name="full_address" class="form-control" rows="2" maxlength="500" placeholder="Landmark or extra location details"><?= esc($profileObj->full_address ?? '') ?></textarea>
                </div>

                <input type="hidden" name="district" id="profileDistrictName" value="<?= esc($profileObj->district_name ?? $profileObj->district ?? '') ?>">
                <input type="hidden" name="city" id="profileCityName" value="<?= esc($profileObj->town_name ?? $profileObj->city ?? '') ?>">
                <input type="email" id="profileEmail" value="<?= esc(authUserEmail()) ?>" hidden>
            </form>
        </div>

        <div class="profile-card-footer" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <button type="button" id="saveProfileBtn" class="btn btn-primary" onclick="TransporterProfile.saveProfileData()">Save Changes</button>
            <button type="button" class="btn btn-secondary" id="resetProfileBtn" onclick="TransporterProfile.resetProfileForm()">Reset</button>
            <button type="button" class="btn btn-outline" onclick="openChangePasswordModal()" style="margin-left:auto;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Change Password
            </button>
        </div>
    </div>

    <div class="content-card profile-shortcut-card" data-open-modal="accountSettingsModal">
        <div class="profile-shortcut-head">
            <h3>Account Settings</h3>
        </div>
        <p>Email settings and password management.</p>
    </div>

    <div class="content-card profile-shortcut-card" data-open-modal="payoutDetailsModal">
        <div class="profile-shortcut-head">
            <h3>Payout Details</h3>
        </div>
        <p>Add or update bank account details for delivery earnings.</p>
    </div>

    <div class="content-card profile-account-info-card">
        <div class="profile-section-head">
            <h3>Account Info</h3>
        </div>
        <div class="profile-account-info-grid">
            <div>
                <span class="profile-account-label">Member Since</span>
                <span class="profile-account-value" id="memberSinceValue"><?= esc($memberSinceValue) ?></span>
            </div>
            <div>
                <span class="profile-account-label">Account Status</span>
                <span class="profile-account-value profile-account-status" id="accountStatusValue"><?= esc($accountStatusLabel) ?></span>
            </div>
            <div>
                <span class="profile-account-label">Last Updated</span>
                <span class="profile-account-value" id="lastUpdatedValue"><?= esc($lastUpdatedValue) ?></span>
            </div>
        </div>
    </div>

    <div class="content-card profile-danger-card">
        <div class="profile-section-head danger">
            <h3>Danger Zone</h3>
        </div>
        <p>Deactivate your transporter account. Active deliveries must be completed before deactivation.</p>
        <button type="button" class="btn btn-danger" data-open-modal="deactivateAccountModal">Deactivate Account</button>
    </div>

    <input type="file" id="profilePhotoFileInput" accept="image/jpeg,image/jpg,image/png,image/webp" style="display: none;">
</div>

<div id="accountSettingsModal" class="modal profile-modal">
    <div class="modal-content profile-modal-content">
        <div class="modal-header profile-modal-header">
            <h3>Account Settings</h3>
            <button type="button" class="modal-close" data-close-modal="accountSettingsModal" aria-label="Close">×</button>
        </div>
        <div class="modal-body account-settings-modal-body">
            <div class="account-settings-section">
                <button type="button" class="account-settings-toggle" data-settings-target="emailSettingsPanel" aria-expanded="false">
                    <span class="account-settings-toggle-main">
                        <span class="account-settings-toggle-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 6h16v12H4z"></path>
                                <path d="m4 7 8 6 8-6"></path>
                            </svg>
                        </span>
                        <span class="account-settings-toggle-label">Change Email</span>
                    </span>
                    <span class="account-settings-toggle-arrow" aria-hidden="true">⌄</span>
                </button>
                <div id="emailSettingsPanel" class="account-settings-panel">
                    <div class="form-group">
                        <label for="accountSettingsEmail">Current Email</label>
                        <input id="accountSettingsEmail" class="form-control" type="email" readonly>
                    </div>
                    <form id="changeEmailForm">
                        <div class="form-group">
                            <label for="newEmailAddress">New Email Address *</label>
                            <input id="newEmailAddress" class="form-control" type="email" maxlength="100" autocomplete="email" required>
                        </div>
                        <div class="form-group">
                            <label for="emailChangePassword">Confirm Password *</label>
                            <input id="emailChangePassword" class="form-control" type="password" autocomplete="current-password" required>
                            <small class="form-hint">Enter your account password to confirm email change.</small>
                        </div>
                        <small class="form-hint" id="emailChangePolicyHint">You can change email up to 2 times after account creation.</small>
                        <div id="emailChangeStatus" class="settings-inline-status is-hidden"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-close-modal="accountSettingsModal">Cancel</button>
                            <button type="submit" id="changeEmailBtn" class="btn btn-primary">Update Email</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="account-settings-section">
                <button type="button" class="account-settings-toggle" data-settings-target="passwordSettingsPanel" aria-expanded="false">
                    <span class="account-settings-toggle-main">
                        <span class="account-settings-toggle-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="4" y="11" width="16" height="9" rx="2"></rect>
                                <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
                            </svg>
                        </span>
                        <span class="account-settings-toggle-label">Change Password</span>
                    </span>
                    <span class="account-settings-toggle-arrow" aria-hidden="true">⌄</span>
                </button>
                <div id="passwordSettingsPanel" class="account-settings-panel">
                    <form id="changePasswordForm">
                        <div class="form-group">
                            <label for="currentPassword">Current Password *</label>
                            <input type="password" id="currentPassword" class="form-control" autocomplete="current-password" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password *</label>
                            <input type="password" id="newPassword" class="form-control" minlength="8" autocomplete="new-password" required>
                            <small class="form-hint">Minimum 8 characters with at least one letter and one number.</small>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password *</label>
                            <input type="password" id="confirmPassword" class="form-control" minlength="8" autocomplete="new-password" required>
                        </div>
                        <div id="passwordChangeStatus" class="settings-inline-status is-hidden"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-close-modal="accountSettingsModal">Cancel</button>
                            <button type="submit" id="changePasswordBtn" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="payoutDetailsModal" class="modal profile-modal">
    <div class="modal-content profile-modal-content">
        <div class="modal-header profile-modal-header">
            <h3>Add or Change Bank Details</h3>
            <button type="button" class="modal-close" data-close-modal="payoutDetailsModal" aria-label="Close">×</button>
        </div>
        <div class="modal-body">
            <p class="payout-helper-text">Transporter earnings will be transferred to this bank account.</p>
            <form id="payoutDetailsForm">
                <div class="form-group">
                    <label for="payoutAccountName">Account Holder Name *</label>
                    <input type="text" id="payoutAccountName" class="form-control" maxlength="120" required>
                </div>
                <div class="form-group">
                    <label for="payoutBankName">Bank Name *</label>
                    <input type="text" id="payoutBankName" class="form-control" maxlength="120" required>
                </div>
                <div class="form-group">
                    <label for="payoutAccountNumber">Account Number *</label>
                    <input type="text" id="payoutAccountNumber" class="form-control" maxlength="30" inputmode="numeric" required>
                </div>
                <div class="form-group">
                    <label for="payoutBranchName">Branch Name</label>
                    <input type="text" id="payoutBranchName" class="form-control" maxlength="120">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close-modal="payoutDetailsModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Bank Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="deactivateAccountModal" class="modal profile-modal">
    <div class="modal-content profile-modal-content">
        <div class="modal-header profile-modal-header danger">
            <h3>Deactivate Transporter Account</h3>
            <button type="button" class="modal-close" data-close-modal="deactivateAccountModal" aria-label="Close">×</button>
        </div>
        <div class="modal-body">
            <p class="deactivate-warning-text">
                Your transporter account will become unavailable for new delivery assignments.
                Complete active deliveries before deactivation.
            </p>
            <div class="form-group">
                <label for="deactivateReason">Reason (optional)</label>
                <textarea id="deactivateReason" class="form-control" rows="2" maxlength="500" placeholder="Tell us why you are deactivating"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-close-modal="deactivateAccountModal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeactivateBtn">Confirm Deactivation</button>
            </div>
        </div>
    </div>
</div>