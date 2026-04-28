<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$userId = $_SESSION['user']['id'];

$sql = "SELECT * FROM passwords WHERE id = :id"; 
 $note = $db->query($sql, ['id' => $_GET['id']])->findOrFail();

// Decrypt password for display
if (! empty($note['password'])) {
    $dec = decrypt_string_from_storage($note['password']);
    $note['password'] = $dec === false ? '' : $dec;
}


$foldersql = "SELECT COALESCE(f.folder_name, 'None') AS folder_name
               FROM passwords p
               LEFT JOIN folders f ON p.folder_id = f.id
               WHERE p.id = :id";
$folders = $db->query($foldersql, ['id' => $note['id']])->get();

// dd([
//   'session_user_id' => $_SESSION['user']['id'],
//   'note_user_id' => $note['user_id'],
//   'note_id' => $note['id']
// ]);

authorize($note['userID'] == $userId);

view("passwords/showpassword.view.php", [
    'note' => $note,
    'folders' => $folders
]);