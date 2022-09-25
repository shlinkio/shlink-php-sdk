<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

final class DomainRedirects
{
    private function __construct(
        public readonly ?string $baseUrlRedirect,
        public readonly ?string $regularNotFoundRedirect,
        public readonly ?string $invalidShortUrlRedirect,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload[DomainRedirectProps::BASE_URL->value] ?? null,
            $payload[DomainRedirectProps::REGULAR_NOT_FOUND->value] ?? null,
            $payload[DomainRedirectProps::INVALID_SHORT_URL->value] ?? null,
        );
    }
}
