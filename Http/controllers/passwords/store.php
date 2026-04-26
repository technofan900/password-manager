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

// attachment will be set after validation if upload present
$attachment = null;

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

// Now handle file upload (after validation)
if (isset($_FILES['attachment']) && ($_FILES['attachment']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['attachment'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['errors'] = ['attachment' => 'File upload error.'];
        $_SESSION['old'] = ['name' => $name, 'login_data' => $login_data, 'password' => $password];
        redirect('/passwords/create');
    }

    // simple size limit 5MB
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $_SESSION['errors'] = ['attachment' => 'File exceeds maximum size of 5MB.'];
        $_SESSION['old'] = ['name' => $name, 'login_data' => $login_data, 'password' => $password];
        redirect('/passwords/create');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = [
        'application/pdf' => 'pdf',
        'text/plain' => 'txt',
        'image/png' => 'png'
    ];

    if (! array_key_exists($mime, $allowed)) {
        $_SESSION['errors'] = ['attachment' => 'Only PDF, TXT, PNG files are allowed.'];
        $_SESSION['old'] = ['name' => $name, 'login_data' => $login_data, 'password' => $password];
        redirect('/passwords/create');
    }

    $ext = $allowed[$mime];
    $uploadsDir = dirname(__DIR__, 3) . '/storage/uploads';
    if (! is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
    $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
    $newName = $baseName . '_' . time() . '.' . $ext;
    $destination = $uploadsDir . '/' . $newName;

    if (! move_uploaded_file($file['tmp_name'], $destination)) {
        $_SESSION['errors'] = ['attachment' => 'Failed to save uploaded file.'];
        $_SESSION['old'] = ['name' => $name, 'login_data' => $login_data, 'password' => $password];
        redirect('/passwords/create');
    }

    $attachment = $newName; // store only filename
}

$sql = "INSERT INTO passwords (name, login_data, password, userID, folder_id, attachment) VALUES (:name, :login_data, :password, :userID, :folder_id, :attachment)";
$db->query($sql, [
    'name' => $name,
    'login_data' => $login_data,
    'password' => $password,
    'userID' => $userID,
    'folder_id' => $folder,
    'attachment' => $attachment
]);

$_SESSION['success'] = 'Note created.';

redirect('/passwords');
