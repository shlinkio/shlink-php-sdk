<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use JsonSerializable;

final class ShortUrlEdition implements JsonSerializable
{
    use ShortUrlPayloadTrait;

    public static function create(): self
    {
        return new self();
    }

    public function withLongUrl(string $longUrl): self
    {
        return $this->cloneWithProp('longUrl', $longUrl);
    }

    public function removingValidSince(): self
    {
        return $this->cloneWithProp('validSince', null);
    }

    public function removingValidUntil(): self
    {
        return $this->cloneWithProp('validUntil', null);
    }

    public function removingMaxVisits(): self
    {
        return $this->cloneWithProp('maxVisits', null);
    }

    public function removingTitle(): self
    {
        return $this->cloneWithProp('title', null);
    }

    public function withoutTags(): self
    {
        return $this->withTags([]);
    }

    public function notCrawlable(): self
    {
        return $this->cloneWithProp('crawlable', false);
    }

    public function withQueryForwardingOnRedirect(): self
    {
        return $this->cloneWithProp('forwardQuery', true);
    }

    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}
