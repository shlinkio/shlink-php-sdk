<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Model;

use JsonSerializable;

final class DomainRedirectsConfig implements JsonSerializable
{
    private array $redirectsPayload = [];

    private function __construct()
    {
    }

    public static function forDomain(string $domain): self
    {
        $instance = new self();
        $instance->redirectsPayload['domain'] = $domain;

        return $instance;
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
        $clone = new self();
        $clone->redirectsPayload = $this->redirectsPayload;
        $clone->redirectsPayload[$prop] = $value;

        return $clone;
    }

    public function jsonSerialize(): array
    {
        return $this->redirectsPayload;
    }
}
