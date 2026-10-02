<?php

namespace App\Core;

use PDO;

class Migrator
{
    private static function ensureMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        Database::getInstance()->exec($sql);
    }

    public static function migrate(): void
    {
        self::ensureMigrationsTable();
        $db = Database::getInstance();

        $executed = $db->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
        $files = glob(BASE_PATH . '/database/migrations/*.php');
        sort($files);

        $nextBatch = (int)$db->query("SELECT MAX(batch) FROM migrations")->fetchColumn() + 1;
        $count = 0;

        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $executed)) {
                continue;
            }

            $migration = require $file;
            echo "Migrating: {$name}\n";

            try {
                if ($db->inTransaction()) {
                    // Note: DDL in MySQL causes implicit commit
                } else {
                    $db->beginTransaction();
                }
                if (isset($migration['up']) && is_array($migration['up'])) {
                    foreach ($migration['up'] as $sql) {
                        $db->exec($sql);
                    }
                }
                $stmt = $db->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$name, $nextBatch]);
                if ($db->inTransaction()) {
                    $db->commit();
                }
                echo "Migrated:  {$name}\n";
                $count++;
            } catch (\Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                echo "Error executing {$name}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }

        if ($count === 0) {
            echo "Nothing to migrate.\n";
        }
    }

    public static function rollback(): void
    {
        self::ensureMigrationsTable();
        $db = Database::getInstance();

        $lastBatch = (int)$db->query("SELECT MAX(batch) FROM migrations")->fetchColumn();
        if ($lastBatch === 0) {
            echo "Nothing to rollback.\n";
            return;
        }

        $stmt = $db->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
        $stmt->execute([$lastBatch]);
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($migrations as $name) {
            $file = BASE_PATH . "/database/migrations/{$name}";
            if (!file_exists($file)) {
                echo "Migration file missing: {$name}\n";
                continue;
            }

            $migration = require $file;
            echo "Rolling back: {$name}\n";

            try {
                if (!$db->inTransaction()) {
                    $db->beginTransaction();
                }
                if (isset($migration['down']) && is_array($migration['down'])) {
                    foreach ($migration['down'] as $sql) {
                        $db->exec($sql);
                    }
                }
                $deleteStmt = $db->prepare("DELETE FROM migrations WHERE migration = ?");
                $deleteStmt->execute([$name]);
                if ($db->inTransaction()) {
                    $db->commit();
                }
                echo "Rolled back:  {$name}\n";
            } catch (\Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                echo "Error rolling back {$name}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    }

    public static function fresh(): void
    {
        $db = Database::getInstance();
        $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $db->exec("DROP TABLE IF EXISTS `{$table}`");
            echo "Dropped table: {$table}\n";
        }
        $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

        self::migrate();
    }

    public static function status(): void
    {
        self::ensureMigrationsTable();
        $db = Database::getInstance();

        $executed = $db->query("SELECT migration, batch, executed_at FROM migrations ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        $executedMap = [];
        foreach ($executed as $row) {
            $executedMap[$row['migration']] = $row;
        }

        $files = glob(BASE_PATH . '/database/migrations/*.php');
        sort($files);

        echo str_pad("Migration", 50) . str_pad("Ran?", 10) . str_pad("Batch", 10) . "Executed At\n";
        echo str_repeat("-", 90) . "\n";

        foreach ($files as $file) {
            $name = basename($file);
            if (isset($executedMap[$name])) {
                $info = $executedMap[$name];
                echo str_pad($name, 50) . str_pad("Yes", 10) . str_pad((string)$info['batch'], 10) . $info['executed_at'] . "\n";
            } else {
                echo str_pad($name, 50) . str_pad("No", 10) . str_pad("-", 10) . "Pending\n";
            }
        }
    }
}
