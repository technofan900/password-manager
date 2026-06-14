<?php

/**
 * CLI COMMAND ONLY - Admin Promotion Tool
 * This script can ONLY be run from the command line, not via web browser.
 * Prevents exploitation by normal users.
 * 
 * Usage: php cli/promote-admin.php <user_id_or_email> [--remove]
 * 
 * Examples:
 *   php cli/promote-admin.php 1
 *   php cli/promote-admin.php user@example.com
 *   php cli/promote-admin.php 1 --remove
 * 
 * Warning - login again to gain access to admin privileges.
 */

// Prevent web access
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "This script can only be run from the command line.\n";
    exit(1);
}

// Setup base path and autoloader
const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'Core/functions.php';

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path("{$class}.php");
});

// Load application
require_once __DIR__ . '/../bootstrap.php';

use Core\App;
use Core\Database;

// Check if arguments provided
if (empty($GLOBALS['argv'][1])) {
    echo "Usage: php cli/promote-admin.php <user_id_or_email> [--remove]\n";
    echo "Examples:\n";
    echo "  php cli/promote-admin.php 1\n";
    echo "  php cli/promote-admin.php user@example.com\n";
    echo "  php cli/promote-admin.php 1 --remove\n";
    exit(1);
}

$identifier = $GLOBALS['argv'][1];
$removeAdmin = isset($GLOBALS['argv'][2]) && $GLOBALS['argv'][2] === '--remove';

// Get database instance
$db = App::resolve(Database::class);

// Find user by ID or email
$query = is_numeric($identifier) 
    ? "SELECT * FROM login WHERE id = :id"
    : "SELECT * FROM login WHERE email = :email";

$params = is_numeric($identifier)
    ? ['id' => (int)$identifier]
    : ['email' => $identifier];

$user = $db->query($query, $params)->find();

if (!$user) {
    echo "❌ User not found: $identifier\n";
    exit(1);
}

// Update admin status
$action = $removeAdmin ? 'removed from' : 'promoted to';
$newStatus = $removeAdmin ? 0 : 1;

$db->query("UPDATE login SET is_admin = :is_admin WHERE id = :id", [
    'is_admin' => $newStatus,
    'id' => $user['id']
]);

echo "✓ User '{$user['username']}' ({$user['email']}) has been $action admin.\n";
exit(0);
