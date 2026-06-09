<?php
use Core\Session;
session_start();

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'Core/functions.php';

if (file_exists(BASE_PATH . 'vendor/autoload.php')) {
    require BASE_PATH . 'vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    // Only autoload our application namespaces to avoid intercepting vendor classes
    if (!str_starts_with($class, 'Core\\') && !str_starts_with($class, 'Http\\')) {
        return false;
    }
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $full = base_path($classPath);
    if (file_exists($full)) {
        require $full;
        return true;
    }
    return false;
});

$router = new \Core\Router();
$routes = require base_path("routes.php");
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

require base_path('bootstrap.php');

$router->route($path, $method);

// unset($_SESSION['_flash']);
Session::unflash();