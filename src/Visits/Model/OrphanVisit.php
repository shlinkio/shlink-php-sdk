<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeInterface;

final readonly class OrphanVisit implements VisitInterface
{
    private function __construct(private Visit $visit, private string $visitedUrl, private OrphanVisitType $type)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            visit: Visit::fromArray($payload),
            visitedUrl: $payload['visitedUrl'] ?? '',
            type: OrphanVisitType::tryFrom($payload['type'] ?? '') ?? OrphanVisitType::REGULAR_NOT_FOUND,
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

    public function location(): VisitLocation|null
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
