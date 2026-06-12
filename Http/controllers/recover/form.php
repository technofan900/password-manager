<?php

use Core\Session;

$errors = Session::getFlash('errors') ?? [];
$old = Session::getFlash('old') ?? [];

view('/recover/recover.view.php', [
    'errors' => $errors,
    'old' => $old
]);
