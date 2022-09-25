<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

enum DomainRedirectProps: string
{
    case BASE_URL = 'baseUrlRedirect';
    case REGULAR_NOT_FOUND = 'regular404Redirect';
    case INVALID_SHORT_URL = 'invalidShortUrlRedirect';
}
