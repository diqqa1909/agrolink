<?php
    // Compute ROOT dynamically so asset URLs resolve correctly whether
    // using Apache (htdocs) or PHP built-in server (port 8000).
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
    // dirname of the script (e.g. /agrolink/public)
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $rootUrl = rtrim($protocol . '://' . $host . $scriptDir, '/');
    define('ROOT', $rootUrl);

    // Database defaults for local development (XAMPP)
    define ('DBHOST', 'localhost');
    define ('DBNAME', 'agrolink');
    define ('DBUSER', 'root');
    define ('DBPASS', '');

    define('APP_NAME', "My Website");
    define('APP_DESC', "tHIS IS MY Website");
    
    //true shows errors
    define('DEBUG', true);