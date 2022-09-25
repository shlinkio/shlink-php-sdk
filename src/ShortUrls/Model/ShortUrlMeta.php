<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;

final class ShortUrlMeta
{
    private function __construct(
        public readonly ?DateTimeInterface $validSince,
        public readonly ?DateTimeInterface $validUntil,
        public readonly ?int $maxVisits,
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
}
