<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;

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
        public readonly ShortUrlVisitsSummary $visitsSummary,
    ) {
        $this->visitsCount = $visitsCount;
    }

    public static function fromArray(array $payload): self
    {
        $visitsCount = $payload['visitsCount'] ?? 0;

        return new self(
            $payload['shortCode'] ?? '',
            $payload['shortUrl'] ?? '',
            $payload['longUrl'] ?? '',
            // @phpstan-ignore-next-line
            DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload['dateCreated']),
                $visitsCount,
            $payload['domain'] ?? null,
            $payload['title'] ?? null,
            $payload['crawlable'] ?? false,
            $payload['forwardQuery'] ?? false,
            $payload['tags'] ?? [],
            ShortUrlMeta::fromArray($payload['meta'] ?? []),
            ShortUrlVisitsSummary::fromArrayWithFallback($payload['visitsSummary'] ?? [], $visitsCount),
        );
    }
}
