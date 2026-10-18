<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function testTruncate(): void
    {
        $longText = str_repeat('я', Formatter::MAX_TEXT_LENGTH + 1);
        $expected = sprintf('%s...', str_repeat('я', Formatter::MAX_TEXT_LENGTH));

        $this->assertSame($expected, Formatter::truncate($longText));
        $this->assertSame('short', Formatter::truncate('short'));
        $this->assertSame('', Formatter::truncate(null));
    }

    public function testDate(): void
    {
        $this->assertSame('2024-01-01 10:00:00', Formatter::date('2024-01-01 10:00:00'));
        $this->assertSame('', Formatter::date(null));
    }
}
