<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Http\ApiVersion;

interface ShlinkConfigInterface
{
    public function baseUrl(): string;

    public function apiKey(): string;

    public function version(): ApiVersion;
}
