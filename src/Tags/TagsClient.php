<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

class TagsClient implements TagsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @return string[]
     */
    public function listTags(): array
    {
        return $this->loadTags()['data'];
    }

    /**
     * @return iterable<TagWithStats>
     */
    public function listTagsWithStats(): iterable
    {
        $tags = $this->loadTags(['withStats' => 'true'])['stats'] ?? [];
        foreach ($tags as $index => $tag) {
            yield $index => TagWithStats::fromArray($tag);
        }
    }

    private function loadTags(array $query = []): array
    {
        return $this->httpClient->getFromShlink('/tags', $query)['tags'] ?? [];
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
            throw match ($e->type()) {
                'INVALID_ARGUMENT' => InvalidDataException::fromHttpException($e),
                'FORBIDDEN_OPERATION' => ForbiddenTagOperationException::fromHttpException($e),
                'TAG_NOT_FOUND' => TagNotFoundException::fromHttpException($e),
                'TAG_CONFLICT' => TagConflictException::fromHttpException($e),
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
            throw match ($e->type()) {
                'FORBIDDEN_OPERATION' => ForbiddenTagOperationException::fromHttpException($e),
                default => $e,
            };
        }
    }
}
