<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags;

use Shlinkio\Shlink\SDK\Utils\JsonDecoder;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

use function array_map;

class TagsClient implements TagsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listTags(): array
    {
        return $this->loadTags()['data'];
    }

    public function listTagsWithStats(): array
    {
        return array_map(
            static fn (array $data) => TagWithStats::fromArray($data),
            $this->loadTags(['withStats' => 'true'])['stats'] ?? [],
        );
    }

    private function loadTags(array $query = []): array
    {
        $resp = $this->httpClient->getFromShlink('/tags', $query);
        return JsonDecoder::decode($resp->getBody()->__toString())['tags'] ?? [];
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
