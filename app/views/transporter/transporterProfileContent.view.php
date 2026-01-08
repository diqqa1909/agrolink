<!-- Transporter Profile Content (embedded in transporterMain.view.php) -->
<div class="content-section">
    <!-- Profile Header with Photo -->
    <div class="profile-header-modern">
        <div class="profile-banner">
            <div class="banner-pattern"></div>
        </div>
        <div class="profile-header-content">
            <div class="profile-photo-section">
                <div id="profilePhotoWrapper" class="profile-photo-wrapper" style="position: relative; width: 120px; height: 120px; border-radius: 50%; overflow: hidden; cursor: pointer; display: inline-block;">
                    <img id="profilePhotoDisplay" src="<?= isset($photoUrl) ? esc($photoUrl) : '' ?>" class="profile-photo" style="display: block; width: 100%; height: 100%; object-fit: cover;">
                    <!-- Default Icon (shown when no photo) -->
                    <div id="defaultProfileIcon" style="position: absolute; top: 0; left: 0; width: 120px; height: 120px; background: #f0f0f0; border-radius: 50%; align-items: center; justify-content: center; display: none;">
                        <svg width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="color: #bbb;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <!-- Photo Overlay Actions (Hover) -->
                    <div id="photoOverlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center; border-radius: 50%;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                        <button type="button" title="Edit Photo" onclick="triggerProfilePhotoUpload()" style="width: 40px; height: 40px; background: white; color: #222; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; margin: 0 6px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                            </svg>
                        </button>
                        <button type="button" title="Delete Photo" onclick="removeProfilePhoto()" style="width: 40px; height: 40px; background: white; color: #222; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; margin: 0 6px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                    <!-- Photo Upload Trigger (Hidden Input) -->
                    <input type="file" id="profilePhotoInput" accept="image/*" style="display: none;" onchange="uploadProfilePhoto()">
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

                        <!-- Transporter Type -->
                        <div class="form-group-modern">
                            <label for="profileTransporterType" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Transporter Type *</label>
                            <select id="profileTransporterType" name="transporter_type" class="form-control" style="text-align: left;">
                                <option value="">-- Select Type --</option>
                                <option value="Individual" <?= ($profile->transporter_type ?? '') === 'Individual' ? 'selected' : '' ?>>Individual</option>
                                <option value="Company" <?= ($profile->transporter_type ?? '') === 'Company' ? 'selected' : '' ?>>Company</option>
                            </select>
                            <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Operating as individual or registered company</small>
                            <span class="error-message" id="error-transporter_type"></span>
                        </div>

                        <!-- Full Address -->
                        <div class="form-group-modern full-width">
                            <label for="profileServiceAreas" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; text-align: left;">Service Areas *</label>
                            <textarea id="profileServiceAreas" name="service_areas" class="form-control" placeholder="e.g., Colombo, Gampaha, Kandy" rows="2" style="text-align: left;"><?= esc($profile->service_areas ?? '') ?></textarea>
                            <small style="color: #666; display: block; text-align: left; margin-top: 5px;">List the districts/areas you provide service to (comma-separated)</small>
                            <span class="error-message" id="error-service_areas"></span>
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
                        <p style="margin: 6px 0 0 0; text-align: left;">Your transportation journey at a glance</p>
                    </div>
                    <div class="stats-grid-modern">
                        <div class="stat-card-modern stat-primary">
                            <div class="stat-value" id="statMemberSince">-</div>
                            <div class="stat-label">Member Since</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-success">
                            <div class="stat-value" id="statTotalDeliveries">0</div>
                            <div class="stat-label">Total Deliveries</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="3" width="15" height="13"></rect>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-info">
                            <div class="stat-value" id="statAverageRating">0.0</div>
                            <div class="stat-label">Average Rating</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card-modern stat-warning">
                            <div class="stat-value" id="statTotalEarnings">Rs. 0</div>
                            <div class="stat-label">Total Earnings</div>
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password Modal -->
                <div id="changePasswordModal" class="modal" style="display: none;">
                    <div class="modal-content" style="max-width: 480px;">
                        <div class="modal-header">
                            <h3>Change Password</h3>
                            <button class="close-modal" onclick="closeChangePasswordModal()">&times;</button>
                        </div>
                        <form id="changePasswordForm">
                            <div class="form-group-modern">
                                <label for="currentPassword">Current Password *</label>
                                <input type="password" id="currentPassword" name="current_password" class="form-control" required>
                                <span class="error-message" id="error-current_password"></span>
                            </div>
                            <div class="form-group-modern">
                                <label for="newPassword">New Password *</label>
                                <input type="password" id="newPassword" name="new_password" class="form-control" required>
                                <small style="color: #666; display: block; text-align: left; margin-top: 5px;">Minimum 6 characters</small>
                                <span class="error-message" id="error-new_password"></span>
                            </div>
                            <div class="form-group-modern">
                                <label for="confirmPassword">Confirm New Password *</label>
                                <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                                <span class="error-message" id="error-confirm_password"></span>
                            </div>
                            <div class="modal-actions" style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end;">
                                <button type="button" class="btn btn-secondary" onclick="closeChangePasswordModal()">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="submitChangePassword()">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>