<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Countable;

final readonly class VisitsOverview implements Countable
{
    /** @deprecated Use $nonOrphanVisits->total instead */
    public int $visitsCount;
    /** @deprecated Use $orphanVisits->total instead */
    public int $orphanVisitsCount;

    private function __construct(public VisitsSummary $nonOrphanVisits, public VisitsSummary $orphanVisits)
    {
        $this->visitsCount = $nonOrphanVisits->total;
        $this->orphanVisitsCount = $orphanVisits->total;
    }

    public static function fromArray(array $payload): self
    {
        $visitsCount = $payload['visitsCount'] ?? 0;
        $orphanVisitsCount = $payload['orphanVisitsCount'] ?? 0;

        return new self(
            nonOrphanVisits: VisitsSummary::fromArrayWithFallback($payload['nonOrphanVisits'] ?? [], $visitsCount),
            orphanVisits: VisitsSummary::fromArrayWithFallback($payload['orphanVisits'] ?? [], $orphanVisitsCount),
        );
    }

    public function count(): int
    {
        return $this->nonOrphanVisits->total + $this->orphanVisits->total;
    }
}
