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
        return new self(
            total: $payload['total'] ?? 0,
            nonBots: $payload['nonBots'] ?? 0,
            bots: $payload['bots'] ?? 0,
        );
    }
}
