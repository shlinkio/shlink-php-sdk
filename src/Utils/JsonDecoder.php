<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Utils;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class JsonDecoder
{
    public static function decode(string $data): array
    {
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }
}
