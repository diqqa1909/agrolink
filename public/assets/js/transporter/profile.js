// Transporter profile management (uses TransporterProfileController endpoints)
let transporterOriginalProfile = null;

function avatarPlaceholder() {
    const name = (window.USER_NAME || 'Transporter').trim() || 'Transporter';
    const encoded = encodeURIComponent(name);
    return `https://ui-avatars.com/api/?name=${encoded}&background=10b981&color=fff&size=180`;
}

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

function populateProfileForm(profile, photoUrl) {
    const nameField = document.getElementById('profileName');
    const emailField = document.getElementById('profileEmail');
    const phoneField = document.getElementById('profilePhone');
    const districtField = document.getElementById('profileDistrict');
    const transporterTypeField = document.getElementById('profileTransporterType');
    const serviceAreasField = document.getElementById('profileServiceAreas');

    if (nameField) nameField.value = profile.name || '';
    if (emailField) emailField.value = profile.email || '';
    if (phoneField) phoneField.value = profile.phone || '';
    if (districtField) districtField.value = profile.district || '';
    if (transporterTypeField) transporterTypeField.value = profile.transporter_type || '';
    if (serviceAreasField) serviceAreasField.value = profile.service_areas || '';

    const displayName = document.getElementById('profileDisplayName');
    const displayEmail = document.getElementById('profileDisplayEmail');
    if (displayName) displayName.textContent = profile.name || '';
    if (displayEmail) displayEmail.textContent = profile.email || '';

    const resolvedPhoto = photoUrl || profile.profile_photo_url || profile.profile_photo;
    setProfilePhoto(resolvedPhoto);
    updateProfileStatistics(profile);

    transporterOriginalProfile = {
        name: profile.name || '',
        email: profile.email || '',
        phone: profile.phone || '',
        district: profile.district || '',
        transporter_type: profile.transporter_type || '',
        service_areas: profile.service_areas || '',
        profile_photo: resolvedPhoto
    };
}

function formatMonthYear(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr.replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleString('en-US', { month: 'short', year: 'numeric' });
}

function updateProfileStatistics(profile) {
    const memberSince = formatMonthYear(profile.created_at || profile.joined_at || '');
    const statMember = document.getElementById('statMemberSince');
    const statDeliveries = document.getElementById('statTotalDeliveries');
    const statRating = document.getElementById('statAverageRating');
    const statEarnings = document.getElementById('statTotalEarnings');

    if (statMember) statMember.textContent = memberSince;
    if (statDeliveries) statDeliveries.textContent = profile.total_deliveries ?? profile.deliveries_count ?? 0;
    if (statRating) statRating.textContent = (profile.average_rating ?? profile.rating ?? 0).toFixed(1);
    if (statEarnings) statEarnings.textContent = 'Rs. ' + (profile.total_earnings ?? 0).toLocaleString();
}

function clearFieldErrors(scope) {
    const selector = scope ? `${scope} .error-message` : '.error-message';
    document.querySelectorAll(selector).forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
}

function loadProfileData() {
    const url = `${window.APP_ROOT}/transporterprofile?ajax=1&t=${Date.now()}`;
    fetch(url, {
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-cache'
    })
        .then(r => {
            if (!r.ok) throw new Error('Failed to load profile');
            return r.json();
        })
        .then(res => {
            if (res.success && res.profile) {
                populateProfileForm(res.profile, res.photoUrl);
            } else {
                showNotification(res.error || 'Could not load profile', 'error');
            }
        })
        .catch(err => {
            console.error('Profile load error:', err);
            showNotification('Unable to load profile', 'error');
        });
}

function saveProfileData() {
    const form = document.getElementById('profileForm');
    if (!form) return;
    clearFieldErrors();

    const formData = new FormData(form);
    const data = {
        name: formData.get('name') || '',
        email: formData.get('email') || '',
        phone: formData.get('phone') || '',
        district: formData.get('district') || '',
        transporter_type: formData.get('transporter_type') || '',
        service_areas: formData.get('service_areas') || ''
    };

    const saveBtn = document.querySelector('.btn-save-profile');
    const originalText = saveBtn ? saveBtn.textContent : '';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }

    fetch(`${window.APP_ROOT}/transporterprofile/saveProfile`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
        .then(r => r.json())
        .then(res => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = originalText;
            }

            if (res.success) {
                showNotification(res.message || 'Profile updated', 'success');
                loadProfileData();
                return;
            }

            if (res.errors && typeof res.errors === 'object') {
                Object.keys(res.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = res.errors[field];
                        errorEl.classList.add('show');
                    }
                });
                showNotification('Please fix the highlighted fields', 'error');
            } else {
                showNotification(res.error || 'Failed to save profile', 'error');
            }
        })
        .catch(err => {
            console.error('Profile save error:', err);
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = originalText;
            }
            showNotification('Unable to save profile', 'error');
        });
}

