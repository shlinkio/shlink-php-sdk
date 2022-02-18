<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Closure;
use Shlinkio\Shlink\SDK\Model\ListEndpointIterator;

final class VisitsList extends ListEndpointIterator
{
    private const ITEMS_PER_PAGE = 1000;

    private function __construct(Closure $visitsLoader, Closure $itemMapper)
    {
        parent::__construct($visitsLoader, $itemMapper, self::ITEMS_PER_PAGE);
    }

    public static function forTupleLoader(Closure $visitsLoader): self
    {
        return new self($visitsLoader, static fn (array $value) => Visit::fromArray($value));
    }

    public static function forOrphanVisitsTupleLoader(Closure $visitsLoader): self
    {
        return new self($visitsLoader, static fn (array $value) => OrphanVisit::fromArray($value));
    }
}
