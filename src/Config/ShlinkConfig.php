<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

final readonly class ShlinkConfig implements ShlinkConfigInterface
{
    private function __construct(private string $baseUrl, private string $apiKey, private ApiVersion $version)
    {
    }

    public static function fromBaseUrlAndApiKey(string $baseUrl, string $apiKey): ShlinkConfigInterface
    {
        return new self($baseUrl, $apiKey, ApiVersion::V3);
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

    /**
     * @throws InvalidConfigException
     */
    public static function fromRawConfig(RawConfigInterface $rawConfig): ShlinkConfigInterface
    {
        $baseUrl = $rawConfig->baseUrl();
        $apiKey = $rawConfig->apiKey();
        if (empty($baseUrl) || empty($apiKey)) {
            throw $rawConfig->missingConfigException();
        }

        $version = $rawConfig->version();
        if ($version !== '' && ApiVersion::tryFrom($version) === null) {
            throw InvalidConfigException::forInvalidVersion($version);
        }

        return self::fromBaseUrlAndApiKey($baseUrl, $apiKey);
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }

    public function version(): ApiVersion
    {
        return $this->version;
    }
}
