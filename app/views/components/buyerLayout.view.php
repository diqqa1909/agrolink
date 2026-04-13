<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Buyer Dashboard' ?> - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body data-app-root="<?= ROOT ?>" data-user-name="<?= htmlspecialchars($_SESSION['USER']->name ?? '') ?>" data-user-email="<?= htmlspecialchars($_SESSION['USER']->email ?? '') ?>">
    <script>
        window.APP_ROOT = "<?= ROOT ?>";
    </script>
    <?php
    $username = $_SESSION['USER']->name ?? 'Buyer';
    $role = $_SESSION['USER']->role ?? 'buyer';
    $cartItemCount = isset($cartItemCount) ? (int)$cartItemCount : 0;
    if (isset($_SESSION['USER']->id) && $role === 'buyer') {
        try {
            $cartModel = new CartModel();
            $cartItemCount = $cartModel->getCartItemCount((int)$_SESSION['USER']->id);
        } catch (Throwable $e) {
            // Keep the controller-provided value if DB refresh fails.
        }
    }

    $notificationUnreadCount = isset($notificationUnreadCount) ? (int)$notificationUnreadCount : null;
    if ($notificationUnreadCount === null && isset($_SESSION['USER']->id) && $role === 'buyer') {
        try {
            $notificationsModel = new BuyerNotificationsModel();
            $notificationUnreadCount = $notificationsModel->getUnreadCount((int)$_SESSION['USER']->id);
        } catch (Throwable $e) {
            $notificationUnreadCount = 0;
        }
    }
    if ($notificationUnreadCount === null) {
        $notificationUnreadCount = 0;
    }
    include '../app/views/components/dashboardNavBar.view.php';
    ?>

    <!-- Dashboard Layout -->
    <div class="dashboard">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= ROOT ?>/buyerdashboard" class="menu-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
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
                    <a href="<?= ROOT ?>/buyerproducts" class="menu-link <?= ($activePage ?? '') === 'products' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </div>
                        Products
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerorders" class="menu-link <?= ($activePage ?? '') === 'orders' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </div>
                        Orders
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyertracking" class="menu-link <?= ($activePage ?? '') === 'tracking' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        Tracking
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/wishlist" class="menu-link <?= ($activePage ?? '') === 'wishlist' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        Wishlist
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/cart" class="menu-link <?= ($activePage ?? '') === 'cart' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                        </div>
                        Cart
                        <span class="cart-badge" style="<?= $cartItemCount > 0 ? '' : 'display:none;' ?>"><?= $cartItemCount ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerreviews" class="menu-link <?= ($activePage ?? '') === 'reviews' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        Reviews
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/croprequest" class="menu-link <?= ($activePage ?? '') === 'requests' ? 'active' : '' ?>">
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
                    <a href="<?= ROOT ?>/buyernotifications" class="menu-link <?= ($activePage ?? '') === 'notifications' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                        </div>
                        <span class="menu-label-with-badge">
                            <span>Notifications</span>
                            <span id="buyerNotificationBadge" class="notification-sidebar-badge <?= $notificationUnreadCount > 0 ? '' : 'is-hidden' ?>"><?= $notificationUnreadCount ?></span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="<?= ROOT ?>/buyerprofile" class="menu-link <?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>">
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
            // Resolve and include the page-specific content
            if (isset($contentView)) {
                $viewToInclude = null;

                // Clean up the path - remove ../ prefix if present
                $cleanPath = $contentView;
                while (strpos($cleanPath, '../') === 0) {
                    $cleanPath = substr($cleanPath, 3);
                }

                // Try multiple candidate paths
                $candidates = [
                    // Absolute path (if full path given)
                    $contentView,
                    // Relative to components directory
                    __DIR__ . '/' . $contentView,
                    // Relative to views directory
                    __DIR__ . '/../' . $contentView,
                    // Just the clean path relative to views
                    __DIR__ . '/../' . $cleanPath,
                    // Relative to app directory
                    dirname(__DIR__) . '/' . $contentView,
                    // Root level
                    __DIR__ . '/../../' . $contentView,
                ];

                foreach ($candidates as $cand) {
                    $candNorm = str_replace('\\', '/', $cand);
                    if (file_exists($candNorm)) {
                        $viewToInclude = $candNorm;
                        break;
                    }
                }

                if ($viewToInclude) {
                    include $viewToInclude;
                } else {
                    echo '<div style="padding:20px;color:#c00;">View not found: ' . htmlspecialchars($contentView) . '</div>';
                }
            }
            ?>
        </main>
    </div>

    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/dashboardNavBar.js"></script>
    <?php if (isset($pageScript)): ?>
        <script src="<?= ROOT ?>/assets/js/buyer/<?= $pageScript ?>"></script>
    <?php endif; ?>
</body>

</html>
