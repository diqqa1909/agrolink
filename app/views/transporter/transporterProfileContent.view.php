<main class="main-content">
    <div class="content-section">
        <!-- Profile Header with Photo -->
        <div class="profile-header-modern">
            <div class="profile-banner">
                <div class="banner-pattern"></div>
            </div>
            <div class="profile-header-content">
                <div class="profile-photo-section">
                    <div id="profilePhotoWrapper" class="profile-photo-wrapper" style="position: relative; width: 120px; height: 120px; border-radius: 50%; overflow: hidden; cursor: pointer; display: inline-block;">
                        <img id="profilePhotoDisplay" src="<?= $photoUrl ?>" class="profile-photo" style="display: block; width: 100%; height: 100%; object-fit: cover;">
                        <!-- Default Icon (shown when no photo) -->
                        <div id="defaultProfileIcon" style="position: absolute; top: 0; left: 0; width: 120px; height: 120px; background: #f0f0f0; border-radius: 50%; align-items: center; justify-content: center; display: none;">
                            <svg width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="color: #bbb;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <!-- Photo Overlay Actions (Hover) -->
                        <div id="photoOverlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center; border-radius: 50%;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                            <button type="button" id="editPhotoBtn" title="Edit Photo" style="width: 40px; height: 40px; background: white; color: #222; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; margin: 0 6px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                                </svg>
                            </button>
                            <button type="button" id="deletePhotoBtn" title="Delete Photo" style="width: 40px; height: 40px; background: white; color: #222; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; margin: 0 6px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
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
                        Transporter Information
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
                    </div>

                    <!-- City -->
                    <div class="form-group-modern">
                        <label for="profileCity" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">City *</label>
                        <input type="text" id="profileCity" name="city" class="form-control" placeholder="e.g., Colombo" value="<?= esc($profile->city ?? '') ?>" style="text-align: left;">
                    </div>

                    <!-- Postal Code -->
                    <div class="form-group-modern">
                        <label for="profilePostalCode" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Postal Code *</label>
                        <input type="text" id="profilePostalCode" name="postal_code" class="form-control" placeholder="e.g., 10100" value="<?= esc($profile->postal_code ?? '') ?>" style="text-align: left;">
                        <small style="color: #666; display: block; text-align: left; margin-top: 5px;">5-digit Sri Lankan postal code</small>
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

            <!-- Hidden file input for photo upload -->
            <input type="file" id="photoFileInput" accept="image/*" style="display: none;">
        </div>
    </div>
</main>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Change Password</h3>
            <button type="button" class="modal-close" onclick="TransporterProfile.closeChangePasswordModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="changePasswordForm">
                <div class="form-group-modern">
                    <label for="currentPassword">Current Password *</label>
                    <input type="password" id="currentPassword" name="current_password" class="form-control" required>
                    <span class="error-message" id="error-current"></span>
                </div>

                <div class="form-group-modern">
                    <label for="newPassword">New Password *</label>
                    <input type="password" id="newPassword" name="new_password" class="form-control" required>
                    <small>Minimum 8 characters</small>
                    <span class="error-message" id="error-new"></span>
                </div>

                <div class="form-group-modern">
                    <label for="confirmPassword">Confirm New Password *</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                    <span class="error-message" id="error-confirm"></span>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                    <button type="button" class="btn btn-secondary" onclick="TransporterProfile.closeChangePasswordModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
