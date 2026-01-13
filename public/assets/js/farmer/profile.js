// Farmer Profile Management

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

// Alias for backward compatibility
function updateProfilePhotoDisplay(photoUrl) {
    setProfilePhoto(photoUrl);
}

// Store original profile data for reset functionality
let originalProfileData = null;

/**
 * Load profile data from server and populate form
 */
function loadProfileData() {
    const form = document.getElementById('profileForm');
    if (!form) return;

    fetch(`${window.APP_ROOT}/farmerprofile?ajax=1&t=${Date.now()}`, {
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-cache'
    })
    .then(r => {
        if (!r.ok) throw new Error('Failed to fetch profile');
        return r.json();
    })
    .then(res => {
        if (res.success && res.profile) {
            const profile = res.profile;
            
            const nameField = document.getElementById('profileName');
            const emailField = document.getElementById('profileEmail');
            const phoneField = document.getElementById('profilePhone');
            const districtField = document.getElementById('profileDistrict');
            const cropsField = document.getElementById('profileCrops');
            const addressField = document.getElementById('profileAddress');
            
            if (nameField) nameField.value = profile.name || '';
            if (emailField) emailField.value = profile.email || '';
            if (phoneField) phoneField.value = profile.phone || '';
            if (districtField) districtField.value = profile.district || '';
            if (cropsField) cropsField.value = profile.crops_selling || '';
            if (addressField) addressField.value = profile.full_address || '';
            
            if (!originalProfileData) {
                originalProfileData = { ...profile };
            }
            
            if (res.photoUrl) {
                setProfilePhoto(res.photoUrl);
            } else {
                setProfilePhoto(null);
            }
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
    
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });

    const formData = {
        name: document.getElementById('profileName')?.value?.trim() || '',
        phone: document.getElementById('profilePhone')?.value?.trim() || '',
        district: document.getElementById('profileDistrict')?.value?.trim() || '',
        crops: document.getElementById('profileCrops')?.value?.trim() || '',
        address: document.getElementById('profileAddress')?.value?.trim() || ''
    };

    const saveBtn = document.getElementById('saveProfileBtn');
    const originalText = saveBtn ? saveBtn.textContent : '';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }

    fetch(`${window.APP_ROOT}/farmerprofile/update`, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(res => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
        }
        
        if (res.success) {
            showNotification('Profile updated successfully', 'success');
            originalProfileData = { ...formData };
            loadProfileData();
        } else {
            if (res.errors && typeof res.errors === 'object') {
                Object.keys(res.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = res.errors[field];
                        errorEl.classList.add('show');
                    }
                });
                showNotification('Please fix the errors below', 'error');
            } else {
                showNotification(res.error || 'Failed to update profile', 'error');
            }
        }
    })
    .catch(err => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
        }
        showNotification('Error saving profile', 'error');
        console.error('Profile save error:', err);
    });
}

/**
 * Reset form to original values (before any edits)
 */
function resetProfileForm() {
    if (!originalProfileData) {
        loadProfileData();
        return;
    }
    
    const nameField = document.getElementById('profileName');
    const emailField = document.getElementById('profileEmail');
    const phoneField = document.getElementById('profilePhone');
    const districtField = document.getElementById('profileDistrict');
    const cropsField = document.getElementById('profileCrops');
    const addressField = document.getElementById('profileAddress');
    
    if (nameField) nameField.value = originalProfileData.name;
    if (emailField) emailField.value = originalProfileData.email;
    if (phoneField) phoneField.value = originalProfileData.phone;
    if (districtField) districtField.value = originalProfileData.district;
    if (cropsField) cropsField.value = originalProfileData.crops_selling || originalProfileData.crops;
    if (addressField) addressField.value = originalProfileData.full_address || originalProfileData.address;
    
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
}

/**
 * Open change password modal
 */
function openChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    
    const errorElements = modal.querySelectorAll('.error-message');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
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
 * Change password
 */
