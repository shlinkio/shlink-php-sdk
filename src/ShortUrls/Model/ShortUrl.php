<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;

final class ShortUrl
{
    public function __construct(
        private string $shortCode,
        private string $shortUrl,
        private string $longUrl,
        private DateTimeInterface $dateCreated,
        private int $visitsCount,
        private ?string $domain,
        private ?string $title,
        private bool $crawlable,
        private bool $forwardQuery,
        private array $tags,
        private ShortUrlMeta $meta,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['shortCode'] ?? '',
            $payload['shortUrl'] ?? '',
            $payload['longUrl'] ?? '',
            DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload['dateCreated']),
            $payload['visitsCount'] ?? 0,
            $payload['domain'] ?? null,
            $payload['title'] ?? null,
            $payload['crawlable'] ?? false,
            $payload['forwardQuery'] ?? false,
            $payload['tags'] ?? [],
            ShortUrlMeta::fromArray($payload['meta'] ?? []),
        );
    }

    public function shortCode(): string
    {
        return $this->shortCode;
    }

    public function shortUrl(): string
    {
        return $this->shortUrl;
    }

    public function longUrl(): string
    {
        return $this->longUrl;
    }

    public function dateCreated(): DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function visitsCount(): int
    {
        return $this->visitsCount;
    }

    public function domain(): ?string
    {
        return $this->domain;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function crawlable(): bool
    {
        return $this->crawlable;
    }

    public function forwardQuery(): bool
    {
        return $this->forwardQuery;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function meta(): ShortUrlMeta
    {
        return $this->meta;
    }
}
