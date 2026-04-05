<?php
// Fix directory permissions for image uploads
$dirs = [
    '/Applications/XAMPP/xamppfiles/htdocs/agrolink/public/assets/images/',
    '/Applications/XAMPP/xamppfiles/htdocs/agrolink/public/assets/images/transporter-profiles/',
    '/Applications/XAMPP/xamppfiles/htdocs/agrolink/public/assets/images/farmer-profiles/',
    '/Applications/XAMPP/xamppfiles/htdocs/agrolink/public/assets/images/products/',
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0777);
        echo "Set permissions for: $dir\n";
    } else {
        mkdir($dir, 0777, true);
        echo "Created and set permissions for: $dir\n";
    }
}

echo "\nDone! All directories are now writable.\n";
?>
