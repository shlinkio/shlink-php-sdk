<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

final readonly class VisitsSummary
{
    private function __construct(
        public int $total,
        public int|null $nonBots,// These are optional for Shlink <3.4.0
        public int|null $bots,// These are optional for Shlink <3.4.0
    ) {
    }

    public static function fromArrayWithFallback(array $payload, int $fallbackTotal): self
    {
        return new self(
            $payload['total'] ?? $fallbackTotal,
            $payload['nonBots'] ?? null,
            $payload['bots'] ?? null,
        );
    }
}
