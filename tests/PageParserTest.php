<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\PageParser;
use PHPUnit\Framework\TestCase;

class PageParserTest extends TestCase
{
    public function testParse(): void
    {
        $html = file_get_contents(__DIR__ . '/fixtures/page.html');

        $this->assertSame([
            'h1' => 'Main header',
            'title' => 'Page title',
            'description' => 'Page description',
        ], PageParser::parse($html));
    }

    public function testParseEmptyPage(): void
    {
        $this->assertSame(
            ['h1' => null, 'title' => null, 'description' => null],
            PageParser::parse('<html><body></body></html>')
        );
    }
}
