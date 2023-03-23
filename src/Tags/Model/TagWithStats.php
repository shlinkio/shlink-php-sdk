<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use Shlinkio\Shlink\SDK\Visits\Model\VisitsCount;

final class TagWithStats
{
    /** @deprecated Use $visitsSummary->total instead */
    public readonly int $visitsCount;

    private function __construct(
        public readonly string $tag,
        public readonly int $shortUrlsCount,
        public readonly VisitsCount $visitsSummary,
        int $visitsCount,
    ) {
        $this->visitsCount = $visitsCount;
    }

    public static function fromArray(array $payload): self
    {
        $visitsCount = $payload['visitsCount'] ?? 0;

        return new self(
            tag: $payload['tag'] ?? '',
            shortUrlsCount: $payload['shortUrlsCount'] ?? 0,
            visitsSummary: VisitsCount::fromArrayWithFallback($payload['visitsSummary'] ?? [], $visitsCount),
            visitsCount: $visitsCount,
        );
    }
}
