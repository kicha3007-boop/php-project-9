<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\UrlValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    public static function urlProvider(): array
    {
        $longUrl = sprintf('https://%s.com', str_repeat('a', UrlValidator::MAX_LENGTH));

        return [
            'valid' => ['https://example.com', null],
            'valid with path' => ['http://example.com/path?query=1', null],
            'empty' => ['', 'URL не должен быть пустым'],
            'wrong scheme' => ['httpsss://abcabca@test.ru', 'Некорректный URL'],
            'not url' => ['example', 'Некорректный URL'],
            'too long' => [$longUrl, 'URL превышает 255 символов'],
        ];
    }

    #[DataProvider('urlProvider')]
    public function testValidate(string $url, ?string $expectedError): void
    {
        $this->assertSame($expectedError, UrlValidator::validate($url));
    }
}
