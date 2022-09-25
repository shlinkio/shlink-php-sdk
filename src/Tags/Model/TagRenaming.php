<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use JsonSerializable;

final class TagRenaming implements JsonSerializable
{
    private function __construct(private readonly array $payload)
    {
    }

    public static function fromOldNameAndNewName(string $oldName, string $newName): self
    {
        return new self([
            'oldName' => $oldName,
            'newName' => $newName,
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}
