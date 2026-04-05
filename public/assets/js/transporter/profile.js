// Transporter Profile Management
// Matched to transporter_profiles table: phone, company_name, license_number, vehicle_type, availability

// Ensure APP_ROOT is defined (set in transporterMain.view.php, fallback if missing)
if (typeof window.APP_ROOT === 'undefined' || !window.APP_ROOT) {
    window.APP_ROOT = 'http://localhost/agrolink/public';
    console.warn('APP_ROOT was undefined, using fallback:', window.APP_ROOT);
}

console.log('Profile.js loaded, APP_ROOT:', window.APP_ROOT);

function parseJsonResponse(response) {
    return response.text().then(text => {
        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid server response');
        }
    });
}

// Helper function to update profile photo display and default icon
function setProfilePhoto(url) {
    const img = document.getElementById('profilePhotoDisplay');
    const defaultIcon = document.getElementById('defaultProfileIcon');
    
    if (!img) return;
    
    if (url && url.length && url !== 'undefined') {
        img.style.display = 'block';
        if (defaultIcon) defaultIcon.style.display = 'none';
        img.src = url;
    } else {
        img.style.display = 'none';
        if (defaultIcon) defaultIcon.style.display = 'flex';
    }
}

// Store original profile data for reset functionality (only declare if not exists)
if (typeof originalProfileData === 'undefined') {
    var originalProfileData = null;
}

/**
 * Load profile data from server and populate form
 */
function loadProfileData() {
    const form = document.getElementById('profileForm');
    if (!form) {
        console.error('Profile form not found');
        return;
    }

    console.log('Loading transporter profile...');

    fetch(`${window.APP_ROOT}/transporterprofile?ajax=1&t=${Date.now()}`, {
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-cache'
    })
    .then(r => {
        if (!r.ok) throw new Error('Failed to fetch profile');
        return parseJsonResponse(r);
    })
    .then(res => {
        if (res.success && res.profile) {
            const profile = res.profile;
            
            console.log('Profile loaded:', profile);

            // Populate form fields
            const nameField = document.getElementById('profileName');
            const emailField = document.getElementById('profileEmail');
            const phoneField = document.getElementById('profilePhone');
            const districtField = document.getElementById('profileDistrict');
            const apartmentCodeField = document.getElementById('profileApartmentCode');
            const streetNameField = document.getElementById('profileStreetName');
            const cityField = document.getElementById('profileCity');
            const postalCodeField = document.getElementById('profilePostalCode');
            const fullAddressField = document.getElementById('profileFullAddress');
            const companyNameField = document.getElementById('profileCompanyName');
            const licenseNumberField = document.getElementById('profileLicenseNumber');
            const vehicleTypeField = document.getElementById('profileVehicleType');
            const availabilityField = document.getElementById('profileAvailability');
            
            if (nameField) nameField.value = profile.name || '';
            if (emailField) emailField.value = profile.email || '';
            if (phoneField) phoneField.value = profile.phone || '';
            if (districtField) districtField.value = profile.district || '';
            if (apartmentCodeField) apartmentCodeField.value = profile.apartment_code || '';
            if (streetNameField) streetNameField.value = profile.street_name || '';
            if (cityField) cityField.value = profile.city || '';
            if (postalCodeField) postalCodeField.value = profile.postal_code || '';
            if (fullAddressField) fullAddressField.value = profile.full_address || '';
            if (companyNameField) companyNameField.value = profile.company_name || '';
            if (licenseNumberField) licenseNumberField.value = profile.license_number || '';
            if (vehicleTypeField) vehicleTypeField.value = profile.vehicle_type || '';
            if (availabilityField) availabilityField.value = profile.availability || '';
            
            // Update header display (name and email in header section)
            const displayName = document.getElementById('profileDisplayName');
            const displayEmail = document.getElementById('profileDisplayEmail');
            if (displayName) displayName.textContent = profile.name || '';
            if (displayEmail) displayEmail.textContent = profile.email || '';
            
            // Store original data for reset
            if (!originalProfileData) {
                originalProfileData = { 
                    name: profile.name || '',
                    email: profile.email || '',
                    phone: profile.phone || '',
                    district: profile.district || '',
                    apartment_code: profile.apartment_code || '',
                    street_name: profile.street_name || '',
                    city: profile.city || '',
                    postal_code: profile.postal_code || '',
                    full_address: profile.full_address || '',
                    company_name: profile.company_name || '',
                    license_number: profile.license_number || '',
                    vehicle_type: profile.vehicle_type || '',
                    availability: profile.availability || ''
                };
            }
            
            // Set profile photo
            if (res.photoUrl) {
                setProfilePhoto(res.photoUrl);
            } else {
                setProfilePhoto(null);
            }
        } else {
            console.error('Invalid profile response:', res);
            showNotification(res.message || 'Failed to load profile', 'error');
        }
    })
    .catch(err => {
        console.error('Profile load error:', err);
        showNotification('Error loading profile data', 'error');
    });
}

