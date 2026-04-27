<?php

// Read values from environment when available. Keep sensible defaults for local/dev.
$envKey = getenv('APP_KEY') ?: getenv('APP_ENCRYPTION_KEY');
return [
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: 3306,
        'dbname' => getenv('DB_NAME') ?: 'school_project',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4'
    ],
    'app' => [
        // Encryption key: prefer env variable `APP_KEY` or `APP_ENCRYPTION_KEY`.
        // Do NOT keep real keys in this file. If env is missing, value will be null.
        'encryption_key' => $envKey ?: null
    ]
];
