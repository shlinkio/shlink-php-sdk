<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Model;

use Closure;
use Countable;
use IteratorAggregate;
use Traversable;

abstract class ListEndpointIterator implements IteratorAggregate, Countable
{
    private array $currentData = [];
    private array $currentPagination = [];

    /**
     * @param Closure(int $page, int $itemsPerPage): array{array, array} $pageLoader
     * @param Closure(mixed): mixed $itemMapper
     */
    public function __construct(private Closure $pageLoader, private Closure $itemMapper, private int $itemsPerPage)
    {
        $this->loadPage(1);
    }

    public function getIterator(): Traversable
    {
        yield from $this->getFullList();
    }

    private function getFullList(): iterable
    {
        $currentPage = $this->currentPagination['currentPage'] ?? -1;

        foreach ($this->currentData as $index => $value) {
            yield $this->calculateIndex($index, $currentPage) => ($this->itemMapper)($value);
        }

        $isLastPage = $currentPage < 0 || $currentPage === $this->currentPagination['pagesCount'];
        if (! $isLastPage) {
            $this->loadPage(($currentPage ?? -1) + 1);
            yield from $this->getFullList();
        }
    }

    private function calculateIndex(int $index, int $currentPage): int
    {
        return ($currentPage - 1) * $this->itemsPerPage + $index;
    }

    public function count(): int
    {
        return $this->currentPagination['totalItems'] ?? 0;
    }

    private function loadPage(int $page): void
    {
        [$data, $pagination] = ($this->pageLoader)($page, $this->itemsPerPage);

        $this->currentData = $data;
        $this->currentPagination = $pagination;
    }
}
