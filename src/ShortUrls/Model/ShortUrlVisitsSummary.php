<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

/**
 * @deprecated Use VisitsCount instead
 */
class ShortUrlVisitsSummary
{
    private function __construct(
        public readonly int $total,
        public readonly ?int $nonBots,// These are optional for Shlink <3.4.0
        public readonly ?int $bots,// These are optional for Shlink <3.4.0
    ) {
    }

    public static function fromArrayWithFallback(array $payload, int $fallbackTotal): static
    {
        // @phpstan-ignore-next-line
        return new static(
            $payload['total'] ?? $fallbackTotal,
            $payload['nonBots'] ?? null,
            $payload['bots'] ?? null,
        );
    }
}
