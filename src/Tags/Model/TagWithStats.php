<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

final class TagWithStats
{
    private function __construct(
        public readonly string $tag,
        public readonly int $shortUrlsCount,
        public readonly int $visitsCount,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self($payload['tag'] ?? '', $payload['shortUrlsCount'] ?? 0, $payload['visitsCount'] ?? 0);
    }
}
