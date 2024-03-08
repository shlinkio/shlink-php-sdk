<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

final readonly class TagWithStats
{
    /** @deprecated Use $visitsSummary->total instead */
    public int $visitsCount;

    private function __construct(public string $tag, public int $shortUrlsCount, public VisitsSummary $visitsSummary)
    {
        $this->visitsCount = $visitsSummary->total;
    }

    public static function fromArray(array $payload): self
    {
        $visitsCount = $payload['visitsCount'] ?? 0;

        return new self(
            tag: $payload['tag'] ?? '',
            shortUrlsCount: $payload['shortUrlsCount'] ?? 0,
            visitsSummary: VisitsSummary::fromArrayWithFallback($payload['visitsSummary'] ?? [], $visitsCount),
        );
    }
}
