<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - AgroLink</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style1.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
</head>

<body>
    <div class="split-container">
        <!-- Left: Quote & Image -->
        <div
            class="split-left"
            style="
                background: url('<?= ROOT ?>/assets/imgs/registerpage/register4.jpg') center
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
                <?php if (!empty($errors)): ?>
                    <div class="alert">
                        <?= implode("<br>", $errors) ?>
                    </div>
                <?php endif ?>

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
                            placeholder="you@example.com" />
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
                    <a href="<?= ROOT ?>/register">Register here</a>
                </div>

                <div class="text-center" style="margin-top: 0.5rem;">
                    <a href="#forgot" class="text-muted">Forgot your password?</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.APP_ROOT = "<?= ROOT ?>";
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <!-- <script src="<?= ROOT ?>/assets/js/auth.js"></script> -->
</body>

</html>