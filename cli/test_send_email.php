<?php
if (php_sapi_name() !== 'cli') {
	http_response_code(403);
	echo "This script can only be run from the command line.\n";
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

require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'bootstrap.php';

session_start();

use Core\TwoFactor\TwoFactorService;

$testEmail = $argv[1] ?? getenv('TEST_EMAIL') ?: getenv('MAIL_FROM') ?: 'test@example.com';

$service = new TwoFactorService();
$service->generateEmailCodeForUser(999, $testEmail);

echo "Attempted send to {$testEmail}\n";
