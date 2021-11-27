<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

final class VisitLocation
{
    private function __construct(
        private string $countryCode,
        private string $countryName,
        private string $regionName,
        private string $cityName,
        private float $latitude,
        private float $longitude,
        private string $timezone,
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

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function countryName(): string
    {
        return $this->countryName;
    }

    public function regionName(): string
    {
        return $this->regionName;
    }

    public function cityName(): string
    {
        return $this->cityName;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function timezone(): string
    {
        return $this->timezone;
    }
}
