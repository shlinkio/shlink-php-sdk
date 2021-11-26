<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

final class DomainRedirectProps
{
    public const BASE_URL = 'baseUrlRedirect';
    public const REGULAR_NOT_FOUND = 'regular404Redirect';
    public const INVALID_SHORT_URL = 'invalidShortUrlRedirect';

    private function __construct() {
    }
}
