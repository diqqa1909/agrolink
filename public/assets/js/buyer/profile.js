// Buyer profile management (uses BuyerProfileController endpoints)
let buyerOriginalProfile = null;

function avatarPlaceholder() {
    const name = (window.USER_NAME || 'Buyer').trim() || 'Buyer';
    const encoded = encodeURIComponent(name);
    return `https://ui-avatars.com/api/?name=${encoded}&background=4CAF50&color=fff&size=180`;
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

function formatMonthYear(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr.replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleString('en-US', { month: 'short', year: 'numeric' });
}

function populateProfileForm(profile, photoUrl) {
    const nameField = document.getElementById('profileName');
    const emailField = document.getElementById('profileEmail');
    const phoneField = document.getElementById('profilePhone');
    const districtField = document.getElementById('profileDistrict');
    const apartmentField = document.getElementById('profileApartmentCode');
    const streetNameField = document.getElementById('profileStreetName');
    const cityField = document.getElementById('profileCity');
    const postalField = document.getElementById('profilePostalCode');
    const addressField = document.getElementById('profileAddress');

    if (nameField) nameField.value = profile.name || '';
    if (emailField) emailField.value = profile.email || '';
    if (phoneField) phoneField.value = profile.phone || '';
    if (districtField) districtField.value = profile.district || '';
    if (apartmentField) apartmentField.value = profile.apartment_code || '';
    if (streetNameField) streetNameField.value = profile.street_name || '';
    if (cityField) cityField.value = profile.city || '';
    if (postalField) postalField.value = profile.postal_code || '';
    if (addressField) addressField.value = profile.full_address || '';

    const displayName = document.getElementById('profileDisplayName');
    const displayEmail = document.getElementById('profileDisplayEmail');
    if (displayName) displayName.textContent = profile.name || '';
    if (displayEmail) displayEmail.textContent = profile.email || '';

    const resolvedPhoto = photoUrl || profile.profile_photo_url || profile.profile_photo;
    setProfilePhoto(resolvedPhoto);
    updateProfileStatistics(profile);

    buyerOriginalProfile = {
        name: profile.name || '',
        email: profile.email || '',
        phone: profile.phone || '',
        district: profile.district || '',
        apartment_code: profile.apartment_code || '',
        street_name: profile.street_name || '',
        city: profile.city || '',
        postal_code: profile.postal_code || '',
        full_address: profile.full_address || '',
        created_at: profile.created_at || '',
        orders_count: profile.total_orders ?? profile.orders_count ?? 0,
        wishlist_count: profile.wishlist_items ?? profile.wishlist_count ?? 0,
        reviews_count: profile.reviews_count ?? 0,
        profile_photo: resolvedPhoto
    };
}

function updateProfileStatistics(profile) {
    const memberSince = formatMonthYear(profile.created_at || profile.joined_at || '');
    const statMember = document.getElementById('statMemberSince');
    const statOrders = document.getElementById('statTotalOrders');
    const statWishlist = document.getElementById('statWishlistItems');
    const statReviews = document.getElementById('statReviewsGiven');

    if (statMember) statMember.textContent = memberSince;
    if (statOrders) statOrders.textContent = profile.total_orders ?? profile.orders_count ?? 0;
    if (statWishlist) statWishlist.textContent = profile.wishlist_items ?? profile.wishlist_count ?? 0;
    if (statReviews) statReviews.textContent = profile.reviews_count ?? 0;
}

function clearFieldErrors(scope) {
    const selector = scope ? `${scope} .error-message` : '.error-message';
    document.querySelectorAll(selector).forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
}

function loadProfileData() {
    const url = `${window.APP_ROOT}/buyerprofile?ajax=1&t=${Date.now()}`;
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
        apartment_code: formData.get('apartment_code') || '',
        street_name: formData.get('street_name') || '',
        city: formData.get('city') || '',
        postal_code: formData.get('postal_code') || '',
        additional_address_details: formData.get('additional_address_details') || ''
    };

    const saveBtn = document.querySelector('.btn-save-profile');
    const originalText = saveBtn ? saveBtn.textContent : '';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }

    fetch(`${window.APP_ROOT}/buyerprofile/saveProfile`, {
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
    if (!buyerOriginalProfile) {
        loadProfileData();
        return;
    }
    populateProfileForm(buyerOriginalProfile, buyerOriginalProfile.profile_photo || null);
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
    }
    input.onchange = () => {
        const file = input.files && input.files[0];
        if (file) uploadSelectedPhoto(file);
        input.value = '';
    };
    input.click();
}

