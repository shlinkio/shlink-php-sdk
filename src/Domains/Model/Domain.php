<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

final readonly class Domain
{
    private function __construct(public string $domain, public bool $isDefault, public DomainRedirects $redirects)
    {
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
