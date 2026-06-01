<?php 

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);

$userId = $_SESSION['user']['id'];

$errors = [];

$pwSettings = $_SESSION['password_settings'] ?? [];
$requirements = [];
if (!empty($pwSettings['min_length'])) {
    $requirements[] = "At least {$pwSettings['min_length']} characters";
}
if (!empty($pwSettings['require_uppercase'])) {
    $requirements[] = 'At least one uppercase letter';
}
if (!empty($pwSettings['require_lowercase'])) {
    $requirements[] = 'At least one lowercase letter';
}
if (!empty($pwSettings['require_numbers'])) {
    $requirements[] = 'At least one number';
}
if (!empty($pwSettings['require_special'])) {
    $requirements[] = 'At least one special character';
}

$foldersql = "SELECT * FROM folders WHERE user_id = :user_id";
$folders = $db->query($foldersql, ['user_id' => $userId])->get();

view("passwords/create.view.php", [
    'errors' => $_SESSION['errors'] ?? [],
    'folders' => $folders,
    'requirements' => $requirements
]);