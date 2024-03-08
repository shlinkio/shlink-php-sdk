<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;

final readonly class ShortUrlMeta
{
    private function __construct(
        public ?DateTimeInterface $validSince,
        public ?DateTimeInterface $validUntil,
        public ?int $maxVisits,
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
