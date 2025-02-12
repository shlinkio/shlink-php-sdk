<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

final readonly class ShortUrl
{
    /** @deprecated Use $visitsSummary->total instead */
    public int $visitsCount;
    /** @deprecated Not returned by Shlink 4.0.0 */
    public DeviceLongUrls|null $deviceLongUrls;

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
        DeviceLongUrls|null $deviceLongUrls,
    ) {
        // Not using constructor property promotion here so that we can mark these props as deprecated
        $this->visitsCount = $visitsSummary->total;
        $this->deviceLongUrls = $deviceLongUrls;
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
            domain: $payload['domain'] ?? null,
            title: $payload['title'] ?? null,
            crawlable: $payload['crawlable'] ?? false,
            forwardQuery: $payload['forwardQuery'] ?? false,
            hasRedirectRules: $payload['hasRedirectRules'] ?? null,
            tags: $payload['tags'] ?? [],
            meta: ShortUrlMeta::fromArray($payload['meta'] ?? []),
            visitsSummary: VisitsSummary::fromArrayWithFallback($payload['visitsSummary'] ?? [], $visitsCount),
            deviceLongUrls: isset($payload['deviceLongUrls'])
                ? DeviceLongUrls::fromArray($payload['deviceLongUrls'])
                : null,
        );
    }

    public function identifier(): ShortUrlIdentifier
    {
        return ShortUrlIdentifier::fromShortUrl($this);
    }
}
