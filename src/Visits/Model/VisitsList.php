<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Closure;
use Shlinkio\Shlink\SDK\Model\ListEndpointIterator;

/**
 * @template VisitType of VisitInterface
 * @extends ListEndpointIterator<VisitType>
 */
final class VisitsList extends ListEndpointIterator
{
    private const ITEMS_PER_PAGE = 1000;

    /**
     * @param Closure(int $page, int $itemsPerPage): array{array, array} $visitsLoader
     * @param Closure(array): VisitType $itemMapper
     */
    private function __construct(Closure $visitsLoader, Closure $itemMapper)
    {
        parent::__construct($visitsLoader, $itemMapper, self::ITEMS_PER_PAGE);
    }

    /**
     * @param Closure(int $page, int $itemsPerPage): array{array, array} $visitsLoader
     * @return VisitsList<Visit>
     */
    public static function forTupleLoader(Closure $visitsLoader): self
    {
        return new self($visitsLoader, static fn (array $value) => Visit::fromArray($value));
    }

    /**
     * @param Closure(int $page, int $itemsPerPage): array{array, array} $visitsLoader
     * @return VisitsList<OrphanVisit>
     */
    public static function forOrphanVisitsTupleLoader(Closure $visitsLoader): self
    {
        return new self($visitsLoader, static fn (array $value) => OrphanVisit::fromArray($value));
    }
}
