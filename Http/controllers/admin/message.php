<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

$message = trim($_POST['site_message'] ?? '');

if ($message === '') {
    clear_site_message();
    redirect('/admin');
}

save_site_message($message);
redirect('/admin');
