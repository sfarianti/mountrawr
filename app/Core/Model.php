<?php

namespace App\Core;

use PDO;

class Model
{
    protected static function db(): PDO
    {
        return Database::getInstance();
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    public static function lastInsertId(): string|false
    {
        return self::db()->lastInsertId();
    }

    private static int $transactionDepth = 0;

    public static function beginTransaction(): bool
    {
        if (self::$transactionDepth === 0) {
            self::db()->beginTransaction();
        }
        self::$transactionDepth++;
        return true;
    }

    public static function commit(): bool
    {
        if (self::$transactionDepth > 0) {
            self::$transactionDepth--;
            if (self::$transactionDepth === 0) {
                return self::db()->commit();
            }
        }
        return true;
    }

    public static function rollBack(): bool
    {
        if (self::$transactionDepth > 0) {
            self::$transactionDepth = 0;
            if (self::db()->inTransaction()) {
                return self::db()->rollBack();
            }
        }
        return true;
    }
}
