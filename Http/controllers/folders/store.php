<?php

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);

$userID = $_SESSION['user']['id'];

$errors = [];

$folder_name = $_POST['folder_name'] ?? '';

$body_min_ln = 1;
$body_max_ln = 256;

if (! Validator::string($folder_name, $body_min_ln, $body_max_ln)) {
    $errors['folder_name'] = "The name must be between {$body_min_ln} and {$body_max_ln} characters";
}

if (! empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = [
        'folder_name' => $folder_name
    ];

    redirect('/folders');
}

$sql = "INSERT INTO folders (user_id, folder_name) VALUES (:user_id, :folder_name)";
$db->query($sql, [
    'user_id' => $userID,
    'folder_name' => $folder_name
]);

// $_SESSION['success'] = 'Folder created.';

redirect('/passwords');