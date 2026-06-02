<?php


use Core\Validator;

$secSettings = $_SESSION['password_settings'] ?? '';

$generated_password = randomPassword($secSettings);

$_SESSION['generated_password'] = $generated_password ?? '';
// unset($_SESSION['generated_password'] );
redirect('/passwords/create');