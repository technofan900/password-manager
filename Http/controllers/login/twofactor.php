<?php

use Core\Session;
use Core\TwoFactor\TwoFactorService;

$errors = Session::getFlash('errors') ?? [];
$old = Session::getFlash('old') ?? [];

$userId = Session::get('user_id');
$email = Session::get('email');

$twoFactorService = new TwoFactorService();

// Ensure there is a pending two-factor user; otherwise redirect
if (! $twoFactorService->hasPendingUser()) {
    redirect('/login');
}

// Generate and send a code for the pending user (safe to call; generator handles storage)
if ($userId && $email) {
    $twoFactorService->generateEmailCodeForUser((int) $userId, $email);
}

view('login/twofactor.view.php', [
    'errors' => $errors,
    'old' => $old,
    'email' => $twoFactorService->getPendingEmail(),
]);
