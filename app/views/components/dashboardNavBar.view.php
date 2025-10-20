<nav class="top-navbar">
    <div class="logo-section">
        <img src="<?= ROOT ?>/assets/imgs/Logo 2.svg" alt="AgroLink">
    </div>
    <div class="user-section">
        <div class="user-info">
            <div class="user-avatar" id="userAvatar" onclick="toggleUserDropdown()" style="cursor: pointer;" title="User Menu">
                <?= strtoupper(substr($username ?? 'U', 0, 2)) ?>
            </div>
            <div class="user-details">
                <div class="user-name" id="userName"><?= $username ?? 'User' ?></div>
                <div class="user-role"><?= ucfirst($role ?? 'User') ?></div>
            </div>
        </div>
        
        <!-- User Dropdown Menu -->
        <div class="user-dropdown" id="userDropdown">
            <a href="#profile" class="dropdown-item" onclick="showSection('profile')">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke-width="2"/>
                </svg>
                Profile
            </a>
            <a href="#settings" class="dropdown-item" onclick="showSection('settings')">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none">
                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                    <path d="M12 1v6m0 6v6M1 12h6m6 0h6" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Settings
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?= ROOT ?>/logout" class="dropdown-item logout-item">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
</nav>