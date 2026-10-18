<?php

namespace Hexlet\Code;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class PageChecker
{
    public const REQUEST_TIMEOUT = 10;

    public function __construct(private Client $client)
    {
    }

    /**
     * Запрашивает страницу и собирает данные проверки; null — если сайт недоступен.
     *
     * @return array{status_code: int, h1: ?string, title: ?string, description: ?string}|null
     */
    public function check(string $url): ?array
    {
        try {
            $response = $this->client->get($url);
        } catch (ConnectException | RequestException $exception) {
            error_log("Check of {$url} failed: {$exception->getMessage()}");
            return null;
        }

        return [
            'status_code' => $response->getStatusCode(),
            ...PageParser::parse((string) $response->getBody()),
        ];
    }
}
