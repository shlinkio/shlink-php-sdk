<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags;

use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

interface TagsClientInterface
{
    public function listTags(): array;

    /**
     * @return TagWithStats[]
     */
    public function listTagsWithStats(): iterable;

    public function renameTag(string $oldName, string $newName): void;

    public function deleteTags(array $tags): void;
}
