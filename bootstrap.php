<?php
use Core\App;
use Core\Database;
use Core\Container;

// Load .env file into environment (simple loader)
// bootstrap.php lives in project root, so .env is in the same directory
$dotenvPath = __DIR__ . '/.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (! strpos($line, '=')) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (strlen($value) >= 2 && $value[0] === '"' && $value[strlen($value)-1] === '"') {
            $value = substr($value, 1, -1);
        }
        putenv("{$name}={$value}");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

$container = new Container();

$container->bind('Core\Database', function () {

    $config = require base_path('config.php');
    return new Database($config['database']);
});

App::setContainer($container);
