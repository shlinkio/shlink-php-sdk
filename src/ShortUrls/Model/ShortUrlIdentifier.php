<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

final class ShortUrlIdentifier
{
    private function __construct(public readonly string $shortCode, public readonly ?string $domain)
    {
    }

    public static function fromShortCode(string $shortCode): self
    {
        return new self($shortCode, null);
    }

    public static function fromShortCodeAndDomain(string $shortCode, string $domain): self
    {
        return new self($shortCode, $domain);
    }

    public static function fromShortUrl(ShortUrl $shortUrl): self
    {
        return new self($shortUrl->shortCode, $shortUrl->domain);
    }

    /**
     * @return array{string, array}
     */
    public function toShortCodeAndQuery(array $baseQuery = []): array
    {
        if ($this->domain !== null) {
            $baseQuery['domain'] = $this->domain;
        }

        return [$this->shortCode, $baseQuery];
    }
}
