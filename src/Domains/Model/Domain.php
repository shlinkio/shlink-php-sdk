<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

final class Domain
{
    private function __construct(
        public readonly string $domain,
        public readonly bool $isDefault,
        public readonly DomainRedirects $redirects,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['domain'] ?? '',
            $payload['isDefault'] ?? false,
            DomainRedirects::fromArray($payload['redirects'] ?? []),
        );
    }
}
