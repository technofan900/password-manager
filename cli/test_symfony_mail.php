<?php
if (php_sapi_name() !== 'cli') {
    echo "This script must be run from CLI.\n";
    exit(1);
}

const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'Core/functions.php';

// Simple autoloader compatible with the app's class layout
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

require BASE_PATH . 'vendor/autoload.php';
require BASE_PATH . 'bootstrap.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$dsn = getenv('MAILER_DSN') ?: null;
if (! $dsn) {
    echo "MAILER_DSN not set\n";
    exit(1);
}

$to = $argv[1] ?? getenv('TEST_EMAIL') ?? getenv('MAIL_FROM') ?? 'test@example.com';
$from = getenv('MAIL_FROM') ?: 'no-reply@example.com';

try {
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);

    $email = (new Email())
        ->from($from)
        ->to($to)
        ->subject('Symfony Mailer test')
        ->text('This is a test message.');

    $mailer->send($email);
    echo "Symfony mailer: sent to {$to}\n";
    @file_put_contents(__DIR__ . '/../storage/mail_errors.log', "[" . date('c') . "] symfony-test: sent to {$to}\n", FILE_APPEND);
} catch (\Throwable $ex) {
    $msg = "symfony-test: failed to send to {$to} error=" . $ex->getMessage();
    echo $msg . "\n";
    @file_put_contents(__DIR__ . '/../storage/mail_errors.log', "[" . date('c') . "] {$msg}\n", FILE_APPEND);
    exit(1);
}
