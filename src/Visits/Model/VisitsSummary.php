<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Countable;

final class VisitsSummary implements Countable
{
    /** @deprecated Use $nonOrphanVisits->total instead */
    public readonly int $visitsCount;
    /** @deprecated Use $orphanVisits->total instead */
    public readonly int $orphanVisitsCount;

    private function __construct(
        public readonly VisitsCount $nonOrphanVisits,
        public readonly VisitsCount $orphanVisits,
        int $visitsCount,
        int $orphanVisitsCount,
    ) {
        $this->visitsCount = $visitsCount;
        $this->orphanVisitsCount = $orphanVisitsCount;
    }

    public static function fromArray(array $payload): self
    {
        $visitsCount = $payload['visitsCount'] ?? 0;
        $orphanVisitsCount = $payload['orphanVisitsCount'] ?? 0;

        return new self(
            nonOrphanVisits: VisitsCount::fromArrayWithFallback($payload['nonOrphanVisits'] ?? [], $visitsCount),
            orphanVisits: VisitsCount::fromArrayWithFallback($payload['orphanVisits'] ?? [], $orphanVisitsCount),
            visitsCount: $visitsCount,
            orphanVisitsCount: $orphanVisitsCount,
        );
    }

    public function count(): int
    {
        return $this->nonOrphanVisits->total + $this->orphanVisits->total;
    }
}
