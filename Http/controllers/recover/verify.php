<?php

use Core\App;
use Core\Database;
use Core\Session;

$email = trim($_POST['email'] ?? '');

$old = ['email' => $email];
$errors = [];

if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$errors['email'] = 'Please provide a valid email address.';
}

if (! empty($errors)) {
	Session::flash('errors', $errors);
	Session::flash('old', $old);
	redirect('/recover');
}

$db = App::resolve(Database::class);
$user = $db->query('SELECT * FROM login WHERE email = :email', ['email' => $email])->find();

// Always show a generic message to avoid revealing whether the email exists.
Session::flash('errors', []);

if ($user) {
	// generate token and persist to storage
	$token = bin2hex(random_bytes(16));
	$path = base_path('storage/password_resets.json');
	$data = [];
	if (file_exists($path)) {
		$json = file_get_contents($path);
		$data = $json ? json_decode($json, true) : [];
		if (! is_array($data)) $data = [];
	}

	$expires = time() + 3600; // 1 hour
	$data[$token] = [
		'email' => $email,
		'expires_at' => $expires
	];

	$dir = dirname($path);
	if (! is_dir($dir)) mkdir($dir, 0755, true);
	file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

	// build reset link
	$appUrl = getenv('APP_URL') ?: ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
	$link = rtrim($appUrl, '/') . '/recover/reset?token=' . $token;

	// send email using configured mailer or PHP mail as fallback
	$config = require base_path('config.php');
	$mailConfig = $config['mail'] ?? [];
	$subject = $mailConfig['subject'] ?? 'Reset your password';
	$from = $mailConfig['from'] ?? 'no-reply@localhost';
	$text = "You requested a password reset. Click the link to reset your password: {$link}\n\nIf you did not request this, ignore this message.";

	try {
		$mailer = App::resolve('Symfony\\Component\\Mailer\\MailerInterface');
		$message = (new \Symfony\Component\Mime\Email())
			->from($from)
			->to($email)
			->subject($subject)
			->text($text);
		$mailer->send($message);
	} catch (\Throwable $ex) {
		// fallback to PHP mail
		$headers = "From: {$from}\r\n";
		@mail($email, $subject, $text, $headers);
	}
}

// Redirect to login with a generic notice
Session::flash('errors', ['success' => 'If an account exists for that email, a reset link has been sent.']);
redirect('/login');

