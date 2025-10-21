<?php

spl_autoload_register(function($classname){
    $filename = "../app/models/".ucfirst($classname).".php";
    if (file_exists($filename)) {
        require $filename;
    } else {
        // Check if it's a Model suffix class
        $modelFilename = "../app/models/".ucfirst($classname)."Model.php";
        if (file_exists($modelFilename)) {
            require $modelFilename;
        }
    }
});

require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'app.php';