<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;

final class ShlinkConfig implements ShlinkConfigInterface
{
    private function __construct(private string $baseUrl, private string $apiKey)
    {
    }

    public static function fromBaseUrlAndApiKey(string $baseUrl, string $apiKey): ShlinkConfigInterface
    {
        return new self($baseUrl, $apiKey);
    }

    /**
     * @throws InvalidConfigException
     */
    public static function fromEnv(): ShlinkConfigInterface
    {
        return EnvShlinkConfig::fromEnv();
    }

    /**
     * @throws InvalidConfigException
     */
    public static function fromArray(array $config): ShlinkConfigInterface
    {
        return ArrayShlinkConfig::fromArray($config);
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }
}