/**
 * Save profile data to server
 */
function saveProfileData() {
    const form = document.getElementById('profileForm');
    if (!form) return;
    
    console.log('Saving transporter profile...');
    
    // Clear previous errors
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });

    // Collect form data
    const formData = new FormData();
    formData.append('name', document.getElementById('profileName')?.value?.trim() || '');
    formData.append('phone', document.getElementById('profilePhone')?.value?.trim() || '');
    formData.append('district', document.getElementById('profileDistrict')?.value?.trim() || '');
    formData.append('apartment_code', document.getElementById('profileApartmentCode')?.value?.trim() || '');
    formData.append('street_name', document.getElementById('profileStreetName')?.value?.trim() || '');
    formData.append('city', document.getElementById('profileCity')?.value?.trim() || '');
    formData.append('postal_code', document.getElementById('profilePostalCode')?.value?.trim() || '');
    formData.append('full_address', document.getElementById('profileFullAddress')?.value?.trim() || '');
    formData.append('company_name', document.getElementById('profileCompanyName')?.value?.trim() || '');
    formData.append('license_number', document.getElementById('profileLicenseNumber')?.value?.trim() || '');
    formData.append('vehicle_type', document.getElementById('profileVehicleType')?.value?.trim() || '');
    formData.append('availability', document.getElementById('profileAvailability')?.value?.trim() || '');

    // Get button and disable it
    const saveBtn = document.querySelector('.btn-save-profile');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Saving...';
    }

    fetch(`${window.APP_ROOT}/transporterprofile/saveProfile`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(parseJsonResponse)
    .then(res => {
        if (res.success) {
            console.log('Profile saved successfully');
            showNotification(res.message || 'Profile updated successfully', 'success');
            
            // Update original data
            originalProfileData = {
                name: formData.get('name'),
                email: document.getElementById('profileEmail')?.value || '',
                phone: formData.get('phone'),
                district: formData.get('district'),
                apartment_code: formData.get('apartment_code'),
                street_name: formData.get('street_name'),
                city: formData.get('city'),
                postal_code: formData.get('postal_code'),
                full_address: formData.get('full_address'),
                company_name: formData.get('company_name'),
                license_number: formData.get('license_number'),
                vehicle_type: formData.get('vehicle_type'),
                availability: formData.get('availability')
            };
            
            // Update display header
            const displayName = document.getElementById('profileDisplayName');
            if (displayName) displayName.textContent = formData.get('name');
        } else {
            console.error('Save profile failed:', res);
            
            // Display field-specific errors
            if (res.errors) {
                Object.keys(res.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = res.errors[field];
                        errorEl.classList.add('show');
                    }
                });
            }
            
            showNotification(res.message || 'Failed to update profile', 'error');
        }
    })
    .catch(err => {
        console.error('Profile save error:', err);
        showNotification('Error updating profile', 'error');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" /><polyline points="17 21 17 13 7 13 7 21" /><polyline points="7 3 7 8 15 8" /></svg> Save Changes';
        }
    });
}

/**
 * Reset profile form to original values
 */
