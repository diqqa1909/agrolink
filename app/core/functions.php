<?php

function redirect($path)
{
    header('Location: ' . ROOT . '/' . $path);
    exit();
}

function esc($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function show($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}