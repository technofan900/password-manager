<?php 

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);

$userId = $_SESSION['user']['id'];

$errors = [];

$foldersql = "SELECT * FROM folders WHERE user_id = :user_id";
$folders = $db->query($foldersql, ['user_id' => $userId])->get();

view("passwords/create.view.php", [
    'errors' => $_SESSION['errors'] ?? [],
    'folders' => $folders
]);