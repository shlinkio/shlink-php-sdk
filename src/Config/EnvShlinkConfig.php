<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;

use function getenv;
use function trim;

final class EnvShlinkConfig implements ShlinkConfigInterface
{
    public const BASE_URL_ENV_VAR = 'SHLINK_BASE_URL';
    public const API_KEY_ENV_VAR = 'SHLINK_API_KEY';

    private function __construct(private ShlinkConfigInterface $wrapped)
    {
    }

    /**
     * @throws InvalidConfigException
     */
    public static function fromEnv(): self
    {
        $env = getenv();
        $baseUrl = trim($env[self::BASE_URL_ENV_VAR] ?? '');
        $apiKey = trim($env[self::API_KEY_ENV_VAR] ?? '');

        if (empty($baseUrl) || empty($apiKey)) {
            throw InvalidConfigException::forMissingEnvVars();
        }

        return new self(ShlinkConfig::fromBaseUrlAndApiKey($baseUrl, $apiKey));
    }

    public function baseUrl(): string
    {
        return $this->wrapped->baseUrl();
    }

    public function apiKey(): string
    {
        return $this->wrapped->apiKey();
    }
}
