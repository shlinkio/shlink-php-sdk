<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Closure;
use Shlinkio\Shlink\SDK\Model\ListEndpointIterator;

final class VisitsList extends ListEndpointIterator
{
    public const ITEMS_PER_PAGE = 1000;

    private function __construct(private Closure $visitsLoader)
    {
        parent::__construct(
            $this->visitsLoader,
            static fn (array $value) => $value,
            self::ITEMS_PER_PAGE,
        );
    }

    public static function forTupleLoader(Closure $visitsLoader): self
    {
        return new self($visitsLoader);
    }
}
