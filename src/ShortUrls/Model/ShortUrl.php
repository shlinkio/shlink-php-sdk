<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsCount;

final class ShortUrl
{
    /** @deprecated Use $visitsSummary->total instead */
    public readonly int $visitsCount;

    private function __construct(
        public readonly string $shortCode,
        public readonly string $shortUrl,
        public readonly string $longUrl,
        public readonly DateTimeInterface $dateCreated,
        int $visitsCount,
        public readonly ?string $domain,
        public readonly ?string $title,
        public readonly bool $crawlable,
        public readonly bool $forwardQuery,
        public readonly array $tags,
        public readonly ShortUrlMeta $meta,
        public readonly VisitsCount $visitsSummary,
        public readonly DeviceLongUrls $deviceLongUrls,
    ) {
        $this->visitsCount = $visitsCount;
    }

    public static function fromArray(array $payload): self
    {
        $visitsCount = $payload['visitsCount'] ?? 0;

        return new self(
            shortCode: $payload['shortCode'] ?? '',
            shortUrl: $payload['shortUrl'] ?? '',
            longUrl: $payload['longUrl'] ?? '',
            // @phpstan-ignore-next-line
            dateCreated: DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload['dateCreated']),
            visitsCount: $visitsCount,
            domain: $payload['domain'] ?? null,
            title: $payload['title'] ?? null,
            crawlable: $payload['crawlable'] ?? false,
            forwardQuery: $payload['forwardQuery'] ?? false,
            tags: $payload['tags'] ?? [],
            meta: ShortUrlMeta::fromArray($payload['meta'] ?? []),
            visitsSummary: VisitsCount::fromArrayWithFallback($payload['visitsSummary'] ?? [], $visitsCount),
            deviceLongUrls: DeviceLongUrls::fromArray($payload['deviceLongUrls'] ?? []),
        );
    }
}