function uploadSelectedPhoto(file) {
    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowed.includes(file.type)) {
        showNotification('Please select a JPG, PNG, or WEBP image', 'error');
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Image size must be under 5MB', 'error');
        return;
    }

    const fd = new FormData();
    fd.append('photo', file);

    fetch(`${window.APP_ROOT}/buyerprofile/uploadPhoto`, {
        method: 'POST',
        credentials: 'include',
        body: fd
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                setProfilePhoto(res.photoUrl || avatarPlaceholder());
                showNotification(res.message || 'Profile photo updated', 'success');
            } else {
                showNotification(res.error || 'Failed to upload photo', 'error');
            }
        })
        .catch(err => {
            console.error('Photo upload error:', err);
            showNotification('Unable to upload photo', 'error');
        });
}

function removeProfilePhoto() {
    fetch(`${window.APP_ROOT}/buyerprofile/removePhoto`, {
        method: 'POST',
        credentials: 'include'
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                setProfilePhoto(null);
                showNotification(res.message || 'Profile photo removed', 'success');
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
    clearFieldErrors('#changePasswordModal');
    const form = document.getElementById('changePasswordForm');
    if (form) form.reset();
    if (typeof openModal === 'function') {
        openModal('changePasswordModal');
    } else {
        const modal = document.getElementById('changePasswordModal');
        if (modal) modal.style.display = 'block';
    }
}

function closeChangePasswordModal() {
    if (typeof closeModal === 'function') {
        closeModal('changePasswordModal');
    } else {
        const modal = document.getElementById('changePasswordModal');
        if (modal) modal.style.display = 'none';
    }
}

function handleChangePasswordSubmit(event) {
    event.preventDefault();
    clearFieldErrors('#changePasswordModal');

    const form = event.target;
    const formData = new FormData(form);
    const data = {
        currentPassword: formData.get('currentPassword') || '',
        newPassword: formData.get('newPassword') || '',
        confirmPassword: formData.get('confirmPassword') || ''
    };

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.textContent : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';
    }

    fetch(`${window.APP_ROOT}/buyerprofile/changePassword`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data)
    })
        .then(r => r.json())
        .then(res => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }

            if (res.success) {
                showNotification(res.message || 'Password updated', 'success');
                closeChangePasswordModal();
                form.reset();
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
                showNotification(res.error || 'Failed to update password', 'error');
            }
        })
        .catch(err => {
            console.error('Password change error:', err);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
            showNotification('Unable to update password', 'error');
        });
}

document.addEventListener('DOMContentLoaded', () => {
    loadProfileData();
    const passwordForm = document.getElementById('changePasswordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', handleChangePasswordSubmit);
    }
    
    // Initialize profile photo upload and hover functionality
    if (document.getElementById('profilePhotoWrapper')) {
        const photoDisplay = document.getElementById('profilePhotoDisplay');
        const photoOverlay = document.getElementById('photoOverlay');
        const photoWrapper = document.getElementById('profilePhotoWrapper');
        
        if (photoDisplay && photoOverlay && photoWrapper) {
            const showOverlay = () => {
                photoOverlay.style.opacity = '1';
            };
            const hideOverlay = () => {
                photoOverlay.style.opacity = '0';
            };
            
            photoWrapper.addEventListener('mouseenter', showOverlay);
            photoWrapper.addEventListener('mouseleave', hideOverlay);
            photoOverlay.addEventListener('mouseenter', showOverlay);
            photoOverlay.addEventListener('mouseleave', hideOverlay);
        }
        
        // Create hidden file input for photo upload
        const profilePhotoInput = document.createElement('input');
        profilePhotoInput.type = 'file';
        profilePhotoInput.accept = 'image/*';
        profilePhotoInput.id = 'profilePhotoInput';
        profilePhotoInput.style.display = 'none';
        document.body.appendChild(profilePhotoInput);
        
        profilePhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                uploadSelectedPhoto(file);
            }
        });
    }
});

// Expose handlers for inline buttons
window.saveProfileData = saveProfileData;
window.resetProfileForm = resetProfileForm;
window.triggerProfilePhotoUpload = triggerProfilePhotoUpload;
window.removeProfilePhoto = removeProfilePhoto;
window.openChangePasswordModal = openChangePasswordModal;
window.closeChangePasswordModal = closeChangePasswordModal;
