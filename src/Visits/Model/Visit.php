<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class Visit implements VisitInterface
{
    private function __construct(
        private string $referer,
        private DateTimeInterface $date,
        private string $userAgent,
        private bool $potentialBot,
        private string $visitedUrl,
        private string|null $redirectUrl,
        private VisitLocation|null $location,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            referer: $payload['referer'] ?? '',
            // @phpstan-ignore-next-line
            date: DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload['date']),
            userAgent: $payload['userAgent'] ?? '',
            potentialBot: $payload['potentialBot'] ?? false,
            visitedUrl: $payload['visitedUrl'] ?? '',
            redirectUrl: $payload['redirectUrl'] ?? null,
            location: isset($payload['visitLocation']) ? VisitLocation::fromArray($payload['visitLocation']) : null,
        );
    }

    public function referer(): string
    {
        return $this->referer;
    }

    public function date(): DateTimeInterface
    {
        return $this->date;
    }

    public function userAgent(): string
    {
        return $this->userAgent;
    }

    public function potentialBot(): bool
    {
        return $this->potentialBot;
    }

    public function location(): VisitLocation|null
    {
        return $this->location;
    }

    public function visitedUrl(): string
    {
        return $this->visitedUrl;
    }

    public function redirectUrl(): string|null
    {
        return $this->redirectUrl;
    }
}
