(function () {
    'use strict';

    const APP_ROOT = window.APP_ROOT || document.body.getAttribute('data-app-root') || '';
    const maxEmailChanges = 2;

    let originalProfileData = null;
    const accountSettingsState = {
        emailChangesUsed: 0,
        emailChangesRemaining: maxEmailChanges,
    };

    const profileFieldMap = {
        name: 'profileName',
        phone: 'profilePhone',
        district: 'profileDistrict',
        apartment_code: 'profileApartmentCode',
        street_name: 'profileStreetName',
        city: 'profileCity',
        postal_code: 'profilePostalCode',
        full_address: 'profileFullAddress',
        company_name: 'profileCompanyName',
        license_number: 'profileLicenseNumber',
        vehicle_type: 'profileVehicleType',
        availability: 'profileAvailability',
    };

    const emailFieldIds = ['newEmailAddress', 'emailChangePassword'];
    const emailServerFieldMap = {
        new_email: 'newEmailAddress',
        password: 'emailChangePassword',
    };

    const passwordFieldIds = ['currentPassword', 'newPassword', 'confirmPassword'];
    const passwordServerFieldMap = {
        current: 'currentPassword',
        new: 'newPassword',
        confirm: 'confirmPassword',
    };

    function parseJsonResponse(response) {
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid server response');
            }
        });
    }

    function profileNotify(message, type) {
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type || 'info');
            return;
        }

        const existing = document.querySelector('.notification');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = `notification ${type || 'info'}`;
        toast.textContent = String(message || 'Notification');
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;padding:12px 16px;border-radius:8px;background:#344054;color:#fff;box-shadow:0 8px 24px rgba(0,0,0,0.2);';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2400);
    }

    function onlyDigits(value) {
        return String(value || '').replace(/\D/g, '');
    }

    function normalizeText(value) {
        return String(value || '').trim().replace(/\s+/g, ' ');
    }

    function getFieldErrorContainer(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return null;
        return field.closest('.form-group') || field.parentElement;
    }

    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const container = getFieldErrorContainer(fieldId);
        if (field) {
            field.classList.remove('error');
            field.removeAttribute('aria-invalid');
        }
        if (container) {
            const errorNode = container.querySelector(`.field-error-text[data-for="${fieldId}"]`);
            if (errorNode) errorNode.remove();
        }
    }

    function setFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const container = getFieldErrorContainer(fieldId);
        if (!field || !container || !message) return;

        clearFieldError(fieldId);
        field.classList.add('error');
        field.setAttribute('aria-invalid', 'true');

        const error = document.createElement('small');
        error.className = 'field-error-text';
        error.dataset.for = fieldId;
        error.textContent = message;
        container.appendChild(error);
    }

    function clearFormErrors(fieldIds) {
        fieldIds.forEach(clearFieldError);
    }

    function applyErrorsFromMap(errorMap, fieldMap) {
        if (!errorMap || typeof errorMap !== 'object') return;
        Object.keys(errorMap).forEach(key => {
            const fieldId = fieldMap[key];
            if (fieldId) setFieldError(fieldId, errorMap[key]);
        });
    }

    function clearInlineStatus(statusId) {
        const statusEl = document.getElementById(statusId);
        if (!statusEl) return;
        statusEl.textContent = '';
        statusEl.classList.remove('success', 'error');
        statusEl.classList.add('is-hidden');
    }

    function setInlineStatus(statusId, message, type) {
        const statusEl = document.getElementById(statusId);
        if (!statusEl) return;
        statusEl.textContent = message;
        statusEl.classList.remove('is-hidden', 'success', 'error');
        statusEl.classList.add(type === 'error' ? 'error' : 'success');
    }

    function formatDate(value) {
        if (!value) return '-';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '-';
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
    }

    function formatDateTime(value) {
        if (!value) return '-';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '-';
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function updateEmailPolicyHint() {
        const hint = document.getElementById('emailChangePolicyHint');
        if (!hint) return;

        const used = Math.max(0, Number(accountSettingsState.emailChangesUsed || 0));
        const remaining = Math.max(0, Number(accountSettingsState.emailChangesRemaining || maxEmailChanges));
        hint.textContent = `Email change policy: maximum ${maxEmailChanges} changes. Used ${used}/${maxEmailChanges}. Remaining ${remaining}.`;
    }

    function updatePhotoActionState(hasPhoto) {
        const addBtn = document.getElementById('addPhotoBtn');
        const changeBtn = document.getElementById('changePhotoBtn');
        const removeBtn = document.getElementById('removePhotoBtn');

        if (addBtn) addBtn.classList.toggle('is-hidden', hasPhoto);
        if (changeBtn) changeBtn.classList.toggle('is-hidden', !hasPhoto);
        if (removeBtn) removeBtn.classList.toggle('is-hidden', !hasPhoto);
    }

    function setProfilePhoto(url) {
        const img = document.getElementById('profilePhotoDisplay');
        const defaultIcon = document.getElementById('defaultProfileIcon');
        if (!img) return;

        const hasPhoto = !!(url && String(url).trim() && String(url) !== 'undefined');
        if (hasPhoto) {
            img.src = String(url);
            img.classList.remove('is-hidden');
            if (defaultIcon) defaultIcon.classList.add('is-hidden');
        } else {
            img.classList.add('is-hidden');
            if (defaultIcon) defaultIcon.classList.remove('is-hidden');
        }

        updatePhotoActionState(hasPhoto);
    }

    function validatePhone(phoneValue) {
        const digits = onlyDigits(phoneValue);
        if (digits.length === 10 && digits.startsWith('0')) {
            return { valid: true, value: digits };
        }
        if (digits.length === 11 && digits.startsWith('94')) {
            return { valid: true, value: `+94${digits.slice(2)}` };
        }
        return { valid: false, error: 'Phone number must be a valid Sri Lankan number' };
    }

    function populateProfile(profile, photoUrl) {
        const fields = {
            profileName: profile.name || '',
            profileEmail: profile.email || '',
            profilePhone: profile.phone || '',
            profileDistrict: profile.district || '',
            profileApartmentCode: profile.apartment_code || '',
            profileStreetName: profile.street_name || '',
            profileCity: profile.city || '',
            profilePostalCode: profile.postal_code || '',
            profileFullAddress: profile.full_address || '',
            profileCompanyName: profile.company_name || '',
            profileLicenseNumber: profile.license_number || '',
            profileVehicleType: profile.vehicle_type || '',
            profileAvailability: profile.availability || '',
            accountSettingsEmail: profile.email || '',
        };

        Object.keys(fields).forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = fields[id];
        });

        const displayName = document.getElementById('profileDisplayName');
        const displayEmail = document.getElementById('profileDisplayEmail');
        const memberSinceValue = document.getElementById('memberSinceValue');
        const accountStatusValue = document.getElementById('accountStatusValue');
        const lastUpdatedValue = document.getElementById('lastUpdatedValue');

        if (displayName) displayName.textContent = profile.name || 'Transporter';
        if (displayEmail) displayEmail.textContent = profile.email || '';
        if (memberSinceValue) memberSinceValue.textContent = formatDate(profile.created_at);
        if (lastUpdatedValue) lastUpdatedValue.textContent = formatDateTime(profile.updated_at);
        if (accountStatusValue) {
            const status = String(profile.status || 'active').toLowerCase();
            accountStatusValue.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        }

        setProfilePhoto(photoUrl || null);
    }

    function loadProfileData() {
        fetch(`${APP_ROOT}/transporterprofile?ajax=1&t=${Date.now()}`, {
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-cache',
        })
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch profile');
                return parseJsonResponse(response);
            })
            .then(res => {
                if (!res.success || !res.profile) {
                    profileNotify(res.error || 'Could not load profile', 'error');
                    return;
                }

                populateProfile(res.profile, res.photoUrl || null);
                originalProfileData = {
                    profile: { ...res.profile },
                    photoUrl: res.photoUrl || null,
                };

                if (typeof res.emailChangesUsed !== 'undefined') {
                    accountSettingsState.emailChangesUsed = Number(res.emailChangesUsed) || 0;
                }
                if (typeof res.emailChangesRemaining !== 'undefined') {
                    accountSettingsState.emailChangesRemaining = Number(res.emailChangesRemaining) || 0;
                } else {
                    accountSettingsState.emailChangesRemaining = Math.max(0, maxEmailChanges - accountSettingsState.emailChangesUsed);
                }
                updateEmailPolicyHint();
            })
            .catch(err => {
                console.error('Transporter profile load error:', err);
                profileNotify('Error loading profile data', 'error');
            });
    }

    function getProfilePayload() {
        return {
            name: normalizeText(document.getElementById('profileName')?.value || ''),
            phone: normalizeText(document.getElementById('profilePhone')?.value || ''),
            district: normalizeText(document.getElementById('profileDistrict')?.value || ''),
            apartment_code: normalizeText(document.getElementById('profileApartmentCode')?.value || ''),
            street_name: normalizeText(document.getElementById('profileStreetName')?.value || ''),
            city: normalizeText(document.getElementById('profileCity')?.value || ''),
            postal_code: normalizeText(document.getElementById('profilePostalCode')?.value || ''),
            full_address: normalizeText(document.getElementById('profileFullAddress')?.value || ''),
            company_name: normalizeText(document.getElementById('profileCompanyName')?.value || ''),
            license_number: normalizeText(document.getElementById('profileLicenseNumber')?.value || ''),
            vehicle_type: normalizeText(document.getElementById('profileVehicleType')?.value || ''),
            availability: normalizeText(document.getElementById('profileAvailability')?.value || ''),
        };
    }

    function validateProfilePayload(payload) {
        const errors = {};

        if (!payload.name) {
            errors.name = 'Full name is required';
        }

        const phoneValidation = validatePhone(payload.phone);
        if (!payload.phone) {
            errors.phone = 'Phone number is required';
        } else if (!phoneValidation.valid) {
            errors.phone = phoneValidation.error;
        } else {
            payload.phone = phoneValidation.value;
        }

        if (!payload.district) errors.district = 'District is required';
        if (!payload.street_name) errors.street_name = 'Street name is required';
        if (!payload.city) errors.city = 'City is required';

        if (!payload.postal_code) {
            errors.postal_code = 'Postal code is required';
        } else if (!/^\d{5}$/.test(payload.postal_code)) {
            errors.postal_code = 'Postal code must be 5 digits';
        }

        if (!payload.license_number) errors.license_number = 'License number is required';
        if (!payload.vehicle_type) errors.vehicle_type = 'Vehicle type is required';
        if (!payload.availability) errors.availability = 'Availability is required';

        return {
            valid: Object.keys(errors).length === 0,
            errors,
            payload,
        };
    }

    function saveProfileData() {
        clearFormErrors(Object.values(profileFieldMap));

        const payload = getProfilePayload();
        const validation = validateProfilePayload(payload);
        if (!validation.valid) {
            Object.keys(validation.errors).forEach(key => {
                const fieldId = profileFieldMap[key];
                if (fieldId) setFieldError(fieldId, validation.errors[key]);
            });
            const firstFieldId = profileFieldMap[Object.keys(validation.errors)[0]];
            const firstField = firstFieldId ? document.getElementById(firstFieldId) : null;
            if (firstField) firstField.focus();
            return;
        }

        const saveBtn = document.getElementById('saveProfileBtn');
        const originalText = saveBtn ? saveBtn.textContent : '';
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
        }

        fetch(`${APP_ROOT}/transporterprofile/saveProfile`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams(validation.payload),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = originalText;
                }

                if (res.success) {
                    profileNotify(res.message || 'Profile updated successfully', 'success');
                    loadProfileData();
                    return;
                }

                if (res.errors && typeof res.errors === 'object') {
                    applyErrorsFromMap(res.errors, profileFieldMap);
                    const firstFieldId = profileFieldMap[Object.keys(res.errors)[0]];
                    const firstField = firstFieldId ? document.getElementById(firstFieldId) : null;
                    if (firstField) firstField.focus();
                    return;
                }

                profileNotify(res.error || 'Failed to update profile', 'error');
            })
            .catch(err => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = originalText;
                }
                console.error('Transporter profile save error:', err);
                profileNotify('Error saving profile', 'error');
            });
    }

    function resetProfileForm() {
        clearFormErrors(Object.values(profileFieldMap));
        if (!originalProfileData || !originalProfileData.profile) {
            loadProfileData();
            return;
        }
        populateProfile(originalProfileData.profile, originalProfileData.photoUrl);
    }

    function triggerProfilePhotoUpload() {
        const input = document.getElementById('profilePhotoFileInput');
        if (input) input.click();
    }

    function uploadProfilePhotoFromInput(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            profileNotify('Please select JPG, PNG, or WebP image', 'error');
            event.target.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            profileNotify('Image size must be less than 5MB', 'error');
            event.target.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('photo', file);

        fetch(`${APP_ROOT}/transporterprofile/uploadPhoto`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData,
        })
            .then(parseJsonResponse)
            .then(res => {
                if (res.success && res.photoUrl) {
                    setProfilePhoto(`${res.photoUrl}?t=${Date.now()}`);
                    profileNotify(res.message || 'Profile photo updated', 'success');
                    return;
                }
                profileNotify(res.error || 'Failed to upload photo', 'error');
            })
            .catch(err => {
                console.error('Transporter photo upload error:', err);
                profileNotify('Error uploading photo', 'error');
            })
            .finally(() => {
                event.target.value = '';
            });
    }

    function removeProfilePhoto() {
        if (!confirm('Remove your profile photo?')) return;

        fetch(`${APP_ROOT}/transporterprofile/removePhoto`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(parseJsonResponse)
            .then(res => {
                if (res.success) {
                    setProfilePhoto(null);
                    profileNotify(res.message || 'Profile photo removed', 'success');
                    return;
                }
                profileNotify(res.error || 'Failed to remove profile photo', 'error');
            })
            .catch(err => {
                console.error('Transporter remove photo error:', err);
                profileNotify('Error removing profile photo', 'error');
            });
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('show');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('show');
    }

    function toggleSettingsPanel(panelId, forceOpen) {
        const panel = document.getElementById(panelId);
        if (!panel) return;

        const shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !panel.classList.contains('is-open');
        const toggleBtn = document.querySelector(`.account-settings-toggle[data-settings-target="${panelId}"]`);

        if (shouldOpen) {
            document.querySelectorAll('.account-settings-panel').forEach(otherPanel => {
                if (otherPanel.id !== panelId) otherPanel.classList.remove('is-open');
            });

            document.querySelectorAll('.account-settings-toggle[data-settings-target]').forEach(otherBtn => {
                if (otherBtn.getAttribute('data-settings-target') !== panelId) {
                    otherBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        panel.classList.toggle('is-open', shouldOpen);
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    }

    function openAccountSettingsModal() {
        const profileEmail = document.getElementById('profileEmail');
        const accountEmail = document.getElementById('accountSettingsEmail');
        if (profileEmail && accountEmail) accountEmail.value = profileEmail.value || '';

        clearFormErrors(emailFieldIds);
        clearFormErrors(passwordFieldIds);
        clearInlineStatus('emailChangeStatus');
        clearInlineStatus('passwordChangeStatus');

        const changeEmailForm = document.getElementById('changeEmailForm');
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changeEmailForm) changeEmailForm.reset();
        if (changePasswordForm) changePasswordForm.reset();

        document.querySelectorAll('.account-settings-panel').forEach(panel => panel.classList.remove('is-open'));
        document.querySelectorAll('.account-settings-toggle[data-settings-target]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));

        updateEmailPolicyHint();
        openModal('accountSettingsModal');
    }

    function validateEmailChange(newEmail, password) {
        const errors = {};
        const normalizedEmail = String(newEmail || '').trim().toLowerCase();
        const currentEmail = String(document.getElementById('accountSettingsEmail')?.value || '').trim().toLowerCase();

        if (!normalizedEmail) {
            errors.newEmailAddress = 'New email is required';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(normalizedEmail)) {
            errors.newEmailAddress = 'Enter a valid email address';
        } else if (currentEmail && normalizedEmail === currentEmail) {
            errors.newEmailAddress = 'New email must be different from current email';
        }

        if (!password) {
            errors.emailChangePassword = 'Password confirmation is required';
        }

        return {
            valid: Object.keys(errors).length === 0,
            errors,
            normalizedEmail,
        };
    }

    function changeEmail(event) {
        event.preventDefault();
        clearFormErrors(emailFieldIds);
        clearInlineStatus('emailChangeStatus');

        const newEmail = document.getElementById('newEmailAddress')?.value || '';
        const password = document.getElementById('emailChangePassword')?.value || '';
        const validation = validateEmailChange(newEmail, password);

        if (!validation.valid) {
            Object.keys(validation.errors).forEach(fieldId => setFieldError(fieldId, validation.errors[fieldId]));
            const firstField = document.getElementById(Object.keys(validation.errors)[0]);
            if (firstField) firstField.focus();
            return;
        }

        const submitBtn = document.getElementById('changeEmailBtn');
        const originalText = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        }

        fetch(`${APP_ROOT}/transporterprofile/changeEmail`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({
                newEmail: validation.normalizedEmail,
                password,
            }),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }

                if (res.success) {
                    const newValue = res.email || validation.normalizedEmail;
                    const profileEmail = document.getElementById('profileEmail');
                    const currentEmail = document.getElementById('accountSettingsEmail');
                    const displayEmail = document.getElementById('profileDisplayEmail');
                    if (profileEmail) profileEmail.value = newValue;
                    if (currentEmail) currentEmail.value = newValue;
                    if (displayEmail) displayEmail.textContent = newValue;

                    accountSettingsState.emailChangesUsed = Number(res.emailChangesUsed ?? accountSettingsState.emailChangesUsed);
                    accountSettingsState.emailChangesRemaining = Number(res.emailChangesRemaining ?? 0);
                    updateEmailPolicyHint();

                    const emailForm = document.getElementById('changeEmailForm');
                    if (emailForm) emailForm.reset();

                    setInlineStatus('emailChangeStatus', res.message || 'Email changed successfully', 'success');
                    profileNotify('Email updated successfully', 'success');
                    return;
                }

                if (res.errors && typeof res.errors === 'object') {
                    applyErrorsFromMap(res.errors, emailServerFieldMap);
                    if (res.errors.limit) {
                        setInlineStatus('emailChangeStatus', res.errors.limit, 'error');
                    }

                    if (typeof res.emailChangesUsed !== 'undefined') {
                        accountSettingsState.emailChangesUsed = Number(res.emailChangesUsed ?? accountSettingsState.emailChangesUsed);
                    }
                    if (typeof res.emailChangesRemaining !== 'undefined') {
                        accountSettingsState.emailChangesRemaining = Number(res.emailChangesRemaining ?? 0);
                    }
                    updateEmailPolicyHint();

                    const firstServerKey = Object.keys(res.errors)[0];
                    const firstFieldId = emailServerFieldMap[firstServerKey];
                    const firstField = firstFieldId ? document.getElementById(firstFieldId) : null;
                    if (firstField) firstField.focus();
                    return;
                }

                setInlineStatus('emailChangeStatus', res.error || 'Failed to change email', 'error');
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
                console.error('Transporter change email error:', err);
                setInlineStatus('emailChangeStatus', 'Error changing email', 'error');
            });
    }

    function validatePasswordFields(currentPassword, newPassword, confirmPassword) {
        const errors = {};

        if (!currentPassword) errors.currentPassword = 'Current password is required';

        if (!newPassword) {
            errors.newPassword = 'New password is required';
        } else if (newPassword.length < 8) {
            errors.newPassword = 'New password must be at least 8 characters';
        } else if (!/[A-Za-z]/.test(newPassword) || !/[0-9]/.test(newPassword)) {
            errors.newPassword = 'Use at least one letter and one number';
        }

        if (!confirmPassword) {
            errors.confirmPassword = 'Confirm password is required';
        } else if (newPassword !== confirmPassword) {
            errors.confirmPassword = 'Passwords do not match';
        }

        return errors;
    }

    function changePassword(event) {
        event.preventDefault();

        clearFormErrors(passwordFieldIds);
        clearInlineStatus('passwordChangeStatus');

        const currentPassword = document.getElementById('currentPassword')?.value || '';
        const newPassword = document.getElementById('newPassword')?.value || '';
        const confirmPassword = document.getElementById('confirmPassword')?.value || '';

        const passwordErrors = validatePasswordFields(currentPassword, newPassword, confirmPassword);
        if (Object.keys(passwordErrors).length > 0) {
            Object.keys(passwordErrors).forEach(fieldId => setFieldError(fieldId, passwordErrors[fieldId]));
            const firstField = document.getElementById(Object.keys(passwordErrors)[0]);
            if (firstField) firstField.focus();
            return;
        }

        const submitBtn = document.getElementById('changePasswordBtn');
        const originalText = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        }

        fetch(`${APP_ROOT}/transporterprofile/changePassword`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword,
            }),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }

                if (res.success) {
                    const form = document.getElementById('changePasswordForm');
                    if (form) form.reset();
                    setInlineStatus('passwordChangeStatus', res.message || 'Password changed successfully.', 'success');
                    profileNotify('Password updated successfully', 'success');
                    return;
                }

                if (res.errors && typeof res.errors === 'object') {
                    applyErrorsFromMap(res.errors, passwordServerFieldMap);
                    const firstServerKey = Object.keys(res.errors)[0];
                    const firstFieldId = passwordServerFieldMap[firstServerKey];
                    const firstField = firstFieldId ? document.getElementById(firstFieldId) : null;
                    if (firstField) firstField.focus();
                    setInlineStatus('passwordChangeStatus', 'Please correct the highlighted fields.', 'error');
                    return;
                }

                setInlineStatus('passwordChangeStatus', res.error || 'Failed to change password', 'error');
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
                console.error('Transporter change password error:', err);
                setInlineStatus('passwordChangeStatus', 'Error changing password', 'error');
            });
    }

    function loadPayoutDetails() {
        fetch(`${APP_ROOT}/transporterprofile/getPayoutAccount`, {
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-cache',
        })
            .then(parseJsonResponse)
            .then(res => {
                const account = res && res.success ? (res.account || null) : null;
                const map = {
                    payoutAccountName: account?.account_holder_name || '',
                    payoutBankName: account?.bank_name || '',
                    payoutAccountNumber: account?.account_number || '',
                    payoutBranchName: account?.branch_name || '',
                };
                Object.keys(map).forEach(id => {
                    const field = document.getElementById(id);
                    if (field) field.value = map[id];
                });
            })
            .catch(err => {
                console.error('Load payout details error:', err);
            });
    }

    function savePayoutDetails(event) {
        event.preventDefault();

        const accountHolderName = normalizeText(document.getElementById('payoutAccountName')?.value || '');
        const bankName = normalizeText(document.getElementById('payoutBankName')?.value || '');
        const accountNumber = onlyDigits(document.getElementById('payoutAccountNumber')?.value || '');
        const branchName = normalizeText(document.getElementById('payoutBranchName')?.value || '');

        if (!accountHolderName || !bankName || !accountNumber) {
            profileNotify('Account holder, bank name, and account number are required', 'error');
            return;
        }

        fetch(`${APP_ROOT}/transporterprofile/savePayoutAccount`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                account_holder_name: accountHolderName,
                bank_name: bankName,
                account_number: accountNumber,
                branch_name: branchName,
            }),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (res.success) {
                    profileNotify(res.message || 'Payout account saved', 'success');
                    closeModal('payoutDetailsModal');
                    return;
                }
                profileNotify(res.error || 'Failed to save payout account', 'error');
            })
            .catch(err => {
                console.error('Save payout details error:', err);
                profileNotify('Error saving payout account', 'error');
            });
    }

    function openPayoutModal() {
        loadPayoutDetails();
        openModal('payoutDetailsModal');
    }

    function confirmDeactivateAccount() {
        const reason = normalizeText(document.getElementById('deactivateReason')?.value || '');
        const confirmBtn = document.getElementById('confirmDeactivateBtn');
        const originalText = confirmBtn ? confirmBtn.textContent : '';

        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Submitting...';
        }

        fetch(`${APP_ROOT}/transporterprofile/requestDeactivation`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({ reason }),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = originalText;
                }

                if (res.success) {
                    profileNotify(res.message || 'Account deactivated successfully.', 'warning');
                    window.location.href = res.redirect || `${APP_ROOT}/login?deactivated=1`;
                    return;
                }

                const blockedCount = Number(res.incompleteDeliveryCount || 0);
                if (blockedCount > 0) {
                    const label = blockedCount === 1 ? 'delivery is' : 'deliveries are';
                    profileNotify(`Cannot deactivate account. ${blockedCount} ${label} still incomplete.`, 'error');
                    return;
                }

                profileNotify(res.error || 'Failed to deactivate account', 'error');
            })
            .catch(err => {
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = originalText;
                }
                console.error('Transporter deactivation error:', err);
                profileNotify('Error submitting deactivation request', 'error');
            });
    }

    function initializeProfilePage() {
        loadProfileData();
        loadPayoutDetails();

        const saveBtn = document.getElementById('saveProfileBtn');
        const resetBtn = document.getElementById('resetProfileBtn');
        if (saveBtn) saveBtn.addEventListener('click', saveProfileData);
        if (resetBtn) resetBtn.addEventListener('click', resetProfileForm);

        const profilePhone = document.getElementById('profilePhone');
        if (profilePhone) {
            profilePhone.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9+]/g, '').slice(0, 13);
                clearFieldError('profilePhone');
            });
        }

        const profilePostalCode = document.getElementById('profilePostalCode');
        if (profilePostalCode) {
            profilePostalCode.addEventListener('input', function () {
                this.value = onlyDigits(this.value).slice(0, 5);
                clearFieldError('profilePostalCode');
            });
        }

        const payoutAccountNumber = document.getElementById('payoutAccountNumber');
        if (payoutAccountNumber) {
            payoutAccountNumber.addEventListener('input', function () {
                this.value = onlyDigits(this.value).slice(0, 30);
            });
        }

        Object.values(profileFieldMap).forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field) return;
            field.addEventListener('input', () => clearFieldError(fieldId));
            if (field.tagName === 'SELECT') {
                field.addEventListener('change', () => clearFieldError(fieldId));
            }
        });

        emailFieldIds.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field) return;
            field.addEventListener('input', () => {
                clearFieldError(fieldId);
                clearInlineStatus('emailChangeStatus');
            });
        });

        passwordFieldIds.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field) return;
            field.addEventListener('input', () => {
                clearFieldError(fieldId);
                clearInlineStatus('passwordChangeStatus');
            });
        });

        const addPhotoBtn = document.getElementById('addPhotoBtn');
        const changePhotoBtn = document.getElementById('changePhotoBtn');
        const removePhotoBtn = document.getElementById('removePhotoBtn');
        const photoFileInput = document.getElementById('profilePhotoFileInput');
        if (addPhotoBtn) addPhotoBtn.addEventListener('click', triggerProfilePhotoUpload);
        if (changePhotoBtn) changePhotoBtn.addEventListener('click', triggerProfilePhotoUpload);
        if (removePhotoBtn) removePhotoBtn.addEventListener('click', removeProfilePhoto);
        if (photoFileInput) photoFileInput.addEventListener('change', uploadProfilePhotoFromInput);

        document.querySelectorAll('.profile-shortcut-card[data-open-modal]').forEach(card => {
            card.addEventListener('click', function () {
                const target = this.getAttribute('data-open-modal');
                if (target === 'accountSettingsModal') {
                    openAccountSettingsModal();
                } else if (target === 'payoutDetailsModal') {
                    openPayoutModal();
                } else {
                    openModal(target);
                }
            });
        });

        document.querySelectorAll('[data-open-modal]').forEach(btn => {
            btn.addEventListener('click', function () {
                if (this.classList.contains('profile-shortcut-card')) {
                    return;
                }
                const target = this.getAttribute('data-open-modal');
                if (!target) return;
                if (target === 'accountSettingsModal') {
                    openAccountSettingsModal();
                } else if (target === 'payoutDetailsModal') {
                    openPayoutModal();
                } else {
                    openModal(target);
                }
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', function () {
                closeModal(this.getAttribute('data-close-modal'));
            });
        });

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) modal.classList.remove('show');
            });
        });

        document.querySelectorAll('.account-settings-toggle[data-settings-target]').forEach(toggle => {
            toggle.addEventListener('click', function () {
                const target = this.getAttribute('data-settings-target');
                if (target) toggleSettingsPanel(target);
            });
        });

        const changeEmailForm = document.getElementById('changeEmailForm');
        if (changeEmailForm) changeEmailForm.addEventListener('submit', changeEmail);

        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) changePasswordForm.addEventListener('submit', changePassword);

        const payoutDetailsForm = document.getElementById('payoutDetailsForm');
        if (payoutDetailsForm) payoutDetailsForm.addEventListener('submit', savePayoutDetails);

        const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');
        if (confirmDeactivateBtn) confirmDeactivateBtn.addEventListener('click', confirmDeactivateAccount);
    }

    window.TransporterProfile = {
        loadProfileData,
        saveProfileData,
        resetProfileForm,
        openAccountSettingsModal,
        openPayoutModal,
        loadPayoutDetails,
        savePayoutDetails,
        triggerProfilePhotoUpload,
        removeProfilePhoto,
        confirmDeactivateAccount,
        toggleSettingsPanel,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProfilePage);
    } else {
        initializeProfilePage();
    }
})();