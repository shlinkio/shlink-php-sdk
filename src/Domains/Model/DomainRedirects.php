<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

final readonly class DomainRedirects
{
    private function __construct(
        public string|null $baseUrlRedirect,
        public string|null $regularNotFoundRedirect,
        public string|null $invalidShortUrlRedirect,
    ) {}

    /**
     * @param array{baseUrlRedirect?: string, regular404Redirect?: string, invalidShortUrlRedirect?: string} $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            $payload[DomainRedirectProps::BASE_URL->value] ?? null,
            $payload[DomainRedirectProps::REGULAR_NOT_FOUND->value] ?? null,
            $payload[DomainRedirectProps::INVALID_SHORT_URL->value] ?? null,
        );
    }
}
