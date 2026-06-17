<?php

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$errors = [];

$_SESSION['old'] = [
    'username' => $username,
    'email' => $email
];

if (! Validator::email($email)) {
    $errors['email'] = "Please provide a valid email address.";
}

if (! Validator::string($username, 4, 255)) {
    $errors['username'] = "Please provide a username between 4 and 255 characters.";
}

$passwordErrors = Validator::checkPasswordStrength($password);
if (! empty($passwordErrors)) {
    $errors['password'] = implode(' ', $passwordErrors);
}

if (! empty($errors)) {
    $_SESSION['errors'] = $errors;
    redirect('/register'); 
}

$user = $db->query('SELECT * FROM login WHERE email = :email', [
    'email' => $email
])->find();

if ($user) {
    $_SESSION['errors'] = [
        'email' => 'Email already registered. Please log in.'
    ];
    redirect('/login');
}

$db->query('INSERT INTO login (username, email, password) VALUES (:username, :email, :password)', [
	'username' => $username,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_BCRYPT)
]);

unset($_SESSION['old']);

redirect('/pop_up');
