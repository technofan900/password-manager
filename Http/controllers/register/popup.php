<?php
use Core\Session;

$message = Session::getFlash('message') ?: 'Registration Success.';

view('register/popup.view.php', [
    'page' => $message
]);
