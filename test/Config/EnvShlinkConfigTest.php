<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Config;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\EnvShlinkConfig;
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

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
    public function exceptionIsThrownIfSomeEnvVarIsMissing(callable $setUp, string $expectedMessage): void
    {
        $setUp();

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage($expectedMessage);

        EnvShlinkConfig::fromEnv();
    }

    public function provideWrongEnvSetUps(): iterable
    {
        $buildSetUpWithEnvVars = static fn (array $envVars = []) => static function () use ($envVars): void {
            foreach ($envVars as $key => $value) {
                putenv(sprintf('%s=%s', $key, $value));
            }
        };
        $standardMessage = 'Either "SHLINK_BASE_URL" and/or "SHLINK_API_KEY" env vars are missing. '
            . 'Make sure both are properly set.';

        yield 'no env vars' => [$buildSetUpWithEnvVars(), $standardMessage];
        yield 'missing base url' => [
            $buildSetUpWithEnvVars([EnvShlinkConfig::API_KEY_ENV_VAR => 'SOME_VALUE']),
            $standardMessage,
        ];
        yield 'missing api key' => [
            $buildSetUpWithEnvVars([EnvShlinkConfig::BASE_URL_ENV_VAR => 'SOME_VALUE']),
            $standardMessage,
        ];
        yield 'invalid api version' => [$buildSetUpWithEnvVars([
            EnvShlinkConfig::BASE_URL_ENV_VAR => 'SOME_VALUE',
            EnvShlinkConfig::API_KEY_ENV_VAR => 'SOME_VALUE',
            EnvShlinkConfig::VERSION_ENV_VAR => '1',
        ]), 'Provided version "1" is invalid. Expected one of ["2", "3"]'];
    }

    /** @test */
    public function configIsProperlyInitializedIfBothEnvVarsAreSet(): void
    {
        putenv(sprintf('%s=API_KEY', EnvShlinkConfig::API_KEY_ENV_VAR));
        putenv(sprintf('%s=BASE_URL', EnvShlinkConfig::BASE_URL_ENV_VAR));
        putenv(sprintf('%s=3', EnvShlinkConfig::VERSION_ENV_VAR));

        $config = EnvShlinkConfig::fromEnv();

        self::assertEquals('API_KEY', $config->apiKey());
        self::assertEquals('BASE_URL', $config->baseUrl());
        self::assertEquals(ApiVersion::V3, $config->version());
    }
}
