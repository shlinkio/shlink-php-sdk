<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

enum ShortUrlListOrderField: string
{
    case LONG_URL = 'longUrl';
    case SHORT_CODE = 'shortCode';
    case DATE_CREATED = 'dateCreated';
    case VISITS = 'visits';
    case TITLE = 'title';
}
