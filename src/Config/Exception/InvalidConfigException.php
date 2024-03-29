<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Config\ArrayShlinkConfig;
use Shlinkio\Shlink\SDK\Config\EnvShlinkConfig;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

use function implode;
use function sprintf;

class InvalidConfigException extends RuntimeException implements ExceptionInterface
{
    public static function forMissingEnvVars(): self
    {
        return new self(sprintf(
            'Either "%s" and/or "%s" env vars are missing. Make sure both are properly set.',
            EnvShlinkConfig::BASE_URL_ENV_VAR,
            EnvShlinkConfig::API_KEY_ENV_VAR,
        ));
    }

    public static function forInvalidConfig(): self
    {
        return new self(sprintf(
            'Provided array is missing "%s" and/or "%s" props, or their values are invalid. Make sure both are set '
            . 'with strings.',
            ArrayShlinkConfig::BASE_URL_PROP,
            ArrayShlinkConfig::API_KEY_PROP,
        ));
    }

    /**
     * @param non-empty-string $invalidVersion
     */
    public static function forInvalidVersion(string $invalidVersion): self
    {
        return new self(sprintf(
            'Provided version "%s" is invalid. Expected one of ["%s"]',
            $invalidVersion,
            implode('", "', ApiVersion::values()),
        ));
    }
}