function resetProfileForm() {
    if (!originalProfileData) {
        console.warn('No original profile data to reset');
        return;
    }
    
    const nameField = document.getElementById('profileName');
    const phoneField = document.getElementById('profilePhone');
    const districtField = document.getElementById('profileDistrict');
    const apartmentCodeField = document.getElementById('profileApartmentCode');
    const streetNameField = document.getElementById('profileStreetName');
    const cityField = document.getElementById('profileCity');
    const postalCodeField = document.getElementById('profilePostalCode');
    const fullAddressField = document.getElementById('profileFullAddress');
    const companyNameField = document.getElementById('profileCompanyName');
    const licenseNumberField = document.getElementById('profileLicenseNumber');
    const vehicleTypeField = document.getElementById('profileVehicleType');
    const availabilityField = document.getElementById('profileAvailability');
    
    if (nameField) nameField.value = originalProfileData.name || '';
    if (phoneField) phoneField.value = originalProfileData.phone || '';
    if (districtField) districtField.value = originalProfileData.district || '';
    if (apartmentCodeField) apartmentCodeField.value = originalProfileData.apartment_code || '';
    if (streetNameField) streetNameField.value = originalProfileData.street_name || '';
    if (cityField) cityField.value = originalProfileData.city || '';
    if (postalCodeField) postalCodeField.value = originalProfileData.postal_code || '';
    if (fullAddressField) fullAddressField.value = originalProfileData.full_address || '';
    if (companyNameField) companyNameField.value = originalProfileData.company_name || '';
    if (licenseNumberField) licenseNumberField.value = originalProfileData.license_number || '';
    if (vehicleTypeField) vehicleTypeField.value = originalProfileData.vehicle_type || '';
    if (availabilityField) availabilityField.value = originalProfileData.availability || '';
    
    // Clear errors
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
    
    showNotification('Form reset to original values', 'info');
}

/**
 * Upload profile photo
 */
function uploadProfilePhoto() {
    const fileInput = document.getElementById('photoFileInput');
    if (!fileInput || !fileInput.files || !fileInput.files[0]) {
        console.error('No file selected');
        return;
    }
    
    const file = fileInput.files[0];
    console.log('Uploading photo:', file.name, file.size, 'bytes');
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('File too large. Maximum size is 5MB', 'error');
        fileInput.value = '';
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Invalid file type. Use JPG, PNG, or WEBP', 'error');
        fileInput.value = '';
        return;
    }
    
    const formData = new FormData();
    formData.append('photo', file);
    
    // Show loading state
    const photoWrapper = document.getElementById('profilePhotoWrapper');
    if (photoWrapper) {
        photoWrapper.style.opacity = '0.6';
        photoWrapper.style.pointerEvents = 'none';
    }
    
    fetch(`${window.APP_ROOT}/transporterprofile/uploadPhoto`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(parseJsonResponse)
    .then(res => {
        if (res.success && res.photoUrl) {
            console.log('Photo uploaded:', res.photoUrl);
            setProfilePhoto(res.photoUrl);
            showNotification(res.message || 'Photo uploaded successfully', 'success');
        } else {
            console.error('Photo upload failed:', res);
            showNotification(res.message || 'Failed to upload photo', 'error');
        }
    })
    .catch(err => {
        console.error('Photo upload error:', err);
        showNotification('Error uploading photo', 'error');
    })
    .finally(() => {
        fileInput.value = '';
        if (photoWrapper) {
            photoWrapper.style.opacity = '1';
            photoWrapper.style.pointerEvents = 'auto';
        }
    });
}

/**
 * Remove profile photo
 */
function removeProfilePhoto() {
    if (!confirm('Are you sure you want to remove your profile photo?')) {
        return;
    }
    
    console.log('Removing profile photo...');
    
    fetch(`${window.APP_ROOT}/transporterprofile/removePhoto`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(parseJsonResponse)
    .then(res => {
        if (res.success) {
            console.log('Photo removed');
            setProfilePhoto(null);
            showNotification(res.message || 'Photo removed successfully', 'success');
        } else {
            console.error('Photo removal failed:', res);
            showNotification(res.message || 'Failed to remove photo', 'error');
        }
    })
    .catch(err => {
        console.error('Photo removal error:', err);
        showNotification('Error removing photo', 'error');
    });
}

/**
 * Open change password modal
 */
function openChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.style.display = 'flex';
        
        // Clear form and errors
        const form = document.getElementById('changePasswordForm');
        if (form) form.reset();
        
        const errorElements = modal.querySelectorAll('.error-message');
        errorElements.forEach(el => {
            el.textContent = '';
            el.classList.remove('show');
        });
    }
}

