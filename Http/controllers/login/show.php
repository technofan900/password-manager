<?php

$errors = $_SESSION['errors'] ?? \Core\Session::getFlash('errors') ?? [];;
$old = $_SESSION['old'] ?? [] ?? \Core\Session::getFlash('old') ?? [];

unset($_SESSION['errors'], $_SESSION['old']);

view('login/login.view.php', [
    'errors' => $errors,
    'old' => $old
]);   