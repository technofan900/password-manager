<?php

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "This script can only be run from the command line.\n";
    exit(1);
}

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'Core/functions.php';

spl_autoload_register(function ($class) {
    if (! str_starts_with($class, 'Core\\') && ! str_starts_with($class, 'Http\\')) {
        return false;
    }

    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $fullPath = base_path($classPath);

    if (file_exists($fullPath)) {
        require $fullPath;
        return true;
    }

    return false;
});

require_once base_path('bootstrap.php');

use Core\App;
use Core\Database;
use Core\Migrator;

$command = $argv[1] ?? 'run';

if (! in_array($command, ['run', 'status', 'rollback'])) {
    echo "Usage: php cli/migrate.php [run|status|rollback]\n";
    exit(1);
}

$db = App::resolve(Database::class);
$migrator = new Migrator($db->connection, base_path('database/migrations'));

try {
    if ($command === 'status') {
        $rows = $migrator->status();

        if (empty($rows)) {
            echo "No migrations found.\n";
            exit(0);
        }

        foreach ($rows as $row) {
            echo str_pad($row['status'], 10) . $row['migration'] . "\n";
        }

        exit(0);
    }

    if ($command === 'rollback') {
        $rolledBack = $migrator->rollback();

        if (empty($rolledBack)) {
            echo "Nothing to rollback.\n";
            exit(0);
        }

        foreach ($rolledBack as $migration) {
            echo "Rolled back: {$migration}\n";
        }

        exit(0);
    }

    $ran = $migrator->run();

    if (empty($ran)) {
        echo "Nothing to migrate.\n";
        exit(0);
    }

    foreach ($ran as $migration) {
        echo "Migrated: {$migration}\n";
    }
} catch (Throwable $exception) {
    echo "Migration failed: {$exception->getMessage()}\n";
    exit(1);
}
