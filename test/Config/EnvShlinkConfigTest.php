<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Config;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\EnvShlinkConfig;
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;

use function putenv;
use function sprintf;

class EnvShlinkConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv(sprintf('%s=', EnvShlinkConfig::API_KEY_ENV_VAR));
        putenv(sprintf('%s=', EnvShlinkConfig::BASE_URL_ENV_VAR));
    }

    /**
     * @test
     * @dataProvider provideWrongEnvSetUps
     */
    public function exceptionIsThrownIfSomeEnvVarIsMissing(callable $setUp): void
    {
        $setUp();

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            'Either "SHLINK_BASE_URL" and/or "SHLINK_API_KEY" env vars are missing. Make sure both are properly set.',
        );

        EnvShlinkConfig::fromEnv();
    }

    public function provideWrongEnvSetUps(): iterable
    {
        $buildSetUpWithEnvVars = static fn (?string $envVar = null) => static function () use ($envVar): void {
            if ($envVar) {
                putenv(sprintf('%s=SOME_VALUE', $envVar));
            }
        };

        yield 'no env vars' => [$buildSetUpWithEnvVars()];
        yield 'missing base url' => [$buildSetUpWithEnvVars(EnvShlinkConfig::API_KEY_ENV_VAR)];
        yield 'missing api key' => [$buildSetUpWithEnvVars(EnvShlinkConfig::BASE_URL_ENV_VAR)];
    }

    /** @test */
    public function configIsProperlyInitializedIfBothEnvVarsAreSet(): void
    {
        putenv(sprintf('%s=API_KEY', EnvShlinkConfig::API_KEY_ENV_VAR));
        putenv(sprintf('%s=BASE_URL', EnvShlinkConfig::BASE_URL_ENV_VAR));

        $config = EnvShlinkConfig::fromEnv();

        self::assertEquals('API_KEY', $config->apiKey());
        self::assertEquals('BASE_URL', $config->baseUrl());
    }
}
