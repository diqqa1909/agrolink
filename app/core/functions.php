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

function sessionValue($key, $default = null)
{
    return $_SESSION[$key] ?? $default;
}

function flash($key, $default = null)
{
    if (!isset($_SESSION[$key])) {
        return $default;
    }

    $value = $_SESSION[$key];
    unset($_SESSION[$key]);

    return $value;
}

function show($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function normalize_phone_number($value)
{
    return preg_replace('/\D+/', '', (string)$value);
}

function is_valid_phone_number($value)
{
    return (bool)preg_match('/^\d{10}$/', normalize_phone_number($value));
}

function normalize_vehicle_registration($value)
{
    $clean = strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string)$value));

    if (!preg_match('/^([A-Z]{0,3})(\d{0,4})$/', $clean, $matches)) {
        return $clean;
    }

    $letters = $matches[1] ?? '';
    $numbers = $matches[2] ?? '';

    if ($letters === '') {
        return $numbers;
    }

    return trim($letters . ($numbers !== '' ? ' ' . $numbers : ''));
}

function is_valid_vehicle_registration($value)
{
    return (bool)preg_match('/^[A-Z]{2,3} \d{4}$/', normalize_vehicle_registration($value));
}
