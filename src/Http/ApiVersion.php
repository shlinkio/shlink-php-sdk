<?php

namespace Shlinkio\Shlink\SDK\Http;

use function array_map;

enum ApiVersion: string
{
    case V2 = '2';
    case V3 = '3';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn (ApiVersion $version) => $version->value, self::cases());
    }
}
