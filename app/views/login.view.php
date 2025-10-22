<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AgroLink</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
    <style>
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
        }
        
        .notification {
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .notification.error {
            background-color: #f44336;
            border-left: 4px solid #d32f2f;
        }
        
        .notification.success {
            background-color: #4CAF50;
            border-left: 4px solid #388e3c;
        }
        
        .notification.warning {
            background-color: #ff9800;
            border-left: 4px solid #f57c00;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin-left: 15px;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @keyframes slideIn {
            from { 
                transform: translateX(100%); 
                opacity: 0; 
            }
            to { 
                transform: translateX(0); 
                opacity: 1; 
            }
        }
        
        @keyframes slideOut {
            from { 
                transform: translateX(0); 
                opacity: 1; 
            }
            to { 
                transform: translateX(100%); 
                opacity: 0; 
            }
        }
        
        /* Remove the old alert styles */
        .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left: Quote & Image -->
        <div
            class="split-left"
            style="
                background: url('<?=ROOT?>/assets/imgs/registerpage/register4.jpg') center
                center/cover no-repeat;
            ">
            <span class="quote-icon">&ldquo;</span>
            <div class="split-left-content">
                <p>
                    Welcome back to AgroLink.<br />
                    Your gateway to Sri Lanka's agricultural marketplace.
                </p>
            </div>
        </div>

        <!-- Right: Login Form -->
        <div class="split-right">
            <div class="form-box">
                <h1>Welcome Back</h1>
                <div class="subtitle">
                    Sign in to access your dashboard
                </div>

                <form id="loginForm" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            required
                            placeholder="you@example.com"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                            placeholder="********" />
                    </div>

                    <div style="text-align: center;">
                        <button type="submit" class="btn btn-primary btn-large">
                            Sign In
                        </button>
                    </div>
                </form>

                <div class="text-center" style="margin-top: 1.5rem;">
                    Don't have an account?
                    <a href="<?=ROOT?>/register">Register here</a>
                </div>

                <div class="text-center" style="margin-top: 0.5rem;">
                    <a href="#forgot" class="text-muted">Forgot your password?</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer">
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="notification error">
                    <span><?= htmlspecialchars($error) ?></span>
                    <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endif ?>
    </div>

    <script src="../public/assets/js/login.js"></script>
</body>
</html>