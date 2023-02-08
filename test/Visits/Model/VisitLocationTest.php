<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Visits\Model;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Visits\Model\VisitLocation;

class VisitLocationTest extends TestCase
{
    /**
     * @test
     * @dataProvider providePayloads
     */
    public function properObjectIsCreatedFromArray(
        array $payload,
        string $expectedCountryCode,
        string $expectedCountryName,
        string $expectedRegionName,
        string $expectedCityName,
        float $expectedLatitude,
        float $expectedLongitude,
        string $expectedTimezone,
    ): void {
        $visitLocation = VisitLocation::fromArray($payload);

        self::assertEquals($expectedCountryCode, $visitLocation->countryCode);
        self::assertEquals($expectedCountryName, $visitLocation->countryName);
        self::assertEquals($expectedRegionName, $visitLocation->regionName);
        self::assertEquals($expectedCityName, $visitLocation->cityName);
        self::assertEquals($expectedLatitude, $visitLocation->latitude);
        self::assertEquals($expectedLongitude, $visitLocation->longitude);
        self::assertEquals($expectedTimezone, $visitLocation->timezone);
    }

    public static function providePayloads(): iterable
    {
        yield 'empty payload' => [[], '', '', '', '', 0.0, 0.0, ''];
        yield 'full payload' => [
            [
                'countryCode' => 'countryCode',
                'countryName' => 'countryName',
                'regionName' => 'regionName',
                'cityName' => 'cityName',
                'latitude' => 40.5,
                'longitude' => 50.4,
                'timezone' => 'timezone',
            ],
            'countryCode',
            'countryName',
            'regionName',
            'cityName',
            40.5,
            50.4,
            'timezone',
        ];
        yield 'partial payload' => [
            [
                'regionName' => 'regionName',
                'cityName' => 'cityName',
                'longitude' => 50.4,
                'timezone' => 'timezone',
            ],
            '',
            '',
            'regionName',
            'cityName',
            0.0,
            50.4,
            'timezone',
        ];
    }
}
