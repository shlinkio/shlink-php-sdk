<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Config;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\ArrayShlinkConfig;
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

class ArrayShlinkConfigTest extends TestCase
{
    #[Test, DataProvider('provideInvalidData')]
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
            ArrayShlinkConfig::VERSION_PROP => '2',
        ], 'Provided version "2" is invalid. Expected one of ["3"]'];
    }

    #[Test]
    public function expectedParamsAreSet(): void
    {
        $rawConfig = [
            ArrayShlinkConfig::BASE_URL_PROP => 'foo',
            ArrayShlinkConfig::API_KEY_PROP => 'bar',
            ArrayShlinkConfig::VERSION_PROP => '3',
        ];

        $config = ArrayShlinkConfig::fromArray($rawConfig);

        self::assertEquals('foo', $config->baseUrl());
        self::assertEquals('bar', $config->apiKey());
        self::assertEquals(ApiVersion::V3, $config->version());
    }
}
