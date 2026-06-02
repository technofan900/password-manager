<?php

use Core\Validator;

$secSettings = $_SESSION['password_settings'] ?? '';

$name = $_GET['name'] ?? null;
$login_data = $_GET['login_data'] ?? null;
$password = $_GET['password'] ?? null;
$folder_select = $_GET['folder_select'] ?? null;

if ($name !== null || $login_data !== null || $password !== null || $folder_select !== null) {
    $_SESSION['old'] = [
        'name' => $name,
        'login_data' => $login_data,
        'password' => $password,
        'folder_select' => $folder_select,
    ];
}

$generated_password = randomPassword($secSettings);

$_SESSION['generated_password'] = $generated_password ?? '';
redirect('/passwords/create');