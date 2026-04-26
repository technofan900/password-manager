<?php
use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$userId = $_SESSION['user']['id'];

$sql = "SELECT * FROM passwords WHERE id = :id"; 
$note = $db->query($sql, ['id' => $_POST['id']])->findOrFail();

authorize($note['userID'] == $userId);

$attachment = $note['attachment'] ?? null;
if (! empty($attachment)) {
	$basename = basename($attachment);
	$filePath = base_path('storage/uploads/' . $basename);
	if (file_exists($filePath)) {
		@unlink($filePath);
	}
}

$sql ="DELETE FROM passwords WHERE id = :id";
$db->query($sql , ['id' => $_POST['id']]);
header('location: /passwords');
exit;
