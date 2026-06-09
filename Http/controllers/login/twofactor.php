<?php

use Core\Session;
use Core\TwoFactor\TwoFactorService;

$errors = Session::getFlash('errors') ?? [];
$old = Session::getFlash('old') ?? [];

$twoFactorService = new TwoFactorService();
if (! $twoFactorService->hasPendingUser()) {
    redirect('/login');
}

view('login/twofactor.view.php', [
    'errors' => $errors,
    'old' => $old,
    'email' => $twoFactorService->getPendingEmail(),
]);
