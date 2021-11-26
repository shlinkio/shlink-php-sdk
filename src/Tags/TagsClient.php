<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags;

use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

class TagsClient implements TagsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listTags(): array
    {
        return $this->loadTags()['data'];
    }

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

    public function renameTag(string $oldName, string $newName): void
    {
        $this->httpClient->callShlinkWithBody('/tags', 'PUT', [
            'oldName' => $oldName,
            'newName' => $newName,
        ]);
    }

    public function deleteTags(array $tags): void
    {
        $this->httpClient->callShlinkWithBody('/tags', 'DELETE', [], ['tags' => $tags]);
    }
}
