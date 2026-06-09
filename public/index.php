<?php
use Core\Session;
session_start();

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'Core/functions.php';

if (file_exists(BASE_PATH . 'vendor/autoload.php')) {
    require BASE_PATH . 'vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path("{$class}.php");
});

$router = new \Core\Router();
$routes = require base_path("routes.php");
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

require base_path('bootstrap.php');

$router->route($path, $method);

// unset($_SESSION['_flash']);
Session::unflash();