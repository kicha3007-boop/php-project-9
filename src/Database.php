<?php

namespace Hexlet\Code;

use PDO;
use RuntimeException;

class Database
{
    private const DEFAULT_PORT = 5432;

    /**
     * Подключение по строке вида postgresql://user:password@host:port/db
     */
    public static function connect(string $databaseUrl): PDO
    {
        $parts = parse_url($databaseUrl);
        if ($databaseUrl === '' || $parts === false || !isset($parts['host'], $parts['path'])) {
            throw new RuntimeException('DATABASE_URL is not set or invalid');
        }

        $host = $parts['host'];
        $port = $parts['port'] ?? self::DEFAULT_PORT;
        $dbName = ltrim($parts['path'], '/');
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbName}";

        return new PDO(
            $dsn,
            isset($parts['user']) ? urldecode($parts['user']) : null,
            isset($parts['pass']) ? urldecode($parts['pass']) : null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}