function changePassword() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    const errorElements = document.querySelectorAll('#changePasswordModal .error-message');
    errorElements.forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });

    if (!currentPassword || !newPassword || !confirmPassword) {
        showNotification('All password fields are required', 'error');
        return;
    }

    if (newPassword !== confirmPassword) {
        const errorEl = document.getElementById('error-confirmPassword');
        if (errorEl) {
            errorEl.textContent = 'Passwords do not match';
            errorEl.classList.add('show');
        }
        showNotification('Passwords do not match', 'error');
        return;
    }

    if (newPassword.length < 6) {
        const errorEl = document.getElementById('error-newPassword');
        if (errorEl) {
            errorEl.textContent = 'Password must be at least 6 characters';
            errorEl.classList.add('show');
        }
        showNotification('Password must be at least 6 characters', 'error');
        return;
    }

    const changeBtn = document.getElementById('changePasswordBtn');
    const originalText = changeBtn ? changeBtn.textContent : '';
    if (changeBtn) {
        changeBtn.disabled = true;
        changeBtn.textContent = 'Changing...';
    }

    fetch(`${window.APP_ROOT}/farmerprofile/changePassword`, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword
        })
    })
    .then(r => r.json())
    .then(res => {
        if (changeBtn) {
            changeBtn.disabled = false;
            changeBtn.textContent = originalText;
        }
        
        if (res.success) {
            showNotification('Password changed successfully', 'success');
            closeChangePasswordModal();
        } else {
            if (res.errors && typeof res.errors === 'object') {
                Object.keys(res.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = res.errors[field];
                        errorEl.classList.add('show');
                    }
                });
            }
            showNotification(res.error || 'Failed to change password', 'error');
        }
    })
    .catch(err => {
        if (changeBtn) {
            changeBtn.disabled = false;
            changeBtn.textContent = originalText;
        }
        showNotification('Error changing password', 'error');
        console.error('Change password error:', err);
    });
}

/**
 * Trigger file input for profile photo upload (creates one if missing)
 */
function triggerProfilePhotoUpload() {
    let input = document.getElementById('profilePhotoInput');
    
    if (!input) {
        input = document.createElement('input');
        input.type = 'file';
        input.id = 'profilePhotoInput';
        input.accept = 'image/*';
        input.style.display = 'none';
        document.body.appendChild(input);

        input.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showNotification('Please select a valid image file (JPG, PNG, or WebP)', 'error');
                input.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showNotification('Image size must be less than 5MB', 'error');
                input.value = '';
                return;
            }

            uploadProfilePhoto(file);
        });
    }

    input.click();
}

/**
 * Upload profile photo to server
 */
function uploadProfilePhoto(file) {
    const formData = new FormData();
    formData.append('photo', file);

    fetch(`${window.APP_ROOT}/farmerprofile/uploadPhoto`, {
        method: 'POST',
        credentials: 'include',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.photoUrl) {
            setProfilePhoto(res.photoUrl + '?t=' + Date.now());
            showNotification('Profile photo updated successfully', 'success');
        } else {
            showNotification(res.error || 'Failed to upload photo', 'error');
        }
        const input = document.getElementById('profilePhotoInput');
        if (input) input.value = '';
    })
    .catch(err => {
        showNotification('Error uploading photo', 'error');
        console.error('Photo upload error:', err);
        const input = document.getElementById('profilePhotoInput');
        if (input) input.value = '';
    });
}

/**
 * Remove profile photo
 */
function removeProfilePhoto() {
    if (!confirm('Are you sure you want to remove your profile photo?')) {
        return;
    }

    fetch(`${window.APP_ROOT}/farmerprofile/removePhoto`, {
        method: 'POST',
        credentials: 'include'
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            setProfilePhoto(null);
            showNotification('Profile photo removed successfully', 'success');
        } else {
            showNotification(res.error || 'Failed to remove photo', 'error');
        }
    })
    .catch(err => {
        showNotification('Error removing photo', 'error');
        console.error('Photo remove error:', err);
    });
}

/**
 * Initialize photo button handlers
 */
function initializePhotoButtons() {
    const editBtn = document.getElementById('editPhotoBtn');
    const deleteBtn = document.getElementById('deletePhotoBtn');
    
    if (editBtn) {
        editBtn.addEventListener('click', function(e) {
            e.preventDefault();
            triggerProfilePhotoUpload();
        });
    }
    
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            removeProfilePhoto();
        });
    }
}

/**
 * Initialize profile page functionality
 */
function initializeProfileFunctionality() {
    loadProfileData();
    initializePhotoButtons();
    
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeChangePasswordModal();
            }
        });
    }
}

/**
 * Notification helper
 */
function showNotification(message, type = 'info') {
    console.log(`[${type.toUpperCase()}] ${message}`);
    
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        color: white;
        border-radius: 8px;
        z-index: 10001;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideInRight 0.3s ease;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Expose functions to window object
window.saveProfileData = saveProfileData;
window.resetProfileForm = resetProfileForm;
window.openChangePasswordModal = openChangePasswordModal;
window.closeChangePasswordModal = closeChangePasswordModal;
window.changePassword = changePassword;
window.loadProfileData = loadProfileData;
window.removeProfilePhoto = removeProfilePhoto;
window.triggerProfilePhotoUpload = triggerProfilePhotoUpload;
window.setProfilePhoto = setProfilePhoto;

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeProfileFunctionality);
} else {
    initializeProfileFunctionality();
}
