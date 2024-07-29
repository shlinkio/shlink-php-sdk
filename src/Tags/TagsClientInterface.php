<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsWithStatsList;

interface TagsClientInterface
{
    /**
     * @return string[]
     */
    public function listTags(): array;

    /**
     * @return string[]
     */
    public function listTagsWithFilter(TagsFilter $filter): array;

    public function listTagsWithStats(): TagsWithStatsList;

    public function listTagsWithStatsWithFilter(TagsFilter $filter): TagsWithStatsList;

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
