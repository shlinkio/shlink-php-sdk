<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use Closure;
use Countable;
use IteratorAggregate;

final class ShortUrlsList implements IteratorAggregate, Countable
{
    private array $currentData = [];
    private array $currentPagination = [];

    public function __construct(private Closure $pageLoader)
    {
        $this->loadPage(1);
    }

    public function getIterator(): iterable
    {
        yield from $this->getFullList();
    }

    private function getFullList(): iterable
    {
        yield from $this->currentData;

        $currentPage = $this->currentPagination['currentPage'];
        $isLastPage = $currentPage === $this->currentPagination['pagesCount'];

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
        $body = ($this->pageLoader)($page);

        $this->currentData = $body['shortUrls']['data'] ?? [];
        $this->currentPagination = $body['shortUrls']['pagination'] ?? [];
    }
}
