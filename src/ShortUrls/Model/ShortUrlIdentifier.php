<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

final class ShortUrlIdentifier
{
    public function __construct(private string $shortCode, private ?string $domain)
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

    public function shortCode(): string
    {
        return $this->shortCode;
    }

    public function domain(): ?string
    {
        return $this->domain;
    }
}
