<?php

namespace Hexlet\Code;

use Symfony\Component\DomCrawler\Crawler;

class PageParser
{
    /**
     * @return array{h1: ?string, title: ?string, description: ?string}
     */
    public static function parse(string $html): array
    {
        $crawler = new Crawler($html);

        return [
            'h1' => self::extractText($crawler, 'h1'),
            'title' => self::extractText($crawler, 'title'),
            'description' => self::extractAttribute($crawler, 'meta[name="description"]', 'content'),
        ];
    }

    private static function extractText(Crawler $crawler, string $selector): ?string
    {
        $node = $crawler->filter($selector);

        return $node->count() > 0 ? trim($node->first()->text()) : null;
    }

    private static function extractAttribute(Crawler $crawler, string $selector, string $attribute): ?string
    {
        $node = $crawler->filter($selector);

        return $node->count() > 0 ? $node->first()->attr($attribute) : null;
    }
}
