<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$id = $_GET['id'] ?? null;
if (! $id) {
    abort();
}

$note = $db->query('SELECT * FROM passwords WHERE id = :id', ['id' => $id])->find();
if (! $note) {
    abort();
}

// Ensure owner
if (! isset($_SESSION['user']['id']) || (int)$_SESSION['user']['id'] !== (int)$note['userID']) {
    abort(Core\Responses::FORBIDDEN);
}

$attachment = $note['attachment'] ?? null;
if (! $attachment) {
    abort();
}

$basename = basename($attachment);
$filePath = base_path('storage/uploads/' . $basename);

if (! file_exists($filePath)) {
    abort();
}

// decrypt to temp file to determine mime and to stream decrypted content
$tempPath = decrypt_file_to_temp($filePath);
if (! $tempPath) {
    abort();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $tempPath);
finfo_close($finfo);

$download = isset($_GET['download']);
$inline = isset($_GET['inline']);

$inlineTypes = ['image/png', 'application/pdf', 'text/plain'];

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($tempPath));

if ($download) {
    header('Content-Disposition: attachment; filename="' . $basename . '"');
} else {
    if (in_array($mime, $inlineTypes) || $inline) {
        header('Content-Disposition: inline; filename="' . $basename . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $basename . '"');
    }
}

readfile($tempPath);
@unlink($tempPath);
exit;
