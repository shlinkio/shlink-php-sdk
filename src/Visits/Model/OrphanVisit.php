<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeInterface;
use Override;

final readonly class OrphanVisit implements VisitInterface
{
    private function __construct(private Visit $visit, private OrphanVisitType $type) {}

    /**
     * @param array{
     *     type?: string,
     *     referer?: string,
     *     date: string,
     *     userAgent?: string,
     *     potentialBot?: bool,
     *     visitedUrl?: string,
     *     redirectUrl?: string,
     *     visitLocation?: array,
     * } $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            visit: Visit::fromArray($payload),
            type: OrphanVisitType::tryFrom($payload['type'] ?? '') ?? OrphanVisitType::REGULAR_NOT_FOUND,
        );
    }

    #[Override]
    public function referer(): string
    {
        return $this->visit->referer();
    }

    #[Override]
    public function date(): DateTimeInterface
    {
        return $this->visit->date();
    }

    #[Override]
    public function userAgent(): string
    {
        return $this->visit->userAgent();
    }

    #[Override]
    public function potentialBot(): bool
    {
        return $this->visit->potentialBot();
    }

    #[Override]
    public function location(): VisitLocation|null
    {
        return $this->visit->location();
    }

    #[Override]
    public function visitedUrl(): string
    {
        return $this->visit->visitedUrl();
    }

    #[Override]
    public function redirectUrl(): string|null
    {
        return $this->visit->redirectUrl();
    }

    public function type(): OrphanVisitType
    {
        return $this->type;
    }
}
