<?php

use Core\App;
use Core\Database;
use Core\Session;

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

$errors = [];

if ($password === '' || $password_confirm === '') {
    $errors['password'] = 'Please provide and confirm your new password.';
}

if ($password !== $password_confirm) {
    $errors['password'] = 'Passwords do not match.';
}

if (! empty($errors)) {
    Session::flash('errors', $errors);
    redirect('/recover/reset?token=' . urlencode($token));
}

$path = base_path('storage/password_resets.json');
if (! $token || ! file_exists($path)) {
    Session::flash('errors', ['token' => 'Invalid or expired reset token.']);
    redirect('/recover');
}

$json = file_get_contents($path);
$data = $json ? json_decode($json, true) : [];
if (! isset($data[$token]) || time() > $data[$token]['expires_at']) {
    Session::flash('errors', ['token' => 'Invalid or expired reset token.']);
    redirect('/recover');
}

$email = $data[$token]['email'];

$db = App::resolve(Database::class);
$db->query('UPDATE login SET password = :password WHERE email = :email', [
    'password' => password_hash($password, PASSWORD_BCRYPT),
    'email' => $email
]);

// remove token
unset($data[$token]);
file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

Session::flash('errors', ['success' => 'Your password has been reset. You can now log in.']);
redirect('/login');
