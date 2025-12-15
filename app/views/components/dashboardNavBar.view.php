<!-- Top Navigation Bar -->
<nav class="top-navbar">
    <div class="logo-section">
        <img src="<?= ROOT ?>/assets/imgs/Logo.png" alt="AgroLink">
    </div>
    <div class="user-section">
        <div>
            <div class="user-avatar" id="userAvatar">
                <?= strtoupper(substr($username ?? 'U', 0, 2)) ?>
            </div>
        </div>
        <div>
            <div class="user-name" id="adminName"><?= $username ?? 'User' ?></div>
            <div class="user-role"><?= ucfirst($role ?? 'User') ?></div>
        </div>
        <form method="POST" action="<?= ROOT ?>/logout" style="display: inline;">
            <button type="submit" class="logout-btn btn login-link">Logout</button>
        </form>
    </div>
</nav>