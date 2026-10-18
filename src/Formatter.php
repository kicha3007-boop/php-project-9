<?php

namespace Hexlet\Code;

use Carbon\Carbon;

class Formatter
{
    public const MAX_TEXT_LENGTH = 200;

    public static function truncate(?string $text): string
    {
        if ($text === null || mb_strlen($text) <= self::MAX_TEXT_LENGTH) {
            return $text ?? '';
        }

        $shortText = mb_substr($text, 0, self::MAX_TEXT_LENGTH);

        return "{$shortText}...";
    }

    public static function date(?string $dateTime): string
    {
        return $dateTime === null ? '' : Carbon::parse($dateTime)->toDateTimeString();
    }
}
