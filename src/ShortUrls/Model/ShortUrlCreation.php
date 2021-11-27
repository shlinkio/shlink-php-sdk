<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use JsonSerializable;
use function array_filter;
use const ARRAY_FILTER_USE_KEY;

final class ShortUrlCreation implements JsonSerializable
{
    use ShortUrlPayloadTrait;

    private function __construct(private array $payload = [])
    {
    }

    public static function forLongUrl(string $longUrl): self
    {
        return new self(['longUrl' => $longUrl]);
    }

    public function withCustomSlug(string $slug): self
    {
        return $this->cloneWithProp('customSlug', $slug)->cloneWithoutProp('shortCodeLength');
    }

    public function withShortCodeLength(int $length): self
    {
        return $this->cloneWithProp('shortCodeLength', $length)->cloneWithoutProp('customSlug');
    }

    public function forDomain(string $domain): self
    {
        return $this->cloneWithProp('domain', $domain);
    }

    public function returnExistingMatchingShortUrl(): self
    {
        return $this->cloneWithProp('findIfExists', true);
    }

    private function cloneWithoutProp(string $prop): self
    {
        $props = array_filter($this->payload, static fn (string $key) => $key !== $prop, ARRAY_FILTER_USE_KEY);
        return new self($props);
    }

    public function jsonSerialize()
    {
        return $this->payload;
    }
}
