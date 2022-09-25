<?php

namespace Shlinkio\Shlink\SDK\Visits\Model;

enum OrphanVisitType: string
{
    case BASE_URL = 'base_url';
    case REGULAR_NOT_FOUND = 'regular_404';
    case INVALID_SHORT_URL = 'invalid_short_url';
}
