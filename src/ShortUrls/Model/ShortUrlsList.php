<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use Closure;
use Shlinkio\Shlink\SDK\Model\ListEndpointIterator;

final class ShortUrlsList extends ListEndpointIterator
{
    public const ITEMS_PER_PAGE = 20;

    private function __construct(private Closure $pageLoader)
    {
        parent::__construct($this->pageLoader, static fn (array $value) => $value, self::ITEMS_PER_PAGE);
    }

    public static function forTupleLoader(Closure $pageLoader): self
    {
        return new self($pageLoader);
    }
}
