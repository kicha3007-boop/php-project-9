<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\UrlNormalizer;
use PHPUnit\Framework\TestCase;

class UrlNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $this->assertSame('https://example.com', UrlNormalizer::normalize(' HTTPS://Example.COM/some/path?q=1 '));
        $this->assertSame('http://localhost:8080', UrlNormalizer::normalize('http://localhost:8080/page'));
    }
}
