<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Config;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\ArrayShlinkConfig;
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;

class ArrayShlinkConfigTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideInvalidData
     */
    public function exceptionIsThrownIfProvidedDataIsInvalid(array $rawConfig): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            'Provided array is missing "baseUrl" and/or "apiKey" props, or their values are invalid. Make sure both '
            . 'are set with strings.',
        );

        ArrayShlinkConfig::fromArray($rawConfig);
    }

    public function provideInvalidData(): iterable
    {
        yield 'both missing' => [[]];
        yield 'missing api key' => [[ArrayShlinkConfig::BASE_URL_PROP => 'foo']];
        yield 'missing base url' => [[ArrayShlinkConfig::API_KEY_PROP => 'bar']];
    }

    /** @test */
    public function expectedApiKeyAndBaseUrlAreSet(): void
    {
        $rawConfig = [
            ArrayShlinkConfig::BASE_URL_PROP => 'foo',
            ArrayShlinkConfig::API_KEY_PROP => 'bar',
        ];

        $config = ArrayShlinkConfig::fromArray($rawConfig);

        self::assertEquals('foo', $config->baseUrl());
        self::assertEquals('bar', $config->apiKey());
    }
}
