<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeInterface;

final class OrphanVisit implements VisitInterface
{
    public const TYPE_BASE_URL = 'base_url';
    public const TYPE_REGULAR_NOT_FOUND = 'regular_404';
    public const TYPE_INVALID_SHORT_URL = 'invalid_short_url';

    private function __construct(private Visit $visit, private string $visitedUrl, private string $type)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            Visit::fromArray($payload),
            $payload['visitedUrl'] ?? '',
            $payload['type'] ?? '',
        );
    }

    public function referer(): string
    {
        return $this->visit->referer();
    }

    public function dateTime(): DateTimeInterface
    {
        return $this->visit->dateTime();
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

    public function type(): string
    {
        return $this->type;
    }
}
