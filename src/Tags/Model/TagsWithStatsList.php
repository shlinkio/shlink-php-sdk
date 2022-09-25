<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use Closure;
use Shlinkio\Shlink\SDK\Model\ListEndpointIterator;

final class TagsWithStatsList extends ListEndpointIterator
{
    private const PAGINATED_ITEMS_PER_PAGE = 30;
    private const NON_PAGINATED_ITEMS_PER_PAGE = -1;

    private function __construct(Closure $pageLoader, int $itemsPerPage)
    {
        parent::__construct($pageLoader, static fn (array $value) => TagWithStats::fromArray($value), $itemsPerPage);
    }

    /**
     * @param Closure(int $page, int $itemsPerPage): array{array, array} $pageLoader
     */
    public static function forTupleLoader(Closure $pageLoader): self
    {
        return new self($pageLoader, self::PAGINATED_ITEMS_PER_PAGE);
    }

    /**
     * @param Closure(int $page, int $itemsPerPage): array{array, array} $pageLoader
     */
    public static function forNonPaginatedTupleLoader(Closure $pageLoader): self
    {
        return new self($pageLoader, self::NON_PAGINATED_ITEMS_PER_PAGE);
    }
}
