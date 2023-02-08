<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Config\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;

class InvalidConfigExceptionTest extends TestCase
{
    #[Test]
    public function createsExceptionForMissingEnvVars(): void
    {
        $e = InvalidConfigException::forMissingEnvVars();

        self::assertEquals(
            'Either "SHLINK_BASE_URL" and/or "SHLINK_API_KEY" env vars are missing. Make sure both are properly set.',
            $e->getMessage(),
        );
    }

    #[Test]
    public function createsExceptionForInvalidConfig(): void
    {
        $e = InvalidConfigException::forInvalidConfig();

        self::assertEquals(
            'Provided array is missing "baseUrl" and/or "apiKey" props, or their values are invalid. Make sure both '
            . 'are set with strings.',
            $e->getMessage(),
        );
    }
}
