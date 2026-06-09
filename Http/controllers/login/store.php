<?php
use Core\Authenticator;
use Http\Forms\LoginForms;
use Core\Session;

$login = $_POST['login'];
$password = $_POST['password'];

$form = new LoginForms();

if ($form->validate($login, $password)) {

    $auth = new Authenticator();
    $result = $auth->attempt($login, $password);

    if ($result === '2fa_required') {
        redirect('/login/2fa');
    }

    if ($result === true) {
        redirect('/');
    }

    $form->setError('login', 'Email/username or password incorrect!');
}

Session::flash('errors', $form->errors());
redirect('/login');

