<?php

namespace Hexlet\Code;

class UrlNormalizer
{
    /**
     * Оставляет схему и хост в нижнем регистре, путь и параметры отбрасывает.
     */
    public static function normalize(string $url): string
    {
        $parts = parse_url(trim($url));
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $port = isset($parts['port']) ? ":{$parts['port']}" : '';

        return "{$scheme}://{$host}{$port}";
    }
}
