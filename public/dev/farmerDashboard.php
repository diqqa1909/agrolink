<?php
session_start();

require __DIR__ . "/../../app/core/init.php";

// Optional: mock a logged-in farmer
// $_SESSION['USER'] = ['id'=>1, 'role'=>'farmer', 'name'=>'Dev Farmer'];

require __DIR__ . "/../../app/views/farmerDashboard.view.php";