<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Utils;

use JsonException;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class JsonDecoder
{
    /**
     * @throws JsonException
     */
    public static function decode(string $data): array
    {
        // @mago-expect analysis:mixed-return-statement
        return json_decode($data, associative: true, flags: JSON_THROW_ON_ERROR);
    }
}
