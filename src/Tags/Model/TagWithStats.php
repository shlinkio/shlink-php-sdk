<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

final readonly class TagWithStats
{
    /**
     * @param int<0, max> $shortUrlsCount
     */
    private function __construct(public string $tag, public int $shortUrlsCount, public VisitsSummary $visitsSummary) {}

    /**
     * @param array{
     *     tag?: string,
     *     shortUrlsCount?: int<0, max>,
     *     visitsSummary?: array{total?: int<0, max>, nonBots?: int<0, max>, bots?: int<0, max>}
     * } $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tag: $payload['tag'] ?? '',
            shortUrlsCount: $payload['shortUrlsCount'] ?? 0,
            visitsSummary: VisitsSummary::fromArray($payload['visitsSummary'] ?? []),
        );
    }
}
