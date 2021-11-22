<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

final class TagWithStats
{
    private function __construct(private string $tag, private int $shortUrlsCount, private int $visitsCount)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['tag'] ?? '', $data['shortUrlsCount'] ?? 0, $data['visitsCount'] ?? 0);
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function shortUrlsCount(): int
    {
        return $this->shortUrlsCount;
    }

    public function visitsCount(): int
    {
        return $this->visitsCount;
    }
}
