<?php

use Core\Authenticator;
use Core\Session;

$code = trim($_POST['code'] ?? '');

if ($code === '') {
    Session::flash('errors', ['code' => 'Please enter the authentication code.']);
    redirect('/login/2fa');
}

$auth = new Authenticator();
if ($auth->verifyTwoFactorCode($code)) {
    redirect('/');
}

Session::flash('errors', ['code' => 'Invalid or expired authentication code.']);
redirect('/login/2fa');
