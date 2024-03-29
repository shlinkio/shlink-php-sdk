<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

enum TagsListOrderField: string
{
    case TAG = 'tag';
    case SHORT_URLS_COUNT = 'shortUrlsCount';
    case VISITS_COUNT = 'visitsCount';
}
