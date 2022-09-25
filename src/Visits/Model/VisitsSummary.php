<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Countable;

final class VisitsSummary implements Countable
{
    private function __construct(public readonly int $visitsCount, public readonly int $orphanVisitsCount)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['visitsCount'] ?? 0,
            $payload['orphanVisitsCount'] ?? 0,
        );
    }

    public function count(): int
    {
        return $this->visitsCount + $this->orphanVisitsCount;
    }
}
