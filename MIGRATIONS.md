# Database migrations

Run all pending migrations:

```bash
php cli/migrate.php
```

Check which migrations have run:

```bash
php cli/migrate.php status
```

Rollback the last migration batch:

```bash
php cli/migrate.php rollback
```

Create new migrations in `database/migrations`. Each migration file should return an array with `up` and `down` callbacks:

```php
<?php

return [
    'up' => function (\PDO $pdo) {
        $pdo->exec('ALTER TABLE login ADD COLUMN example VARCHAR(255) NULL');
    },
    'down' => function (\PDO $pdo) {
        $pdo->exec('ALTER TABLE login DROP COLUMN example');
    }
];
```
