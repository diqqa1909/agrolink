// Buyer Profile Management
(function () {
    'use strict';

    const APP_ROOT = window.APP_ROOT || document.body.getAttribute('data-app-root') || '';
    const maxEmailChanges = 2;

    let originalProfileData = null;
    let savedCards = [];
    const accountSettingsState = {
        emailChangesUsed: 0,
        emailChangesRemaining: maxEmailChanges,
    };

    const profileFieldMap = {
        name: 'profileName',
        email: 'profileEmail',
        phone: 'profilePhone',
        district: 'profileDistrict',
        apartment_code: 'profileApartmentCode',
        street_name: 'profileStreetName',
        city: 'profileCity',
        postal_code: 'profilePostalCode',
        additional_address_details: 'profileAdditionalAddress',
    };

    const passwordFieldIds = ['currentPassword', 'newPassword', 'confirmPassword'];
    const passwordServerFieldMap = {
        current: 'currentPassword',
        new: 'newPassword',
        confirm: 'confirmPassword',
    };

    const emailFieldIds = ['newEmailAddress', 'emailChangePassword'];
    const emailServerFieldMap = {
        new_email: 'newEmailAddress',
        password: 'emailChangePassword',
    };

    const cardFieldIds = ['cardHolderName', 'cardNumber', 'cardExpiryMonth', 'cardExpiryYear'];
    const cardServerFieldMap = {
        card_holder_name: 'cardHolderName',
        card_last_four: 'cardNumber',
        expiry_month: 'cardExpiryMonth',
        expiry_year: 'cardExpiryYear',
    };

    function parseJsonResponse(response) {
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (err) {
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

        const notification = document.createElement('div');
        notification.className = `notification ${type || 'info'}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 14px 20px;
            color: white;
            border-radius: 8px;
            z-index: 10001;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 2800);
    }

    function onlyDigits(value) {
        return String(value || '').replace(/\D/g, '');
    }

    function normalizeText(value) {
        return String(value || '').trim().replace(/\s+/g, ' ');
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function openProfileModal(id) {
        if (typeof window.openModal === 'function') {
            window.openModal(id);
            return;
        }
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('show');
    }

    function closeProfileModal(id) {
        if (typeof window.closeModal === 'function') {
            window.closeModal(id);
            return;
        }
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('show');
    }

    function formatDate(value) {
        if (!value) return '-';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '-';
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
        });
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

    function updateEmailPolicyHint() {
        const hint = document.getElementById('emailChangePolicyHint');
        if (!hint) return;

        const used = Math.max(0, Number(accountSettingsState.emailChangesUsed || 0));
        const remaining = Math.max(0, Number(accountSettingsState.emailChangesRemaining || maxEmailChanges));
        hint.textContent = `Email change policy: maximum ${maxEmailChanges} changes. Used ${used}/${maxEmailChanges}. Remaining ${remaining}.`;
    }

    function validatePhone(phoneValue) {
        const digits = onlyDigits(phoneValue);

        if (digits.length === 10 && digits.startsWith('0')) {
            return { valid: true, value: digits };
        }

        if (digits.length === 11 && digits.startsWith('94')) {
            return { valid: true, value: `+94${digits.slice(2)}` };
        }

        return {
            valid: false,
            error: 'Phone number must be a valid Sri Lankan number',
        };
    }

    function getProfilePayload() {
        return {
            name: normalizeText(document.getElementById('profileName')?.value || ''),
            email: normalizeText(document.getElementById('profileEmail')?.value || '').toLowerCase(),
            phone: normalizeText(document.getElementById('profilePhone')?.value || ''),
            district: normalizeText(document.getElementById('profileDistrict')?.value || ''),
            apartment_code: normalizeText(document.getElementById('profileApartmentCode')?.value || ''),
            street_name: normalizeText(document.getElementById('profileStreetName')?.value || ''),
            city: normalizeText(document.getElementById('profileCity')?.value || ''),
            postal_code: normalizeText(document.getElementById('profilePostalCode')?.value || ''),
            additional_address_details: normalizeText(document.getElementById('profileAdditionalAddress')?.value || ''),
        };
    }

    function validateProfilePayload(payload) {
        const errors = {};

        if (!payload.name) {
            errors.name = 'Full name is required';
        } else if (payload.name.length < 2 || payload.name.length > 100 || !/^[A-Za-z\s.'-]+$/.test(payload.name)) {
            errors.name = 'Enter a valid full name';
        }

        const phoneValidation = validatePhone(payload.phone);
        if (!payload.phone) {
            errors.phone = 'Phone number is required';
        } else if (!phoneValidation.valid) {
            errors.phone = phoneValidation.error;
        } else {
            payload.phone = phoneValidation.value;
        }

        if (!payload.district) {
            errors.district = 'District is required';
        }

        if (payload.apartment_code.length > 50) {
            errors.apartment_code = 'Apartment or building code is too long';
        }

        if (!payload.street_name) {
            errors.street_name = 'Street name is required';
        } else if (payload.street_name.length > 100) {
            errors.street_name = 'Street name is too long';
        }

        if (!payload.city) {
            errors.city = 'City is required';
        } else if (payload.city.length > 50) {
            errors.city = 'City is too long';
        }

        if (!payload.postal_code) {
            errors.postal_code = 'Postal code is required';
        } else if (!/^\d{5}$/.test(payload.postal_code)) {
            errors.postal_code = 'Postal code must be 5 digits';
        }

        if (payload.additional_address_details.length > 100) {
            errors.additional_address_details = 'Additional address details are too long';
        }

        return {
            valid: Object.keys(errors).length === 0,
            errors,
            payload,
        };
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

    function validatePasswordFields(currentPassword, newPassword, confirmPassword) {
        const errors = {};

        if (!currentPassword) {
            errors.currentPassword = 'Current password is required';
        }

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

    function populateProfile(profile, photoUrl, stats) {
        const fieldMap = {
            profileName: profile.name || '',
            profileEmail: profile.email || '',
            profilePhone: profile.phone || '',
            profileDistrict: profile.district || '',
            profileApartmentCode: profile.apartment_code || '',
            profileStreetName: profile.street_name || '',
            profileCity: profile.city || '',
            profilePostalCode: profile.postal_code || '',
            profileAdditionalAddress: profile.additional_address_details || '',
            accountSettingsEmail: profile.email || '',
        };

        Object.keys(fieldMap).forEach(id => {
            const field = document.getElementById(id);
            if (field) field.value = fieldMap[id];
        });

        const displayName = document.getElementById('profileDisplayName');
        const displayEmail = document.getElementById('profileDisplayEmail');
        const memberSinceValue = document.getElementById('memberSinceValue');
        const totalOrdersValue = document.getElementById('totalOrdersValue');
        const activeDeliveriesValue = document.getElementById('activeDeliveriesValue');

        if (displayName) displayName.textContent = profile.name || 'Buyer';
        if (displayEmail) displayEmail.textContent = profile.email || '';

        const memberSince = stats?.member_since || profile.created_at || null;
        const totalOrders = Number(stats?.total_orders || 0);
        const activeDeliveries = Number(stats?.active_deliveries || 0);

        if (memberSinceValue) memberSinceValue.textContent = formatDate(memberSince);
        if (totalOrdersValue) totalOrdersValue.textContent = String(totalOrders);
        if (activeDeliveriesValue) activeDeliveriesValue.textContent = String(activeDeliveries);

        setProfilePhoto(photoUrl || null);
    }

    function loadProfileData() {
        fetch(`${APP_ROOT}/buyerprofile?ajax=1&t=${Date.now()}`, {
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-cache',
        })
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch profile');
                return response.json();
            })
            .then(res => {
                if (!res.success || !res.profile) {
                    profileNotify(res.error || 'Could not load profile', 'error');
                    return;
                }

                populateProfile(res.profile, res.photoUrl, res.profileStats || {});
                originalProfileData = {
                    profile: { ...res.profile },
                    photoUrl: res.photoUrl || null,
                    profileStats: { ...(res.profileStats || {}) },
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
                console.error('Profile load error:', err);
                profileNotify('Error loading profile data', 'error');
            });
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

        fetch(`${APP_ROOT}/buyerprofile/saveProfile`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
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

                console.error('Profile save error:', err);
                profileNotify('Error saving profile', 'error');
            });
    }

    function resetProfileForm() {
        clearFormErrors(Object.values(profileFieldMap));

        if (!originalProfileData || !originalProfileData.profile) {
            loadProfileData();
            return;
        }

        populateProfile(originalProfileData.profile, originalProfileData.photoUrl, originalProfileData.profileStats || {});
    }

    function triggerProfilePhotoUpload() {
        let input = document.getElementById('buyerProfilePhotoInput');
        if (!input) {
            input = document.createElement('input');
            input.type = 'file';
            input.id = 'buyerProfilePhotoInput';
            input.accept = 'image/*';
            input.style.display = 'none';
            document.body.appendChild(input);

            input.addEventListener('change', function (event) {
                const file = event.target.files && event.target.files[0];
                if (!file) return;

                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    profileNotify('Please select JPG, PNG, or WebP image', 'error');
                    input.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    profileNotify('Image size must be less than 5MB', 'error');
                    input.value = '';
                    return;
                }

                uploadProfilePhoto(file);
            });
        }

        input.click();
    }

    function uploadProfilePhoto(file) {
        const formData = new FormData();
        formData.append('photo', file);

        fetch(`${APP_ROOT}/buyerprofile/uploadPhoto`, {
            method: 'POST',
            credentials: 'include',
            body: formData,
        })
            .then(parseJsonResponse)
            .then(res => {
                if (res.success && res.photoUrl) {
                    setProfilePhoto(`${res.photoUrl}?t=${Date.now()}`);
                    profileNotify('Profile photo updated', 'success');
                    return;
                }
                profileNotify(res.error || 'Failed to upload photo', 'error');
            })
            .catch(err => {
                console.error('Photo upload error:', err);
                profileNotify('Error uploading photo', 'error');
            })
            .finally(() => {
                const input = document.getElementById('buyerProfilePhotoInput');
                if (input) input.value = '';
            });
    }

    function removeProfilePhoto() {
        if (!confirm('Remove your profile photo?')) return;

        fetch(`${APP_ROOT}/buyerprofile/removePhoto`, {
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
                console.error('Remove photo error:', err);
                profileNotify('Error removing profile photo', 'error');
            });
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
        const currentEmailField = document.getElementById('accountSettingsEmail');
        if (profileEmail && currentEmailField) {
            currentEmailField.value = profileEmail.value || '';
        }

        clearFormErrors(passwordFieldIds);
        clearFormErrors(emailFieldIds);
        clearInlineStatus('emailChangeStatus');
        clearInlineStatus('passwordChangeStatus');

        const changeEmailForm = document.getElementById('changeEmailForm');
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changeEmailForm) changeEmailForm.reset();
        if (changePasswordForm) changePasswordForm.reset();

        document.querySelectorAll('.account-settings-panel').forEach(panel => panel.classList.remove('is-open'));
        document.querySelectorAll('.account-settings-toggle[data-settings-target]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));

        updateEmailPolicyHint();
        openProfileModal('accountSettingsModal');
    }

    function changeEmail(event) {
        event.preventDefault();

        clearFormErrors(emailFieldIds);
        clearInlineStatus('emailChangeStatus');

        const newEmail = document.getElementById('newEmailAddress')?.value || '';
        const password = document.getElementById('emailChangePassword')?.value || '';
        const validation = validateEmailChange(newEmail, password);

        if (!validation.valid) {
            Object.keys(validation.errors).forEach(fieldId => {
                setFieldError(fieldId, validation.errors[fieldId]);
            });

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

        fetch(`${APP_ROOT}/buyerprofile/changeEmail`, {
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
                console.error('Change email error:', err);
                setInlineStatus('emailChangeStatus', 'Error changing email', 'error');
            });
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

        fetch(`${APP_ROOT}/buyerprofile/changePassword`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({ currentPassword, newPassword, confirmPassword }),
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
                console.error('Change password error:', err);
                setInlineStatus('passwordChangeStatus', 'Error changing password', 'error');
            });
    }

    function confirmDeactivateAccount() {
        const reasonField = document.getElementById('deactivateReason');
        const reason = normalizeText(reasonField?.value || '');

        const confirmBtn = document.getElementById('confirmDeactivateBtn');
        const originalText = confirmBtn ? confirmBtn.textContent : '';
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Submitting...';
        }

        fetch(`${APP_ROOT}/buyerprofile/requestDeactivation`, {
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
                    closeProfileModal('deactivateAccountModal');
                    if (reasonField) reasonField.value = '';
                    profileNotify(res.message || 'Account deactivated successfully', 'warning');
                    window.location.href = res.redirect || `${APP_ROOT}/login?deactivated=1`;
                    return;
                }

                profileNotify(res.error || 'Failed to submit deactivation request', 'error');
            })
            .catch(err => {
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = originalText;
                }
                console.error('Deactivation request error:', err);
                profileNotify('Error submitting deactivation request', 'error');
            });
    }

    function detectCardBrand(digits) {
        if (/^4\d{12}(\d{3})?(\d{3})?$/.test(digits)) return 'Visa';
        if (/^(5[1-5]\d{14}|2(2[2-9]|[3-6]\d|7[01])\d{12})$/.test(digits)) return 'Mastercard';
        if (/^3[47]\d{13}$/.test(digits)) return 'Amex';
        if (/^6(?:011|5\d{2}|4[4-9]\d)\d{12,15}$/.test(digits)) return 'Discover';
        return 'Card';
    }

    function luhnCheck(digits) {
        let sum = 0;
        let shouldDouble = false;

        for (let i = digits.length - 1; i >= 0; i -= 1) {
            let digit = Number(digits.charAt(i));
            if (shouldDouble) {
                digit *= 2;
                if (digit > 9) digit -= 9;
            }
            sum += digit;
            shouldDouble = !shouldDouble;
        }

        return sum % 10 === 0;
    }

    function formatCardNumberInput(value) {
        const digits = onlyDigits(value).slice(0, 19);
        return digits.replace(/(.{4})/g, '$1 ').trim();
    }

    function validateCardForm(rawData) {
        const errors = {};

        const holderName = normalizeText(rawData.holderName);
        const cardNumberDigits = onlyDigits(rawData.cardNumber);
        const expiryMonthDigits = onlyDigits(rawData.expiryMonth);
        const expiryYearDigits = onlyDigits(rawData.expiryYear);

        if (!holderName) {
            errors.cardHolderName = 'Card holder name is required';
        } else if (!/^[A-Za-z][A-Za-z\s.'-]{1,79}$/.test(holderName)) {
            errors.cardHolderName = 'Enter a valid card holder name';
        }

        if (!cardNumberDigits) {
            errors.cardNumber = 'Card number is required';
        } else if (!/^\d{12,19}$/.test(cardNumberDigits)) {
            errors.cardNumber = 'Card number must be 12 to 19 digits';
        } else if (!luhnCheck(cardNumberDigits)) {
            errors.cardNumber = 'Card number is invalid';
        }

        if (!expiryMonthDigits) {
            errors.cardExpiryMonth = 'Expiry month is required';
        }

        if (!expiryYearDigits) {
            errors.cardExpiryYear = 'Expiry year is required';
        }

        const month = Number(expiryMonthDigits);
        const year = Number(expiryYearDigits);

        if (expiryMonthDigits && (Number.isNaN(month) || month < 1 || month > 12)) {
            errors.cardExpiryMonth = 'Month must be between 01 and 12';
        }

        if (expiryYearDigits && (Number.isNaN(year) || year < 0 || year > 99)) {
            errors.cardExpiryYear = 'Year must be two digits';
        }

        if (!errors.cardExpiryMonth && !errors.cardExpiryYear) {
            const now = new Date();
            const fullYear = 2000 + year;
            const expiryDate = new Date(fullYear, month, 0, 23, 59, 59, 999);
            if (expiryDate < now) {
                errors.cardExpiryMonth = 'Card has expired';
            }
        }

        if (Object.keys(errors).length > 0) {
            return { valid: false, errors };
        }

        return {
            valid: true,
            card: {
                id: `card_${Date.now()}_${Math.random().toString(36).slice(2, 9)}`,
                holderName,
                brand: detectCardBrand(cardNumberDigits),
                last4: cardNumberDigits.slice(-4),
                expiryMonth: String(month).padStart(2, '0'),
                expiryYear: String(year).padStart(2, '0'),
                isDefault: !!rawData.setDefault,
                createdAt: new Date().toISOString(),
            },
        };
    }

    function normalizeServerCards(cards) {
        if (!Array.isArray(cards)) return [];
        return cards
            .filter(card => card && typeof card === 'object' && card.id && card.last4)
            .map(card => ({
                id: String(card.id),
                holderName: String(card.holderName || ''),
                brand: String(card.brand || 'Card'),
                last4: String(card.last4 || '').slice(-4),
                expiryMonth: String(card.expiryMonth || '').padStart(2, '0').slice(-2),
                expiryYear: String(card.expiryYear || '').padStart(4, '0').slice(-4),
                isDefault: !!card.isDefault,
                createdAt: String(card.createdAt || ''),
            }));
    }

    function applySavedCards(cards) {
        savedCards = normalizeServerCards(cards);
        renderSavedCards();
    }

    function loadSavedCards() {
        return fetch(`${APP_ROOT}/buyerprofile/listCards`, {
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-cache',
        })
            .then(parseJsonResponse)
            .then(res => {
                if (res.success) {
                    applySavedCards(res.cards || []);
                    return true;
                }
                profileNotify(res.error || 'Unable to load saved cards', 'error');
                savedCards = [];
                renderSavedCards();
                return false;
            })
            .catch(err => {
                console.error('Load saved cards error:', err);
                profileNotify('Unable to load saved cards', 'error');
                savedCards = [];
                renderSavedCards();
                return false;
            });
    }

    function renderSavedCards() {
        const list = document.getElementById('savedCardsList');
        if (!list) return;
        const addBtn = document.getElementById('openAddCardFormBtn');
        if (addBtn) {
            addBtn.textContent = savedCards.length > 0 ? 'Add Another Card' : 'Add Card';
        }

        if (!savedCards.length) {
            list.innerHTML = `
                <div class="buyer-payment-empty">
                    <h4>No saved cards yet</h4>
                    <p>Add your first card now. You can save multiple cards.</p>
                </div>
            `;
            return;
        }

        list.innerHTML = savedCards.map(card => {
            const defaultBadge = card.isDefault ? '<span class="buyer-saved-card-default">Default</span>' : '';
            const actions = card.isDefault
                ? '<button type="button" class="btn btn-secondary" disabled>Default Card</button>'
                : `<button type="button" class="btn btn-secondary" data-card-action="default" data-card-id="${escapeHtml(card.id)}">Set Default</button>`;

            return `
                <article class="buyer-saved-card" data-card-id="${escapeHtml(card.id)}">
                    <div class="buyer-saved-card-main">
                        <div class="buyer-saved-card-top">
                            <h4>${escapeHtml(card.brand)} ending in ${escapeHtml(card.last4)}</h4>
                            ${defaultBadge}
                        </div>
                        <p>Card Holder: ${escapeHtml(card.holderName)}</p>
                        <p>Expires: ${escapeHtml(card.expiryMonth)}/${escapeHtml(card.expiryYear)}</p>
                    </div>
                    <div class="buyer-saved-card-actions">
                        ${actions}
                        <button type="button" class="btn btn-danger" data-card-action="remove" data-card-id="${escapeHtml(card.id)}">Remove</button>
                    </div>
                </article>
            `;
        }).join('');
    }

    function setDefaultCard(cardId) {
        fetch(`${APP_ROOT}/buyerprofile/setDefaultCard`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({ card_id: cardId }),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (res.success) {
                    applySavedCards(res.cards || []);
                    profileNotify(res.message || 'Default card updated', 'success');
                    return;
                }
                profileNotify(res.error || 'Failed to update default card', 'error');
            })
            .catch(err => {
                console.error('Set default card error:', err);
                profileNotify('Failed to update default card', 'error');
            });
    }

    function removeCard(cardId) {
        const card = savedCards.find(item => item.id === cardId);
        if (!card) return;

        const confirmed = confirm(`Remove ${card.brand} ending in ${card.last4}?`);
        if (!confirmed) return;

        fetch(`${APP_ROOT}/buyerprofile/removeCard`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({ card_id: cardId }),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (res.success) {
                    applySavedCards(res.cards || []);
                    profileNotify(res.message || 'Saved card removed', 'success');
                    return;
                }
                profileNotify(res.error || 'Failed to remove card', 'error');
            })
            .catch(err => {
                console.error('Remove card error:', err);
                profileNotify('Failed to remove card', 'error');
            });
    }

    function hideAddCardForm() {
        const form = document.getElementById('paymentCardForm');
        const toggleBtn = document.getElementById('openAddCardFormBtn');
        if (form) {
            form.reset();
            form.classList.add('is-hidden');
        }
        clearFormErrors(cardFieldIds);
        if (toggleBtn) toggleBtn.classList.remove('is-hidden');
    }

    function showAddCardForm() {
        const form = document.getElementById('paymentCardForm');
        const toggleBtn = document.getElementById('openAddCardFormBtn');
        if (!form) return;

        form.classList.remove('is-hidden');
        if (toggleBtn) toggleBtn.classList.add('is-hidden');

        const holderField = document.getElementById('cardHolderName');
        if (holderField) holderField.focus();
    }

    function saveCard(event) {
        event.preventDefault();

        clearFormErrors(cardFieldIds);

        const rawData = {
            holderName: document.getElementById('cardHolderName')?.value || '',
            cardNumber: document.getElementById('cardNumber')?.value || '',
            expiryMonth: document.getElementById('cardExpiryMonth')?.value || '',
            expiryYear: document.getElementById('cardExpiryYear')?.value || '',
            setDefault: !!document.getElementById('cardSetDefault')?.checked,
        };

        const validation = validateCardForm(rawData);
        if (!validation.valid) {
            Object.keys(validation.errors).forEach(fieldId => {
                setFieldError(fieldId, validation.errors[fieldId]);
            });

            const firstField = document.getElementById(Object.keys(validation.errors)[0]);
            if (firstField) firstField.focus();
            return;
        }

        const saveBtn = document.getElementById('saveCardBtn');
        const originalText = saveBtn ? saveBtn.textContent : '';
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
        }

        fetch(`${APP_ROOT}/buyerprofile/addCard`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({
                card_holder_name: validation.card.holderName,
                card_brand: validation.card.brand,
                card_last_four: validation.card.last4,
                expiry_month: validation.card.expiryMonth,
                expiry_year: validation.card.expiryYear,
                is_default: validation.card.isDefault ? '1' : '0',
            }),
        })
            .then(parseJsonResponse)
            .then(res => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = originalText;
                }

                if (res.success) {
                    applySavedCards(res.cards || []);
                    hideAddCardForm();
                    profileNotify(res.message || 'Card saved successfully', 'success');
                    return;
                }

                if (res.errors && typeof res.errors === 'object') {
                    applyErrorsFromMap(res.errors, cardServerFieldMap);
                    const firstServerKey = Object.keys(res.errors)[0];
                    const firstFieldId = cardServerFieldMap[firstServerKey];
                    const firstField = firstFieldId ? document.getElementById(firstFieldId) : null;
                    if (firstField) firstField.focus();
                    profileNotify('Please check card details and try again', 'error');
                    return;
                }

                profileNotify(res.error || 'Failed to save card', 'error');
            })
            .catch(err => {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = originalText;
                }
                console.error('Save card error:', err);
                profileNotify('Failed to save card', 'error');
            });
    }

    function openPaymentMethodsModal() {
        hideAddCardForm();
        openProfileModal('paymentMethodsModal');
        loadSavedCards();
    }

    function initializeShortcuts() {
        const shortcutCards = document.querySelectorAll('.profile-shortcut-card[data-open-modal]');
        shortcutCards.forEach(card => {
            card.addEventListener('click', function () {
                const target = this.getAttribute('data-open-modal');
                if (target === 'accountSettingsModal') {
                    openAccountSettingsModal();
                    return;
                }
                if (target === 'paymentMethodsModal') {
                    openPaymentMethodsModal();
                    return;
                }
                openProfileModal(target);
            });
        });

        const openButtons = document.querySelectorAll('[data-open-modal]');
        openButtons.forEach(btn => {
            if (btn.classList.contains('profile-shortcut-card')) return;
            btn.addEventListener('click', function () {
                const target = this.getAttribute('data-open-modal');
                if (target) openProfileModal(target);
            });
        });

        const closeButtons = document.querySelectorAll('[data-close-modal]');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const target = this.getAttribute('data-close-modal');
                if (target) closeProfileModal(target);
            });
        });

        document.querySelectorAll('.modal.profile-modal').forEach(modal => {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) modal.classList.remove('show');
            });
        });
    }

    function initializeProfileFieldValidation() {
        Object.keys(profileFieldMap).forEach(key => {
            const fieldId = profileFieldMap[key];
            const field = document.getElementById(fieldId);
            if (!field) return;

            const eventName = field.tagName === 'SELECT' ? 'change' : 'input';
            field.addEventListener(eventName, function () {
                clearFieldError(fieldId);
            });
        });

        const phoneField = document.getElementById('profilePhone');
        if (phoneField) {
            phoneField.addEventListener('input', function () {
                this.value = this.value.replace(/[^\d+\s-]/g, '').slice(0, 13);
                clearFieldError('profilePhone');
            });
        }

        const postalField = document.getElementById('profilePostalCode');
        if (postalField) {
            postalField.addEventListener('input', function () {
                this.value = onlyDigits(this.value).slice(0, 5);
                clearFieldError('profilePostalCode');
            });
        }
    }

    function initializeAccountSettingsValidation() {
        emailFieldIds.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field) return;

            field.addEventListener('input', function () {
                clearFieldError(fieldId);
                clearInlineStatus('emailChangeStatus');
                if (fieldId === 'newEmailAddress') {
                    this.value = this.value.trimStart();
                }
            });
        });

        passwordFieldIds.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field) return;

            field.addEventListener('input', function () {
                clearFieldError(fieldId);
                clearInlineStatus('passwordChangeStatus');
            });
        });

        const settingsToggles = document.querySelectorAll('.account-settings-toggle[data-settings-target]');
        settingsToggles.forEach(btn => {
            btn.addEventListener('click', function () {
                const panelId = this.getAttribute('data-settings-target');
                if (panelId) toggleSettingsPanel(panelId);
            });
        });
    }

    function initializePaymentMethods() {
        loadSavedCards();

        const cardNumber = document.getElementById('cardNumber');
        if (cardNumber) {
            cardNumber.addEventListener('input', function () {
                this.value = formatCardNumberInput(this.value);
                clearFieldError('cardNumber');
            });
        }

        const expiryMonth = document.getElementById('cardExpiryMonth');
        if (expiryMonth) {
            expiryMonth.addEventListener('input', function () {
                this.value = onlyDigits(this.value).slice(0, 2);
                clearFieldError('cardExpiryMonth');
            });
        }

        const expiryYear = document.getElementById('cardExpiryYear');
        if (expiryYear) {
            expiryYear.addEventListener('input', function () {
                this.value = onlyDigits(this.value).slice(0, 2);
                clearFieldError('cardExpiryYear');
            });
        }

        const holderName = document.getElementById('cardHolderName');
        if (holderName) {
            holderName.addEventListener('input', function () {
                clearFieldError('cardHolderName');
            });
        }

        const openAddCardFormBtn = document.getElementById('openAddCardFormBtn');
        if (openAddCardFormBtn) {
            openAddCardFormBtn.addEventListener('click', showAddCardForm);
        }

        const cancelAddCardBtn = document.getElementById('cancelAddCardBtn');
        if (cancelAddCardBtn) {
            cancelAddCardBtn.addEventListener('click', hideAddCardForm);
        }

        const paymentCardForm = document.getElementById('paymentCardForm');
        if (paymentCardForm) {
            paymentCardForm.addEventListener('submit', saveCard);
        }

        const savedCardsList = document.getElementById('savedCardsList');
        if (savedCardsList) {
            savedCardsList.addEventListener('click', function (event) {
                const button = event.target.closest('[data-card-action][data-card-id]');
                if (!button) return;

                const action = button.getAttribute('data-card-action');
                const cardId = button.getAttribute('data-card-id');
                if (!action || !cardId) return;

                if (action === 'default') {
                    setDefaultCard(cardId);
                    return;
                }

                if (action === 'remove') {
                    removeCard(cardId);
                }
            });
        }
    }

    function initializeProfilePage() {
        const profileForm = document.getElementById('profileForm');
        if (!profileForm) return;

        loadProfileData();
        initializeShortcuts();
        initializeProfileFieldValidation();
        initializeAccountSettingsValidation();
        initializePaymentMethods();

        const addPhotoBtn = document.getElementById('addPhotoBtn');
        const changePhotoBtn = document.getElementById('changePhotoBtn');
        const removePhotoBtn = document.getElementById('removePhotoBtn');
        if (addPhotoBtn) addPhotoBtn.addEventListener('click', triggerProfilePhotoUpload);
        if (changePhotoBtn) changePhotoBtn.addEventListener('click', triggerProfilePhotoUpload);
        if (removePhotoBtn) removePhotoBtn.addEventListener('click', removeProfilePhoto);

        const changeEmailForm = document.getElementById('changeEmailForm');
        if (changeEmailForm) {
            changeEmailForm.addEventListener('submit', changeEmail);
        }

        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', changePassword);
        }

        const deactivateBtn = document.getElementById('confirmDeactivateBtn');
        if (deactivateBtn) {
            deactivateBtn.addEventListener('click', confirmDeactivateAccount);
        }
    }

    window.BuyerProfile = {
        loadProfileData,
        saveProfileData,
        resetProfileForm,
        triggerProfilePhotoUpload,
        removeProfilePhoto,
        openAccountSettingsModal,
        openPaymentMethodsModal,
        setProfilePhoto,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProfilePage);
    } else {
        initializeProfilePage();
    }
})();
