<?php

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old']);

view('register/register.view.php', [
    'errors' => $errors,
    'old' => $old
]);
