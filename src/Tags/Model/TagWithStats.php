<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

final readonly class TagWithStats
{
    private function __construct(public string $tag, public int $shortUrlsCount, public VisitsSummary $visitsSummary)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            tag: $payload['tag'] ?? '',
            shortUrlsCount: $payload['shortUrlsCount'] ?? 0,
            visitsSummary: VisitsSummary::fromArray($payload['visitsSummary'] ?? []),
        );
    }
}
