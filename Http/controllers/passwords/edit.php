<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$userId = $_SESSION['user']['id'];

$sql = "SELECT * FROM passwords WHERE id = :id"; 
 $note = $db->query($sql, ['id' => $_GET['id']])->findOrFail();

// Decrypt password for edit form
if (! empty($note['password'])) {
    $dec = decrypt_string_from_storage($note['password']);
    $note['password'] = $dec === false ? '' : $dec;
}

// Load folders for the current user so the edit form can show the folder select.
$foldersql = "SELECT id, user_id, folder_name FROM folders WHERE user_id = :user_id ORDER BY id DESC";
$folders = $db->query($foldersql, ['user_id' => $userId])->get();

authorize ($note['userID'] == $userId);

view("passwords/editpassword.view.php", [
    'errors' => $_SESSION['errors'] ?? [],
    'note' => $note,
    'folders' => $folders
]);