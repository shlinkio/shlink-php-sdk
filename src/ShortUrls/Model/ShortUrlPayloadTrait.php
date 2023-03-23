<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeInterface;

trait ShortUrlPayloadTrait
{
    private function __construct(private array $payload = [])
    {
    }

    public function withDeviceLongUrl(Device $device, string $longUrl): self
    {
        $deviceLongUrls = $this->payload['deviceLongUrls'] ?? [];
        $deviceLongUrls[$device->value] = $longUrl;

        return $this->cloneWithProp('deviceLongUrls', $deviceLongUrls);
    }

    public function withoutDeviceLongUrl(Device $device): self
    {
        $deviceLongUrls = $this->payload['deviceLongUrls'] ?? [];
        unset($deviceLongUrls[$device->value]);

        return $this->cloneWithProp('deviceLongUrls', $deviceLongUrls);
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

    public function validatingTheLongUrl(): self
    {
        return $this->cloneWithProp('validateUrl', true);
    }

    public function notValidatingTheLongUrl(): self
    {
        return $this->cloneWithProp('validateUrl', false);
    }

    private function cloneWithProp(string $prop, mixed $value): self
    {
        $clone = new self($this->payload);
        $clone->payload[$prop] = $value;

        return $clone;
    }
}
