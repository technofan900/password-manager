<?php

// Base Pages
$router->get('/', 'index.php');
$router->get('/about', 'about.php');

// Login
$router->get('/login', 'login/show.php')->only('guest');
$router->post('/login', 'login/store.php')->only('guest');
$router->delete('/login', 'login/destroy.php')->only('auth');

// Register page
$router->get('/register', 'register/show.php')->only('guest');
$router->post('/register', 'register/create.php')->only('guest');
//register pop up
$router->get("/pop_up", 'register/popup.php')->only('auth');

// Settings page
if (isset($_SESSION['user'])) {
    $auth = 'auth';
} else {
    $auth = 'guest';
}
$router->get('/settings', 'settings/index.php')->only($auth);
$router->post('/set-setting', 'settings/set.php')->only('auth');

// Passwords page
$router->get("/passwords", "passwords/index.php")->only('auth');
$router->get("/passwords/create", "passwords/create.php")->only('auth');
// Handle creating a new password entry
$router->post('/passwords', 'passwords/store.php')->only('auth');
// Handle updating an existing password entry (form uses method override)
$router->patch('/passwords', 'passwords/update.php')->only('auth');
// Goes to popup that deletes note
$router->get('/password/popup', 'passwords/popup.php')->only('auth');
// Deletes note (form uses _method override)
$router->delete('/passwords', 'passwords/destroy.php')->only('auth');
$router->get("/password", "passwords/show.php")->only('auth');
$router->get("/password/edit", "passwords/edit.php")->only('auth');

// Secure attachment serving
$router->get('/attachment', 'attachments/show.php')->only('auth');

// create folders
$router->get("/folders" , "folders/show.php")->only('auth');
$router->post("/folder" , 'folders/store.php')->only('auth');