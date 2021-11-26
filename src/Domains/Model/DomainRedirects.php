<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

final class DomainRedirects
{
    private function __construct(
        private ?string $baseUrlRedirect,
        private ?string $regularNotFoundRedirect,
        private ?string $invalidShortUrlRedirect,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload[DomainRedirectProps::BASE_URL] ?? null,
            $payload[DomainRedirectProps::REGULAR_NOT_FOUND] ?? null,
            $payload[DomainRedirectProps::INVALID_SHORT_URL] ?? null,
        );
    }

    public function baseUrlRedirect(): ?string
    {
        return $this->baseUrlRedirect;
    }

    public function regularNotFoundRedirect(): ?string
    {
        return $this->regularNotFoundRedirect;
    }

    public function invalidShortUrlRedirect(): ?string
    {
        return $this->invalidShortUrlRedirect;
    }
}
