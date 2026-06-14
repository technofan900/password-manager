<?php

use Core\App;
use Core\Database;
use Core\Session;

$db = App::resolve(Database::class);

$token = $_POST['token'] ?? '';

$path = base_path('storage/account_deletions.json');
if (! $token || ! file_exists($path)) {
    Session::flash('errors', ['token' => 'Invalid or expired deactivation token.']);
    redirect('/');
}

$json = file_get_contents($path);
$data = $json ? json_decode($json, true) : [];
if (! isset($data[$token]) || time() > $data[$token]['expires_at']) {
    Session::flash('errors', ['token' => 'Invalid or expired deactivation token.']);
    redirect('/');
}

$email = $data[$token]['email'];
$userId = (int) ($data[$token]['user_id'] ?? 0);

$user = $db->query('SELECT id, email FROM login WHERE id = :id AND email = :email', [
    'id' => $userId,
    'email' => $email
])->find();

if (! $user) {
    unset($data[$token]);
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    Session::flash('errors', ['token' => 'This account has already been deactivated.']);
    redirect('/');
}

$notes = $db->query('SELECT attachment FROM passwords WHERE userID = :user_id', [
    'user_id' => $userId
])->get();

foreach ($notes as $note) {
    $attachment = $note['attachment'] ?? null;
    if (! empty($attachment)) {
        $filePath = base_path('storage/uploads/' . basename($attachment));
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}

$db->query('DELETE FROM passwords WHERE userID = :user_id', [
    'user_id' => $userId
]);

$db->query('DELETE FROM folders WHERE user_id = :user_id', [
    'user_id' => $userId
]);

$passwordSettingsPath = base_path('storage/password_settings.json');
if (file_exists($passwordSettingsPath)) {
    $passwordSettingsJson = file_get_contents($passwordSettingsPath);
    $passwordSettings = $passwordSettingsJson ? json_decode($passwordSettingsJson, true) : [];

    if (is_array($passwordSettings) && isset($passwordSettings[(string) $userId])) {
        unset($passwordSettings[(string) $userId]);
        file_put_contents($passwordSettingsPath, json_encode($passwordSettings, JSON_PRETTY_PRINT), LOCK_EX);
    }
}

$db->query('DELETE FROM login WHERE id = :id AND email = :email', [
    'id' => $userId,
    'email' => $email
]);

unset($data[$token]);
file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

if (isset($_SESSION['user']['id']) && (int) $_SESSION['user']['id'] === $userId) {
    unset($_SESSION['user'], $_SESSION['_two_factor']);
    session_regenerate_id(true);
}

Session::flash('errors', ['success' => 'Your account and stored data have been deleted.']);
redirect('/');
