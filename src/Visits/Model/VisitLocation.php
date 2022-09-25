<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

final class VisitLocation
{
    private function __construct(
        public readonly string $countryCode,
        public readonly string $countryName,
        public readonly string $regionName,
        public readonly string $cityName,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $timezone,
    ) {
    }

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