function resetProfileForm() {
    clearFieldErrors();
    if (!transporterOriginalProfile) {
        loadProfileData();
        return;
    }
    populateProfileForm(transporterOriginalProfile, transporterOriginalProfile.profile_photo || null);
}

function triggerProfilePhotoUpload() {
    const inp = document.getElementById('profilePhotoInput');
    if (inp) inp.click();
}

function uploadProfilePhoto() {
    const inp = document.getElementById('profilePhotoInput');
    if (!inp || !inp.files || !inp.files.length) return;

    const file = inp.files[0];
    if (!file.type.startsWith('image/')) {
        showNotification('Please select an image file (jpg, png, etc.)', 'error');
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showNotification('File size must be less than 5MB', 'error');
        return;
    }

    const fd = new FormData();
    fd.append('photo', file);

    fetch(`${window.APP_ROOT}/transporterprofile/uploadPhoto`, {
        method: 'POST',
        credentials: 'include',
        body: fd
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showNotification('Photo uploaded', 'success');
                setProfilePhoto(res.url || res.photoUrl || res.photo_url);
            } else {
                showNotification(res.error || res.message || 'Photo upload failed', 'error');
            }
        })
        .catch(err => {
            console.error('Upload error:', err);
            showNotification('Unable to upload photo', 'error');
        })
        .finally(() => {
            inp.value = '';
        });
}

function removeProfilePhoto() {
    if (!confirm('Remove your profile photo?')) return;
    fetch(`${window.APP_ROOT}/transporterprofile/removePhoto`, {
        method: 'POST',
        credentials: 'include'
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showNotification('Photo removed', 'success');
                setProfilePhoto(null);
            } else {
                showNotification(res.error || 'Failed to remove photo', 'error');
            }
        })
        .catch(err => {
            console.error('Remove photo error:', err);
            showNotification('Unable to remove photo', 'error');
        });
}

function openChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) modal.style.display = 'flex';
    clearFieldErrors('#changePasswordModal');
}

function closeChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) modal.style.display = 'none';
    clearFieldErrors('#changePasswordModal');
    document.getElementById('changePasswordForm').reset();
}

function submitChangePassword() {
    clearFieldErrors('#changePasswordModal');
    const form = document.getElementById('changePasswordForm');
    if (!form) return;

    const fd = new FormData(form);
    const data = {
        current_password: fd.get('current_password') || '',
        new_password: fd.get('new_password') || '',
        confirm_password: fd.get('confirm_password') || ''
    };

    if (!data.current_password || !data.new_password || !data.confirm_password) {
        showNotification('All fields are required', 'error');
        return;
    }

    if (data.new_password.length < 6) {
        const errorEl = document.getElementById('error-new_password');
        if (errorEl) {
            errorEl.textContent = 'Password must be at least 6 characters';
            errorEl.classList.add('show');
        }
        return;
    }

    if (data.new_password !== data.confirm_password) {
        const errorEl = document.getElementById('error-confirm_password');
        if (errorEl) {
            errorEl.textContent = 'Passwords do not match';
            errorEl.classList.add('show');
        }
        return;
    }

    fetch(`${window.APP_ROOT}/transporterprofile/changePassword`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showNotification('Password changed successfully', 'success');
                closeChangePasswordModal();
            } else {
                if (res.field) {
                    const errorEl = document.getElementById(`error-${res.field}`);
                    if (errorEl) {
                        errorEl.textContent = res.message || res.error;
                        errorEl.classList.add('show');
                    }
                } else {
                    showNotification(res.message || res.error || 'Failed to change password', 'error');
                }
            }
        })
        .catch(err => {
            console.error('Change password error:', err);
            showNotification('Unable to change password', 'error');
        });
}

// Auto-load on page ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('profileForm')) {
        loadProfileData();
    }
});
