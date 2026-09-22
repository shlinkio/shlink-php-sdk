<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

final readonly class VisitLocation
{
    private function __construct(
        public string $countryCode,
        public string $countryName,
        public string $regionName,
        public string $cityName,
        public float $latitude,
        public float $longitude,
        public string $timezone,
    ) {}

    /**
     * @param array{
     *     countryCode?: string,
     *     countryName?: string,
     *     regionName?: string,
     *     cityName?: string,
     *     latitude?: float,
     *     longitude?: float,
     *     timezone?: string,
     * } $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['countryCode'] ?? '',
            $payload['countryName'] ?? '',
            $payload['regionName'] ?? '',
            $payload['cityName'] ?? '',
            $payload['latitude'] ?? 0.0,
            $payload['longitude'] ?? 0.0,
            $payload['timezone'] ?? '',
        );
    }
}
