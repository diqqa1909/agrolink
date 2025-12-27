<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Buyer Dashboard' ?> - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
</head>

<body>
    <?php
    $username = $_SESSION['USER']->name ?? 'Buyer';
    $role = $_SESSION['USER']->role ?? 'buyer';
    include '../app/views/components/dashboardNavBar.view.php';
    ?>

    <!-- Dashboard Layout -->
    <div class="dashboard">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="<?= ROOT ?>/buyerdashboard" class="menu-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        Dashboard
                    </a></li>
                <li><a href="<?= ROOT ?>/buyerorders" class="menu-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                            </svg>
                        </div>
                        Orders
                    </a></li>
                <li><a href="<?= ROOT ?>/buyerwishlist" class="menu-link <?= ($activePage ?? '') === 'wishlist' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                        </div>
                        Wishlist
                    </a></li>
                <li><a href="<?= ROOT ?>/buyerfeedback" class="menu-link <?= ($activePage ?? '') === 'feedback' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2z" />
                                <line x1="9" y1="10" x2="15" y2="10" />
                                <line x1="9" y1="14" x2="13" y2="14" />
                            </svg>
                        </div>
                        Reviews & Complaints
                    </a></li>
                <li><a href="<?= ROOT ?>/buyerprofile" class="menu-link <?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        Profile
                    </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php
            // Include the page-specific content
            if (isset($contentView)) {
                include $contentView;
            }
            ?>
        </main>
    </div>

    <!-- Make ROOT available to JS -->
    <script>
        window.APP_ROOT = "<?= ROOT ?>";
        window.USER_NAME = <?= json_encode($_SESSION['USER']->name ?? '') ?>;
        window.USER_EMAIL = <?= json_encode($_SESSION['USER']->email ?? '') ?>;
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/farmerDashboard.js"></script>
</body>

</html>