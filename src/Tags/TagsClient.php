<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsWithStatsList;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

class TagsClient implements TagsClientInterface
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @return string[]
     */
    public function listTags(): array
    {
        return $this->listTagsWithFilter(TagsFilter::create());
    }

    public function listTagsWithFilter(TagsFilter $filter): array
    {
        return $this->httpClient->getFromShlink('/tags', $filter)['tags']['data'] ?? [];
    }

    /**
     * @return TagsWithStatsList|TagWithStats[]
     */
    public function listTagsWithStats(): TagsWithStatsList
    {
        return $this->listTagsWithStatsWithFilter(TagsFilter::create());
    }

    /**
     * @return TagsWithStatsList|TagWithStats[]
     */
    public function listTagsWithStatsWithFilter(TagsFilter $filter): TagsWithStatsList
    {
        $query = $filter->toArray();
        $buildQueryWithPage = static function (int $page, int $itemsPerPage) use ($query): array {
            $query['itemsPerPage'] = $itemsPerPage;
            $query['page'] = $page;

            return $query;
        };
        $tupleLoader = function (int $page, int $itemsPerPage) use ($buildQueryWithPage): array {
            $payload = $this->httpClient->getFromShlink('/tags/stats', $buildQueryWithPage($page, $itemsPerPage));
            return [$payload['tags']['data'] ?? [], $payload['tags']['pagination'] ?? []];
        };

        return $filter->shouldPaginateRequest()
            ? TagsWithStatsList::forTupleLoader($tupleLoader)
            : TagsWithStatsList::forNonPaginatedTupleLoader($tupleLoader);
    }

    /**
     * @throws HttpException
     * @throws InvalidDataException
     * @throws ForbiddenTagOperationException
     * @throws TagNotFoundException
     * @throws TagConflictException
     */
    public function renameTag(TagRenaming $tagRenaming): void
    {
        try {
            $this->httpClient->callShlinkWithBody('/tags', 'PUT', $tagRenaming);
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::INVALID_ARGUMENT->value => InvalidDataException::fromHttpException($e),
                ErrorType::FORBIDDEN_OPERATION->value => ForbiddenTagOperationException::fromHttpException($e),
                ErrorType::TAG_NOT_FOUND->value => TagNotFoundException::fromHttpException($e),
                ErrorType::TAG_CONFLICT->value => TagConflictException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @throws HttpException
     * @throws ForbiddenTagOperationException
     */
    public function deleteTags(string ...$tags): void
    {
        try {
            $this->httpClient->callShlinkWithBody('/tags', 'DELETE', [], ['tags' => $tags]);
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::FORBIDDEN_OPERATION->value => ForbiddenTagOperationException::fromHttpException($e),
                default => $e,
            };
        }
    }
}
