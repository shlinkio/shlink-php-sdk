<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

final class ShortUrlListOrderFields
{
    public const LONG_URL = 'longUrl';
    public const SHORT_CODE = 'shortCode';
    public const DATE_CREATED = 'dateCreated';
    public const VISITS = 'visits';
    public const TITLE = 'title';

    private function __construct()
    {
    }
}
