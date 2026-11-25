<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use JsonSerializable;
use Override;

final readonly class TagRenaming implements JsonSerializable
{
    private function __construct(private array $payload) {}

    public static function fromOldNameAndNewName(string $oldName, string $newName): self
    {
        return new self([
            'oldName' => $oldName,
            'newName' => $newName,
        ]);
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}
