<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroLink - Farm to Market</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style1.css">
</head>

<body>
    <?php require 'components/navbar.view.php' ?>

    <!-- Hero Section -->
    <section id="home" class="hero" style="background: url('<?= ROOT ?>/assets/imgs/hero-farm3.jpg') center/cover no-repeat; min-height: 68vh; position: relative;">
        <!-- lighter overlay for readability -->
        <div class="hero-overlay" style="position:absolute; inset:0; background: linear-gradient(180deg, rgba(0,0,0,0.10) 0%, rgba(0,0,0,0.16) 40%, rgba(0,0,0,0.20) 100%);"></div>
        <div class="container" style="position: relative; z-index: 2; min-height: 68vh; display:flex; align-items:center; justify-content:center;">
            <div class="hero-content" style="max-width: 780px; color: #ffffff; text-shadow: 0 1px 2px rgba(0,0,0,0.2); text-align:center;">
                <h1 style="margin-bottom: 12px; color:#ffffff;">Trade Smarter. Deliver Faster.</h1>
                <p style="margin-bottom: 20px; font-size: 1.2rem; line-height: 1.7; color: rgba(255,255,255,0.92);">A centralized digital marketplace that connects farmers, buyers, and transporters to streamline agricultural trade.</p>
                <div class="hero-actions" style="display:flex; gap: 12px; flex-wrap: wrap; justify-content:center;">
                    <a href="<?= ROOT ?>/login" class="btn btn-primary btn-large">Login</a>
                    <a href="<?= ROOT ?>/register" class="btn btn-secondary btn-large">Register Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="about" class="section">
        <div class="container">
            <h2 class="section-title">How AgroLink Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>List & Browse Produce</h3>
                    <p>Farmers list fresh products; buyers browse regionally available produce with detailed information.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Place Orders & Schedule Delivery</h3>
                    <p>Buyers order directly from farmers; transporters coordinate and manage delivery logistics.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Rate & Review</h3>
                    <p>Build trust with a feedback-based ecosystem that ensures quality and reliability.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="features" class="section bg-light">
        <div class="container">
            <h2 class="section-title">Why Use AgroLink?</h2>
            <div class="grid grid-3">
                <div class="card">
                    <h3>Direct Farm-to-Buyer Sales</h3>
                    <p>Eliminate middlemen and boost farmer profits with direct connections between producers and buyers.</p>
                </div>
                <div class="card">
                    <h3>Transport Coordination</h3>
                    <p>Assign and track deliveries in one place with integrated logistics management.</p>
                </div>
                <div class="card">
                    <h3>Role-Based Dashboards</h3>
                    <p>Tailored tools and interfaces designed specifically for each user type and their needs.</p>
                </div>
                <div class="card">
                    <h3>Track Orders & Revenue</h3>
                    <p>Real-time updates and comprehensive analytics to monitor your business performance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- User Roles Section -->
    <section id="roles" class="section roles">
        <div class="container">
            <h2 class="section-title">Who is AgroLink for?</h2>
            <div class="grid grid-4">
                <div class="role-card">
                    <div class="role-icon">
                        <img src="<?= ROOT ?>/assets/imgs/farmer-icon.png" alt="Farmer" style="height: 48px; width: 48px;">
                    </div>
                    <h3>Farmers</h3>
                    <ul>
                        <li>Easy product listing & Updates</li>
                        <li>Get orders instantly</li>
                        <li>Track sales & payments</li>
                        <li>Connect directly with buyers</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon">
                        <img src="<?= ROOT ?>/assets/imgs/buyer-icon.png" alt="Buyer" style="height: 48px; width: 48px;">
                    </div>
                    <h3>Buyers</h3>
                    <ul>
                        <li>Browse fresh products</li>
                        <li>Secure online checkout</li>
                        <li>Track deliveries</li>
                        <li>Request specific crops</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon">
                        <img src="<?= ROOT ?>/assets/imgs/transporter-icon.png" alt="Transporter" style="height: 48px; width: 48px;">
                    </div>
                    <h3>Transporters</h3>
                    <ul>
                        <li>Accept & manage delivery tasks</li>
                        <li>Update delivery status</li>
                        <li>Earn more with reliable clients</li>
                    </ul>
                </div>
            </div>
            <div style="text-align:center; margin-top: 24px;">
                <a href="<?= ROOT ?>/register" class="btn btn-primary btn-large">Register Now</a>
            </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">What Our Users Say</h2>
            <div class="grid grid-2">
                <div class="card">
                    <p>"Finally, a platform that gives farmers full control over pricing! AgroLink has transformed how I sell my produce."</p>
                    <div class="mt-md">
                        <strong>Ranjith Fernando</strong><br>
                        <span class="text-muted">Farmer - Matale</span>
                    </div>
                </div>
                <div class="card">
                    <p>"Our restaurant sources reliably through AgroLink – no middlemen needed! Fresh produce delivered on time, every time."</p>
                    <div class="mt-md">
                        <strong>Duleeka Rathnayake</strong><br>
                        <span class="text-muted">Buyer - Colombo</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="section bg-light">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="grid grid-2">
                <div class="faq-item">
                    <div class="faq-question" style="cursor: pointer; padding: var(--spacing-md); background: var(--white); border-radius: var(--radius-md); margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-bold);">
                        Is AgroLink free to use?
                    </div>
                    <div class="faq-answer" style="display: none; padding: var(--spacing-md); background: var(--white); border-radius: var(--radius-md); border-top: 2px solid var(--primary-green);">
                        Yes, AgroLink is completely free for all registered users including farmers, buyers, and transporters.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question" style="cursor: pointer; padding: var(--spacing-md); background: var(--white); border-radius: var(--radius-md); margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-bold);">
                        Can I sign up as both a buyer and a farmer?
                    </div>
                    <div class="faq-answer" style="display: none; padding: var(--spacing-md); background: var(--white); border-radius: var(--radius-md); border-top: 2px solid var(--primary-green);">
                        Yes, you can register for multiple roles using separate registrations with different email addresses.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question" style="cursor: pointer; padding: var(--spacing-md); background: var(--white); border-radius: var(--radius-md); margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-bold);">
                        How do I track my deliveries?
                    </div>
                    <div class="faq-answer" style="display: none; padding: var(--spacing-md); background: var(--white); border-radius: var(--radius-md); border-top: 2px solid var(--primary-green);">
                        All users can track delivery status in real-time through their respective dashboards with updates from transporters.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <h2 class="section-title">Get in Touch</h2>
            <div class="grid grid-2">
                <div class="card">
                    <h3>Contact Information</h3>
                    <p><strong>Email:</strong> agrolink.lk@gmail.com</p>
                    <p><strong>Phone:</strong> +94 11 2559 259</p>
                    <p><strong>Address:</strong> UCSC Building Complex, Reid Avenue, Colombo 07,<br>Sri Lanka</p>
                </div>
                <div class="card">
                    <h3>Quick Contact</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="contactName">Name</label>
                            <input type="text" id="contactName" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="contactEmail">Email</label>
                            <input type="email" id="contactEmail" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="contactMessage">Message</label>
                            <textarea id="contactMessage" name="message" class="form-control" rows="4" required></textarea>
                        </div>
                        <div style="text-align: center;">
                            <button type="submit" class="btn btn-primary btn-large">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">
                        <img src="<?= ROOT ?>/assets/imgs/Logo 2.svg" alt="AgroLink" style="height: 50px;">
                    </div>
                    <p>Empowering agricultural trade across Sri Lanka through digital innovation.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="#home">Home</a>
                    <a href="#about">About</a>
                    <a href="#features">Features</a>
                    <a href="products.html">Browse Products</a>
                </div>
                <div class="footer-section">
                    <h3>Users</h3>
                    <a href="<?= ROOT ?>/register">Register as Farmer</a>
                    <a href="<?= ROOT ?>/register">Register as Buyer</a>
                    <a href="<?= ROOT ?>/register">Register as Transporter</a>
                    <a href="<?= ROOT ?>/login">Login</a>
                </div>
                <div class="footer-section">
                    <h3>Legal</h3>
                    <a href="#terms">Terms & Conditions</a>
                    <a href="#privacy">Privacy Policy</a>
                    <a href="#contact">Contact Us</a>
                    <a href="#support">Support</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 AgroLink - CS22 Project. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Registration Modal
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create AgroLink Account</h3>
                <button class="modal-close" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <p>Select your role to get started:</p>
                <div class="grid grid-3">
                    <a href="<?= ROOT ?>/register" class="card text-center">
                        <div class="card-icon">
                            <img src="<?= ROOT ?>/assets/imgs/farmer-icon.png" alt="Farmer" class="register-icon">
                        </div>
                        <h4>Farmer</h4>
                        <p>List and sell your produce</p>
                    </a>
                    <a href="<?= ROOT ?>/register" class="card text-center">
                        <div class="card-icon">
                            <img src="<?= ROOT ?>/assets/imgs/buyer-icon.png" alt="Buyer" class="register-icon">
                        </div>
                        <h4>Buyer</h4>
                        <p>Purchase fresh produce</p>
                    </a>
                    <a href="<?= ROOT ?>/register" class="card text-center">
                        <div class="card-icon">
                            <img src="<?= ROOT ?>/assets/imgs/transporter-icon.png" alt="Transporter" class="register-icon">
                        </div>
                        <h4>Transporter</h4>
                        <p>Provide delivery services</p>
                    </a>
                </div>
            </div>
        </div>
    </div>-->

    <script>
        window.APP_ROOT = "<?= ROOT ?>";
    </script>
    <script src="<?= ROOT ?>/assets/js/main.js"></script>
    <script src="<?= ROOT ?>/assets/js/home.js"></script>
    <script>
        // Contact form handling
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showNotification('Thank you for your message! We will get back to you soon.', 'success');
            this.reset();
        });
    </script>
</body>

</html>