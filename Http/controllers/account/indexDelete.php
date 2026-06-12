<?php

use Core\Session;

$errors = Session::getFlash('errors') ?? [];

view('account/delete.view.php', [
    'errors' => $errors
]);
