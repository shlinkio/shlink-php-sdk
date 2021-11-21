<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Model;

use Closure;
use Countable;
use IteratorAggregate;

abstract class ListEndpointIterator implements IteratorAggregate, Countable
{
    private array $currentData = [];
    private array $currentPagination = [];

    public function __construct(private Closure $pageLoader, private Closure $itemMapper, private int $itemsPerPage)
    {
        $this->loadPage(1);
    }

    public function getIterator(): iterable
    {
        yield from $this->getFullList();
    }

    private function getFullList(): iterable
    {
        $currentPage = $this->currentPagination['currentPage'] ?? -1;

        // We cannot simply "yield from" in order to keep consistent indexes
        foreach ($this->currentData as $index => $value) {
            yield $index + (($currentPage - 1) * $this->itemsPerPage) => ($this->itemMapper)($value);
        }

        $isLastPage = $currentPage < 0 || $currentPage === $this->currentPagination['pagesCount'];
        if (! $isLastPage) {
            $this->loadPage(($currentPage ?? -1) + 1);
            yield from $this->getFullList();
        }
    }

    public function count(): int
    {
        return $this->currentPagination['totalItems'] ?? 0;
    }

    private function loadPage(int $page): void
    {
        [$data, $pagination] = ($this->pageLoader)($page);

        $this->currentData = $data;
        $this->currentPagination = $pagination;
    }
}
