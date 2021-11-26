<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

final class Domain
{
    private function __construct(private string $domain, private bool $isDefault, private DomainRedirects $redirects)
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

    public function domain(): string
    {
        return $this->domain;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function redirects(): DomainRedirects
    {
        return $this->redirects;
    }
}
