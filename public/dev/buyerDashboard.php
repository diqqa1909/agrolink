<?php
session_start();

require __DIR__ . "/../../app/core/init.php";

// Optional: mock a logged-in buyer
// $_SESSION['USER'] = ['id' => 2, 'role' => 'buyer', 'name' => 'Dev Buyer'];

// Update the view path/name if your file differs
require __DIR__ . "/../../app/views/buyerDashboard.view.php";