/**
 * Close change password modal
 */
function closeChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Submit password change
 */
function submitChangePassword() {
    const form = document.getElementById('changePasswordForm');
    if (!form) return;
    
    // Clear errors
    const errorElements = form.querySelectorAll('.error-message');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
    
    const currentPassword = document.getElementById('currentPassword')?.value || '';
    const newPassword = document.getElementById('newPassword')?.value || '';
    const confirmPassword = document.getElementById('confirmPassword')?.value || '';
    
    // Basic validation
    if (!currentPassword) {
        const errorEl = document.getElementById('error-current');
        if (errorEl) {
            errorEl.textContent = 'Current password is required';
            errorEl.classList.add('show');
        }
        return;
    }
    
    if (!newPassword || newPassword.length < 8) {
        const errorEl = document.getElementById('error-new');
        if (errorEl) {
            errorEl.textContent = 'New password must be at least 8 characters';
            errorEl.classList.add('show');
        }
        return;
    }
    
    if (newPassword !== confirmPassword) {
        const errorEl = document.getElementById('error-confirm');
        if (errorEl) {
            errorEl.textContent = 'Passwords do not match';
            errorEl.classList.add('show');
        }
        return;
    }
    
    const formData = new FormData();
    formData.append('current_password', currentPassword);
    formData.append('new_password', newPassword);
    formData.append('confirm_password', confirmPassword);
    
    fetch(`${window.APP_ROOT}/transporterprofile/changePassword`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(parseJsonResponse)
    .then(res => {
        if (res.success) {
            showNotification(res.message || 'Password changed successfully', 'success');
            closeChangePasswordModal();
            form.reset();
        } else {
            // Display field-specific errors
            if (res.errors) {
                Object.keys(res.errors).forEach(field => {
                    const errorEl = form.querySelector(`#error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = res.errors[field];
                        errorEl.classList.add('show');
                    }
                });
            } else {
                showNotification(res.message || 'Failed to change password', 'error');
            }
        }
    })
    .catch(err => {
        console.error('Password change error:', err);
        showNotification('Error changing password', 'error');
    });
}

// Namespaced API for inline handlers
window.TransporterProfile = {
    saveProfileData,
    resetProfileForm,
    openChangePasswordModal,
    closeChangePasswordModal,
    submitChangePassword,
    removeProfilePhoto,
    uploadProfilePhoto,
    loadProfileData,
    setProfilePhoto
};

// Backward-compatible aliases (temporary)
window.saveProfileData = window.TransporterProfile.saveProfileData;
window.resetProfileForm = window.TransporterProfile.resetProfileForm;
window.openChangePasswordModal = window.TransporterProfile.openChangePasswordModal;
window.closeChangePasswordModal = window.TransporterProfile.closeChangePasswordModal;
window.removeProfilePhoto = window.TransporterProfile.removeProfilePhoto;

// Event listeners setup
document.addEventListener('DOMContentLoaded', function() {
    console.log('Transporter profile page loaded, APP_ROOT:', window.APP_ROOT);
    
    // Load profile data on page load
    loadProfileData();
    
    // Photo upload button (edit button in overlay)
    const editPhotoBtn = document.getElementById('editPhotoBtn');
    if (editPhotoBtn) {
        editPhotoBtn.addEventListener('click', function() {
            const fileInput = document.getElementById('photoFileInput');
            if (fileInput) fileInput.click();
        });
    }
    
    // Photo delete button (delete button in overlay)
    const deletePhotoBtn = document.getElementById('deletePhotoBtn');
    if (deletePhotoBtn) {
        deletePhotoBtn.addEventListener('click', removeProfilePhoto);
    }
    
    // File input change handler
    const fileInput = document.getElementById('photoFileInput');
    if (fileInput) {
        fileInput.addEventListener('change', uploadProfilePhoto);
    }
    
    // Change password form submit handler
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitChangePassword();
        });
    }
    
    // Close modal on outside click
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeChangePasswordModal();
            }
        });
    }
});
