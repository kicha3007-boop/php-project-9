<?php

namespace Hexlet\Code;

use Carbon\Carbon;
use PDO;

class UrlCheckRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByUrlId(int $urlId): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM url_checks WHERE url_id = :url_id ORDER BY id DESC');
        $statement->execute(['url_id' => $urlId]);

        return $statement->fetchAll();
    }

    /**
     * @param array{status_code: int, h1: ?string, title: ?string, description: ?string} $check
     */
    public function create(int $urlId, array $check): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at)
             VALUES (:url_id, :status_code, :h1, :title, :description, :created_at)'
        );
        $statement->execute([
            'url_id' => $urlId,
            'status_code' => $check['status_code'],
            'h1' => $check['h1'],
            'title' => $check['title'],
            'description' => $check['description'],
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
