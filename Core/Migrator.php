<?php
namespace Core;

use PDO;

class Migrator
{
    protected PDO $connection;
    protected string $path;

    public function __construct(PDO $connection, string $path)
    {
        $this->connection = $connection;
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
    }

    public function ensureMigrationsTable()
    {
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                batch INT NOT NULL,
                executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function run()
    {
        $this->ensureMigrationsTable();
        $pending = $this->pendingMigrations();

        if (empty($pending)) {
            return [];
        }

        $batch = $this->nextBatchNumber();
        $ran = [];

        foreach ($pending as $file) {
            $migration = require $file;

            if (! isset($migration['up']) || ! is_callable($migration['up'])) {
                throw new \Exception("Migration {$file} is missing an up callback.");
            }

            $migration['up']($this->connection);
            $this->recordMigration(basename($file), $batch);
            $ran[] = basename($file);
        }

        return $ran;
    }

    public function rollback()
    {
        $this->ensureMigrationsTable();
        $migrations = $this->lastBatchMigrations();

        if (empty($migrations)) {
            return [];
        }

        $rolledBack = [];

        foreach ($migrations as $migrationName) {
            $file = $this->path . DIRECTORY_SEPARATOR . $migrationName;

            if (! file_exists($file)) {
                throw new \Exception("Migration file not found: {$file}");
            }

            $migration = require $file;

            if (! isset($migration['down']) || ! is_callable($migration['down'])) {
                throw new \Exception("Migration {$file} is missing a down callback.");
            }

            $migration['down']($this->connection);
            $this->deleteMigration($migrationName);
            $rolledBack[] = $migrationName;
        }

        return $rolledBack;
    }

    public function status()
    {
        $this->ensureMigrationsTable();
        $applied = $this->appliedMigrations();

        return array_map(function ($file) use ($applied) {
            $name = basename($file);

            return [
                'migration' => $name,
                'status' => in_array($name, $applied) ? 'ran' : 'pending'
            ];
        }, $this->migrationFiles());
    }

    protected function pendingMigrations()
    {
        $applied = $this->appliedMigrations();

        return array_values(array_filter($this->migrationFiles(), function ($file) use ($applied) {
            return ! in_array(basename($file), $applied);
        }));
    }

    protected function migrationFiles()
    {
        $files = glob($this->path . DIRECTORY_SEPARATOR . '*.php');
        sort($files);

        return $files;
    }

    protected function appliedMigrations()
    {
        $statement = $this->connection->query('SELECT migration FROM migrations ORDER BY migration');

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    protected function nextBatchNumber()
    {
        $statement = $this->connection->query('SELECT MAX(batch) FROM migrations');

        return ((int) $statement->fetchColumn()) + 1;
    }

    protected function lastBatchMigrations()
    {
        $statement = $this->connection->query('SELECT MAX(batch) FROM migrations');
        $batch = (int) $statement->fetchColumn();

        if ($batch === 0) {
            return [];
        }

        $statement = $this->connection->prepare(
            'SELECT migration FROM migrations WHERE batch = :batch ORDER BY migration DESC'
        );
        $statement->execute(['batch' => $batch]);

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    protected function recordMigration($migration, $batch)
    {
        $statement = $this->connection->prepare(
            'INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)'
        );
        $statement->execute([
            'migration' => $migration,
            'batch' => $batch
        ]);
    }

    protected function deleteMigration($migration)
    {
        $statement = $this->connection->prepare('DELETE FROM migrations WHERE migration = :migration');
        $statement->execute(['migration' => $migration]);
    }
}
