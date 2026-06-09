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

// Ensure Composer autoloader is available in all entrypoints
$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

$container = new Container();

$container->bind('Core\Database', function () {

    $config = require base_path('config.php');
    return new Database($config['database']);
});

// Bind a Symfony Mailer if MAILER_DSN is configured. Attempt to create the mailer
// and log any errors; fall back to null mailer if creation fails.
$mailerDsn = getenv('MAILER_DSN') ?: null;
if ($mailerDsn) {
    $container->bind('Symfony\\Component\\Mailer\\MailerInterface', function () use ($mailerDsn) {
        $logPath = __DIR__ . '/storage/mail_errors.log';
        try {
            $transport = \Symfony\Component\Mailer\Transport::fromDsn($mailerDsn);
            return new \Symfony\Component\Mailer\Mailer($transport);
        } catch (\Throwable $ex) {
            @file_put_contents($logPath, "[" . date('c') . "] bootstrap: failed to create symfony mailer: " . $ex->getMessage() . "\n", FILE_APPEND | LOCK_EX);
            throw $ex;
        }
    });
}

App::setContainer($container);
