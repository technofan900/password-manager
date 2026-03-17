<?php

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);

$userID = $_SESSION['user']['id'];

$errors = [];

$name = $_POST['name'] ?? '';
$login_data = $_POST['login_data'] ?? '';
$password = $_POST['password'] ?? '';
$folder = $_POST['folder_select'] ?? '';

if ($folder === ' ') {
    $folder = null; // Convert empty string to NULL
}

$body_min_ln = 3;
$body_max_ln = 256;

if (! Validator::string($name, $body_min_ln, $body_max_ln)) {
    $errors['body'] = "The body must be between {$body_min_ln} and {$body_max_ln} characters";
}

if (! empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = [
        'name' => $name,
        'login_data' => $login_data,
        'password' => $password
    ];

    redirect('/passwords/create');
}

$sql = "INSERT INTO passwords (name, login_data, password, userID, folder_id) VALUES (:name, :login_data, :password, :userID, :folder_id)";
$db->query($sql, [
    'name' => $name,
    'login_data' => $login_data,
    'password' => $password,
    'userID' => $userID,
    'folder_id' => $folder
]);

$_SESSION['success'] = 'Note created.';

redirect('/passwords');
