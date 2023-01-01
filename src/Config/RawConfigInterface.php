<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;

interface RawConfigInterface
{
    public function baseUrl(): string;

    public function apiKey(): string;

    public function version(): string;

    public function missingConfigException(): InvalidConfigException;
}
