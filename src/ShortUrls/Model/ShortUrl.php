<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

final readonly class ShortUrl
{
    /**
     * @param $hasRedirectRules - It's `null` for Shlink older than 4.3
     */
    private function __construct(
        public string $shortCode,
        public string $shortUrl,
        public string $longUrl,
        public DateTimeInterface $dateCreated,
        public string|null $domain,
        public string|null $title,
        public bool $crawlable,
        public bool $forwardQuery,
        public bool|null $hasRedirectRules,
        public array $tags,
        public ShortUrlMeta $meta,
        public VisitsSummary $visitsSummary,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            shortCode: $payload['shortCode'] ?? '',
            shortUrl: $payload['shortUrl'] ?? '',
            longUrl: $payload['longUrl'] ?? '',
            // @phpstan-ignore-next-line
            dateCreated: DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload['dateCreated']),
            domain: $payload['domain'] ?? null,
            title: $payload['title'] ?? null,
            crawlable: $payload['crawlable'] ?? false,
            forwardQuery: $payload['forwardQuery'] ?? false,
            hasRedirectRules: $payload['hasRedirectRules'] ?? null,
            tags: $payload['tags'] ?? [],
            meta: ShortUrlMeta::fromArray($payload['meta'] ?? []),
            visitsSummary: VisitsSummary::fromArray($payload['visitsSummary'] ?? []),
        );
    }

    public function identifier(): ShortUrlIdentifier
    {
        return ShortUrlIdentifier::fromShortUrl($this);
    }
}
