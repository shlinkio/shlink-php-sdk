<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeInterface;

final class OrphanVisit implements VisitInterface
{
    private function __construct(
        private readonly Visit $visit,
        private readonly string $visitedUrl,
        private readonly OrphanVisitType $type,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            Visit::fromArray($payload),
            $payload['visitedUrl'] ?? '',
            OrphanVisitType::tryFrom($payload['type'] ?? '') ?? OrphanVisitType::REGULAR_NOT_FOUND,
        );
    }

    public function referer(): string
    {
        return $this->visit->referer();
    }

    public function date(): DateTimeInterface
    {
        return $this->visit->date();
    }

    public function userAgent(): string
    {
        return $this->visit->userAgent();
    }

    public function potentialBot(): bool
    {
        return $this->visit->potentialBot();
    }

    public function location(): ?VisitLocation
    {
        return $this->visit->location();
    }

    public function visitedUrl(): string
    {
        return $this->visitedUrl;
    }

    public function type(): OrphanVisitType
    {
        return $this->type;
    }
}
