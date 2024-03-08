<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

use function getenv;
use function trim;

final readonly class EnvShlinkConfig implements ShlinkConfigInterface
{
    public const BASE_URL_ENV_VAR = 'SHLINK_BASE_URL';
    public const API_KEY_ENV_VAR = 'SHLINK_API_KEY';
    public const VERSION_ENV_VAR = 'SHLINK_API_VERSION';

    private function __construct(private ShlinkConfigInterface $wrapped)
    {
    }

    /**
     * @throws InvalidConfigException
     */
    public static function fromEnv(): self
    {
        $env = getenv();
        return new self(ShlinkConfig::fromRawConfig(new class ($env) implements RawConfigInterface {
            public function __construct(private readonly array $env)
            {
            }

            public function baseUrl(): string
            {
                return trim($this->env[EnvShlinkConfig::BASE_URL_ENV_VAR] ?? '');
            }

            public function apiKey(): string
            {
                return trim($this->env[EnvShlinkConfig::API_KEY_ENV_VAR] ?? '');
            }

            public function version(): string
            {
                return trim($this->env[EnvShlinkConfig::VERSION_ENV_VAR] ?? '');
            }

            public function missingConfigException(): InvalidConfigException
            {
                return InvalidConfigException::forMissingEnvVars();
            }
        }));
    }

    public function baseUrl(): string
    {
        return $this->wrapped->baseUrl();
    }

    public function apiKey(): string
    {
        return $this->wrapped->apiKey();
    }

    public function version(): ApiVersion
    {
        return $this->wrapped->version();
    }
}
