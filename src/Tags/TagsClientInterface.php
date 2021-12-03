<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

interface TagsClientInterface
{
    /**
     * @return string[]
     */
    public function listTags(): array;

    /**
     * @return iterable<TagWithStats>
     */
    public function listTagsWithStats(): iterable;

    /**
     * @throws HttpException
     * @throws InvalidDataException
     * @throws ForbiddenTagOperationException
     * @throws TagNotFoundException
     * @throws TagConflictException
     */
    public function renameTag(TagRenaming $tagRenaming): void;

    /**
     * @throws HttpException
     * @throws ForbiddenTagOperationException
     */
    public function deleteTags(string ...$tags): void;
}
