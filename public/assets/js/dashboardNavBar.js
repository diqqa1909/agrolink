function getUserDropdown() {
    return document.getElementById('userDropdown');
}

function getUserSection() {
    return document.querySelector('.user-section');
}

function setToggleExpanded(isOpen) {
    const toggle = document.querySelector('.nav-user-toggle');
    if (toggle) {
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
}

// Navbar dropdown functionality
function toggleUserDropdown() {
    const dropdown = getUserDropdown();
    if (!dropdown) {
        return;
    }

    const isOpen = dropdown.classList.toggle('show');
    setToggleExpanded(isOpen);
}

// Close dropdown when clicking outside.
document.addEventListener('click', function(event) {
    const userSection = getUserSection();
    const dropdown = getUserDropdown();

    if (!userSection || !dropdown) {
        return;
    }

    if (!userSection.contains(event.target)) {
        dropdown.classList.remove('show');
        setToggleExpanded(false);
    }
});

// Close dropdown when pressing Escape.
document.addEventListener('keydown', function(event) {
    if (event.key !== 'Escape') {
        return;
    }

    const dropdown = getUserDropdown();
    if (dropdown) {
        dropdown.classList.remove('show');
        setToggleExpanded(false);
    }
});