<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Transporter Dashboard' ?> - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    $username = $_SESSION['USER']->name ?? 'Transporter';
    $role = $_SESSION['USER']->role ?? 'transporter';
    include '../app/views/components/dashboardNavBar.view.php';
    ?>

    <!-- Dashboard Layout -->
    <div class="dashboard">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= ROOT ?>/transporterdashboard" class="menu-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" data-section="dashboard">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/transporterdashboard?section=vehicle" class="menu-link <?= ($activePage ?? '') === 'vehicles' ? 'active' : '' ?>" data-section="vehicle">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8h-1V6c0-1.1-.9-2-2-2h-2c-1.1 0-2 .9-2 2v2H8V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v2H1v2h2v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V10h2V8zM4 6h2v2H4V6zm10 0h2v2h-2V6zM4 18v-6h14v6H4z"></path>
                            </svg>
                        </div>
                        Vehicles
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/transporterdashboard?section=available-deliveries" class="menu-link <?= ($activePage ?? '') === 'requests' ? 'active' : '' ?>" data-section="available-deliveries">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        Requests
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/transporterdashboard?section=mydeliveries" class="menu-link <?= ($activePage ?? '') === 'deliveries' ? 'active' : '' ?>" data-section="mydeliveries">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        Deliveries
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/transporterdashboard?section=earnings" class="menu-link <?= ($activePage ?? '') === 'earnings' ? 'active' : '' ?>" data-section="earnings">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="1"></circle>
                                <path d="M12 1v6m0 6v6M4.22 4.22l4.24 4.24m2.12 2.12l4.24 4.24M1 12h6m6 0h6M4.22 19.78l4.24-4.24m-2.12-2.12l-4.24-4.24M19.78 19.78l-4.24-4.24m-2.12-2.12l-4.24-4.24"></path>
                            </svg>
                        </div>
                        Earnings
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/transporterdashboard?section=feedback" class="menu-link <?= ($activePage ?? '') === 'notifications' ? 'active' : '' ?>" data-section="feedback">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        Reviews
                        <span class="badge">5</span>
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/transporterprofile" class="menu-link <?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        Profile
                    </a>
                </li>
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

    <script>
        window.APP_ROOT = "<?= ROOT ?>";
        window.USER_NAME = <?= json_encode($_SESSION['USER']->name ?? '') ?>;
        window.USER_EMAIL = <?= json_encode($_SESSION['USER']->email ?? '') ?>;
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <?php if (isset($pageScript)): ?>
        <script src="<?= ROOT ?>/assets/js/transporter/<?= $pageScript ?>"></script>
    <?php endif; ?>
    <script src="<?= ROOT ?>/assets/js/dashboardNavBar.js"></script>
</body>

</html>
