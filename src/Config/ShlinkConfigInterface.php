<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

interface ShlinkConfigInterface
{
    public function baseUrl(): string;

    public function apiKey(): string;
}
