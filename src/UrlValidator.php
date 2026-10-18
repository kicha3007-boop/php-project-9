<?php

namespace Hexlet\Code;

use Valitron\Validator;

class UrlValidator
{
    public const MAX_LENGTH = 255;

    /**
     * Возвращает текст ошибки или null, если адрес корректен.
     */
    public static function validate(string $url): ?string
    {
        $validator = new Validator(['url' => $url]);
        $validator->stopOnFirstFail();
        $validator->rule('required', 'url')->message('URL не должен быть пустым');
        $validator->rule('lengthMax', 'url', self::MAX_LENGTH)->message('URL превышает 255 символов');
        $validator->rule('url', 'url')->message('Некорректный URL');

        if ($validator->validate()) {
            return null;
        }

        $errors = $validator->errors('url');

        return $errors[0];
    }
}
