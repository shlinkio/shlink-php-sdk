<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Countable;

final readonly class VisitsDeletion implements Countable
{
    /**
     * @param int<0, max> $deletedVisits
     */
    private function __construct(public int $deletedVisits)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self($payload['deletedVisits'] ?? 0);
    }

    public function count(): int
    {
        return $this->deletedVisits;
    }
}
