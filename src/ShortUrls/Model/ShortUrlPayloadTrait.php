<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeInterface;

trait ShortUrlPayloadTrait
{
    private function __construct(private array $payload = [])
    {
    }

    public function validSince(DateTimeInterface $validSince): self
    {
        return $this->cloneWithProp('validSince', $validSince->format(DateTimeInterface::ATOM));
    }

    public function validUntil(DateTimeInterface $validSince): self
    {
        return $this->cloneWithProp('validUntil', $validSince->format(DateTimeInterface::ATOM));
    }

    public function withMaxVisits(int $maxVisits): self
    {
        return $this->cloneWithProp('maxVisits', $maxVisits);
    }

    public function withTags(string ...$tags): self
    {
        return $this->cloneWithProp('tags', $tags);
    }

    public function withTitle(string $title): self
    {
        return $this->cloneWithProp('title', $title);
    }

    public function crawlable(): self
    {
        return $this->cloneWithProp('crawlable', true);
    }

    public function withoutQueryForwardingOnRedirect(): self
    {
        return $this->cloneWithProp('forwardQuery', false);
    }

    private function cloneWithProp(string $prop, mixed $value): self
    {
        $clone = new self($this->payload);
        $clone->payload[$prop] = $value;

        return $clone;
    }
}
