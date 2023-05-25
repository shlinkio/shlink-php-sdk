<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Countable;

final class VisitsDeletion implements Countable
{
    private function __construct(public readonly int $deletedVisits)
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
