<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeImmutable;
use DateTimeInterface;

final class Visit implements VisitInterface
{
    private function __construct(
        private string $referer,
        private DateTimeInterface $dateTime,
        private string $userAgent,
        private bool $potentialBot,
        private ?VisitLocation $location,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['referer'] ?? '',
            DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload['date']), // @phpstan-ignore-line
            $payload['userAgent'] ?? '',
            $payload['potentialBot'] ?? false,
            isset($payload['visitLocation']) ? VisitLocation::fromArray($payload['visitLocation']) : null,
        );
    }

    public function referer(): string
    {
        return $this->referer;
    }

    public function dateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function userAgent(): string
    {
        return $this->userAgent;
    }

    public function potentialBot(): bool
    {
        return $this->potentialBot;
    }

    public function location(): ?VisitLocation
    {
        return $this->location;
    }
}
