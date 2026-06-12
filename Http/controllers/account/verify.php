<?php

use Core\App;
use Core\Database;
use Core\Session;

$db = App::resolve(Database::class);

$password = trim($_POST['password'] ?? '');
$confirmation = $_POST['deactivate-confirm'] ?? '';
$userId = $_SESSION['user']['id'] ?? '';
$errors = [];

$sql='SELECT * FROM login WHERE id = :id';
$user = $db->query($sql,[
    'id' => $userId
])->find();

if (! $user) {
    logout();
    redirect('/');
}

$email = $user['email'];

if (!password_verify($password, $user['password'])) {
    $errors['password'] = 'Invalid password. Please enter a valid password.';
}

if (empty($confirmation)) {
    $errors['confirmation'] = 'Please confirm you want to deactivate account.';
}

if (! empty($errors)) {
    Session::flash('errors', $errors);
    redirect('/deactivate');
}

// Generate token and persist to storage.
$token = bin2hex(random_bytes(32));
$path = base_path('storage/account_deletions.json');
$data = [];
if (file_exists($path)) {
    $json = file_get_contents($path);
    $data = $json ? json_decode($json, true) : [];
    if (! is_array($data)) {
        $data = [];
    }
}

$expires = time() + 3600; // 1 hour
$data[$token] = [
    'user_id' => (int) $user['id'],
    'email' => $email,
    'expires_at' => $expires
];

$dir = dirname($path);
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}
file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

// Build account deactivation link.
$appUrl = getenv('APP_URL') ?: ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$link = rtrim($appUrl, '/') . '/deactivate/complete?token=' . urlencode($token);

// Send email using configured mailer or PHP mail as fallback.
$config = require base_path('config.php');
$mailConfig = $config['mail'] ?? [];
$subject = 'Confirm account deactivation';
$from = $mailConfig['from'] ?? 'no-reply@localhost';
$text = "You requested account deactivation. Click the link to confirm and continue: {$link}\n\nThis link expires in 1 hour. If you did not request this, ignore this message.";

try {
    $mailer = App::resolve('Symfony\\Component\\Mailer\\MailerInterface');
    $message = (new \Symfony\Component\Mime\Email())
        ->from($from)
        ->to($email)
        ->subject($subject)
        ->text($text);
    $mailer->send($message);
} catch (\Throwable $ex) {
    $headers = "From: {$from}\r\n";
    @mail($email, $subject, $text, $headers);
}

Session::flash('errors', ['success' => 'A confirmation link has been sent to your email address. Open it to finish deactivating your account.']);
redirect('/deactivate');
