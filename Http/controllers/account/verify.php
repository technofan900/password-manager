<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$password = $_POST['password'] ?? '';
$confirmation = $_POST['deactivate-confirm'] ?? '';
$userId = $_SESSION['user']['id'] ?? '';

$sql='SELECT * FROM login WHERE id = :id';
$user = $db->query($sql,[
    'id' => $userId
]);

if (password_verify($password, $user['password']) && ) {

}