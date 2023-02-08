<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Config;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\ArrayShlinkConfig;
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

class ArrayShlinkConfigTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideInvalidData
     */
    public function exceptionIsThrownIfProvidedDataIsInvalid(
        array $rawConfig,
        string $expectedMessage,
    ): void {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage($expectedMessage);

        ArrayShlinkConfig::fromArray($rawConfig);
    }

    public static function provideInvalidData(): iterable
    {
        $standardMessage = 'Provided array is missing "baseUrl" and/or "apiKey" props, or their values are invalid. '
            . 'Make sure both are set with strings.';

        yield 'both missing' => [[], $standardMessage];
        yield 'missing api key' => [[ArrayShlinkConfig::BASE_URL_PROP => 'foo'], $standardMessage];
        yield 'missing base url' => [[ArrayShlinkConfig::API_KEY_PROP => 'bar'], $standardMessage];
        yield 'invalid version' => [[
            ArrayShlinkConfig::BASE_URL_PROP => 'foo',
            ArrayShlinkConfig::API_KEY_PROP => 'bar',
            ArrayShlinkConfig::VERSION_PROP => '4',
        ], 'Provided version "4" is invalid. Expected one of ["2", "3"]'];
    }

    /**
     * @test
     * @dataProvider provideVersions
     */
    public function expectedParamsAreSet(?string $version, ApiVersion $expectedVersion): void
    {
        $rawConfig = [
            ArrayShlinkConfig::BASE_URL_PROP => 'foo',
            ArrayShlinkConfig::API_KEY_PROP => 'bar',
            ArrayShlinkConfig::VERSION_PROP => $version,
        ];

        $config = ArrayShlinkConfig::fromArray($rawConfig);

        self::assertEquals('foo', $config->baseUrl());
        self::assertEquals('bar', $config->apiKey());
        self::assertEquals($expectedVersion, $config->version());
    }

    public static function provideVersions(): iterable
    {
        yield 'explicit version 3' => ['3', ApiVersion::V3];
        yield 'explicit version 2' => ['2', ApiVersion::V2];
        yield 'no version' => [null, ApiVersion::V2];
        yield 'empty version' => ['', ApiVersion::V2];
    }
}
