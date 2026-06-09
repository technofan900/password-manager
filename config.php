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
        'encryption_key' => $envKey ?: null,
        'email_two_factor_enabled' => getenv('EMAIL_2FA_ENABLED') !== 'false',
        'email_two_factor_code_length' => intval(getenv('EMAIL_2FA_CODE_LENGTH') ?: 6),
    ],
    'mail' => [
        'from' => getenv('MAIL_FROM') ?: 'no-reply@localhost',
        'subject' => getenv('MAIL_SUBJECT') ?: 'Your authentication code'
    ],
];
