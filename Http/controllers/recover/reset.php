<?php

use Core\Session;

$token = $_GET['token'] ?? '';

$errors = Session::getFlash('errors') ?? [];

$path = base_path('storage/password_resets.json');
$valid = false;
$email = null;
if ($token && file_exists($path)) {
    $json = file_get_contents($path);
    $data = $json ? json_decode($json, true) : [];
    if (isset($data[$token]) && isset($data[$token]['expires_at']) && time() <= $data[$token]['expires_at']) {
        $valid = true;
        $email = $data[$token]['email'];
    }
}

if (! $valid) {
    // invalid or expired token
    Session::flash('errors', ['token' => 'The password reset link is invalid or has expired.']);
    redirect('/recover');
}

view('/recover/reset.view.php', [
    'token' => $token,
    'email' => $email,
    'errors' => $errors
]);
