<?php
    if ($_SERVER['SERVER_NAME']=='localhost') {
        define ('ROOT', 'http://localhost/agrolinknew/public');

        define ('DBHOST', 'localhost');
        define ('DBNAME', 'agrolink');
        define ('DBUSER', 'root');
        define ('DBPASS', '');
        
    }else{
        define ('ROOT', 'https://www.website.com');

    }

    define('APP_NAME', "My Website");
    define('APP_DESC', "tHIS IS MY Website");
    
    //true shows errors
    define('DEBUG', true);