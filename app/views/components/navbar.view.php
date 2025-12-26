<!-- Header -->
<header class="header">
    <nav class="nav container">
        <div class="logo">
            <img src="<?=ROOT?>/assets/imgs/Logo 2.svg" alt="AgroLink" style="height: 60px;">
        </div>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#features">Features</a>
            <a href="#roles">User Roles</a>
            <a href="#contact">Contact</a>
        </div>
        <div class="welcome-text">
            <?php 
            if(!empty($username)){?>
                <div class="nav-actions">
                    <?='Hi, '.$username?>
                    <!-- Logout Form -->
                    <form method="POST" action="<?=ROOT?>/logout" style="display: inline;">
                        <button type="submit" class="btn btn-secondary logout-link">Logout</button>
                    </form>
                </div>
            <?php } ?>
        </div>
        <?php if(empty($username)): ?>
            <div class="nav-actions">
                <a href="<?=ROOT?>/login" class="btn btn-secondary login-link">Login</a>
                <a href="<?=ROOT?>/register" class="btn btn-primary">Register</a>
            </div>
        <?php endif ?>
    </nav>
</header>