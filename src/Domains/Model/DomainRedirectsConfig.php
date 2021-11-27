<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

use JsonSerializable;

final class DomainRedirectsConfig implements JsonSerializable
{
    private function __construct(private array $payload = [])
    {
    }

    public static function forDomain(string $domain): self
    {
        return new self(['domain' => $domain]);
    }

    public function withBaseUrlRedirect(string $url): self
    {
        return $this->getCloneWithProp(DomainRedirectProps::BASE_URL, $url);
    }

    public function removingBaseUrlRedirect(): self
    {
        return $this->getCloneWithProp(DomainRedirectProps::BASE_URL, null);
    }

    public function withRegularNotFoundRedirect(string $url): self
    {
        return $this->getCloneWithProp(DomainRedirectProps::REGULAR_NOT_FOUND, $url);
    }

    public function removingRegularNotFoundRedirect(): self
    {
        return $this->getCloneWithProp(DomainRedirectProps::REGULAR_NOT_FOUND, null);
    }

    public function withInvalidShortUrlRedirect(string $url): self
    {
        return $this->getCloneWithProp(DomainRedirectProps::INVALID_SHORT_URL, $url);
    }

    public function removingInvalidShortUrlRedirect(): self
    {
        return $this->getCloneWithProp(DomainRedirectProps::INVALID_SHORT_URL, null);
    }

    private function getCloneWithProp(string $prop, ?string $value): self
    {
        $clone = new self($this->payload);
        $clone->payload[$prop] = $value;

        return $clone;
    }

    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}
