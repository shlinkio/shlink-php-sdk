<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Config;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\ArrayShlinkConfig;
use Shlinkio\Shlink\SDK\Config\EnvShlinkConfig;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

use function putenv;
use function sprintf;

class ShlinkConfigTest extends TestCase
{
    /**
     * @param callable(string, string): ShlinkConfig $createConfig
     */
    #[Test, DataProvider('provideMethods')]
    public function configIsInitializedForV2(callable $createConfig, ApiVersion $expectedVersion): void
    {
        $config = $createConfig('baseUrl', 'apiKey');

        self::assertEquals('baseUrl', $config->baseUrl());
        self::assertEquals('apiKey', $config->apiKey());
        self::assertEquals($expectedVersion, $config->version());
    }

    public static function provideMethods(): iterable
    {
        yield 'deprecated' => [ShlinkConfig::fromBaseUrlAndApiKey(...), ApiVersion::V2];
        yield 'v2' => [ShlinkConfig::fromV2BaseUrlAndApiKey(...), ApiVersion::V2];
        yield 'v3' => [ShlinkConfig::fromV3BaseUrlAndApiKey(...), ApiVersion::V3];
    }

    #[Test]
    public function createsAnEnvShlinkConfigFromEnv(): void
    {
        putenv(sprintf('%s=API_KEY', EnvShlinkConfig::API_KEY_ENV_VAR));
        putenv(sprintf('%s=BASE_URL', EnvShlinkConfig::BASE_URL_ENV_VAR));

        $config = ShlinkConfig::fromEnv();

        self::assertInstanceOf(EnvShlinkConfig::class, $config);
        self::assertEquals('BASE_URL', $config->baseUrl());
        self::assertEquals('API_KEY', $config->apiKey());

        putenv(sprintf('%s=', EnvShlinkConfig::API_KEY_ENV_VAR));
        putenv(sprintf('%s=', EnvShlinkConfig::BASE_URL_ENV_VAR));
    }

    #[Test]
    public function createsAnArrayShlinkConfigFromArray(): void
    {
        $rawConfig = [
            ArrayShlinkConfig::BASE_URL_PROP => 'foo',
            ArrayShlinkConfig::API_KEY_PROP => 'bar',
        ];

        $config = ShlinkConfig::fromArray($rawConfig);

        self::assertInstanceOf(ArrayShlinkConfig::class, $config);
        self::assertEquals('foo', $config->baseUrl());
        self::assertEquals('bar', $config->apiKey());
    }
}
