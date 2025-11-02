<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Countable;

final readonly class VisitsOverview implements Countable
{
    private function __construct(public VisitsSummary $nonOrphanVisits, public VisitsSummary $orphanVisits)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            nonOrphanVisits: VisitsSummary::fromArray($payload['nonOrphanVisits'] ?? []),
            orphanVisits: VisitsSummary::fromArray($payload['orphanVisits'] ?? []),
        );
    }

    public function count(): int
    {
        return $this->nonOrphanVisits->total + $this->orphanVisits->total;
    }
}
