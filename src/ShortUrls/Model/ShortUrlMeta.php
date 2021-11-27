<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;

final class ShortUrlMeta
{
    private function __construct(
        private ?DateTimeInterface $validSince,
        private ?DateTimeInterface $validUntil,
        private ?int $maxVisits,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            self::toNullableDate($payload['validSince'] ?? null),
            self::toNullableDate($payload['validUntil'] ?? null),
            $payload['maxVisits'] ?? null,
        );
    }

    private static function toNullableDate(?string $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        return DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value) ?: null;
    }

    public function validSince(): ?DateTimeInterface
    {
        return $this->validSince;
    }

    public function validUntil(): ?DateTimeInterface
    {
        return $this->validUntil;
    }

    public function maxVisits(): ?int
    {
        return $this->maxVisits;
    }
}
