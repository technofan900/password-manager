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

$config = require base_path('config.php');
$rateLimit = $config['rate_limit'] ?? [];

if (($rateLimit['enabled'] ?? true) === true) {
    $routeKey = strtoupper($method) . ':' . $path;
    $rule = $rateLimit['routes'][$routeKey] ?? $rateLimit;
    $maxAttempts = (int) ($rule['max_attempts'] ?? 120);
    $decaySeconds = (int) ($rule['decay_seconds'] ?? 60);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $bucket = isset($rateLimit['routes'][$routeKey])
        ? "{$clientIp}:{$routeKey}"
        : "{$clientIp}:global";

    $limiter = new \Core\RateLimiter(base_path('storage/rate_limits'));
    $result = $limiter->attempt($bucket, $maxAttempts, $decaySeconds);

    header('X-RateLimit-Limit: ' . $maxAttempts);
    header('X-RateLimit-Remaining: ' . $result['remaining']);

    if (! $result['allowed']) {
        http_response_code(429);
        header('Retry-After: ' . $result['retry_after']);
        require base_path('views/429.php');
        exit;
    }
}

$router->route($path, $method);

// unset($_SESSION['_flash']);
Session::unflash();
