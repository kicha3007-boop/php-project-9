<?php

namespace Hexlet\Code;

use Carbon\Carbon;
use PDO;

class UrlRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM urls WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    public function findByName(string $name): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM urls WHERE name = :name');
        $statement->execute(['name' => $name]);

        return $statement->fetch() ?: null;
    }

    public function create(string $name): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO urls (name, created_at) VALUES (:name, :created_at) RETURNING id'
        );
        $statement->execute(['name' => $name, 'created_at' => Carbon::now()->toDateTimeString()]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Сайты от новых к старым вместе с датой и кодом последней проверки — одним запросом.
     */
    public function allWithLastCheck(): array
    {
        $sql = <<<SQL
            SELECT urls.id, urls.name, last_check.status_code, last_check.created_at AS last_check_at
            FROM urls
            LEFT JOIN LATERAL (
                SELECT status_code, created_at
                FROM url_checks
                WHERE url_checks.url_id = urls.id
                ORDER BY url_checks.id DESC
                LIMIT 1
            ) AS last_check ON TRUE
            ORDER BY urls.id DESC
            SQL;

        return $this->pdo->query($sql)->fetchAll();
    }
}
