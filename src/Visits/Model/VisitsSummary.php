<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

final readonly class VisitsSummary
{
    /**
     * @param int<0, max> $total
     * @param int<0, max> $nonBots
     * @param int<0, max> $bots
     */
    private function __construct(public int $total, public int $nonBots, public int $bots)
    {
    }

    public static function fromArray(array $payload): self
    {
        return self::fromArrayWithFallback($payload);
    }

    /** @deprecated Use self::fromArray instead */
    public static function fromArrayWithFallback(array $payload, int $fallbackTotal = 0): self
    {
        return new self(
            $payload['total'] ?? $fallbackTotal,
            $payload['nonBots'] ?? $fallbackTotal,
            $payload['bots'] ?? $fallbackTotal,
        );
    }
}
