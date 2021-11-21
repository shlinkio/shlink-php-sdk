<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits;

use Closure;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class VisitsClient implements VisitsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listShortUrlVisits(string $shortCode, ?string $domain = null): VisitsList
    {
        $query = $domain !== null ? ['domain' => $domain] : [];
        return new VisitsList($this->createVisitsLoaderForUrl("/short-urls/$shortCode/visits", $query));
    }

    public function listTagVisits(string $tag): VisitsList
    {
        return new VisitsList($this->createVisitsLoaderForUrl("/tags/$tag/visits"));
    }

    public function listOrphanVisits(): VisitsList
    {
        return new VisitsList($this->createVisitsLoaderForUrl('/visits/orphan'));
    }

    private function createVisitsLoaderForUrl(string $url, array $query = []): Closure
    {
        return function (int $page) use ($url, $query): array {
            $query['page'] = $page;
            $query['itemsPerPage'] = VisitsList::ITEMS_PER_PAGE;
            $resp = $this->httpClient->getFromShlink($url, $query);
            $body = json_decode($resp->getBody()->__toString(), true, 512, JSON_THROW_ON_ERROR);

            return [$body['visits']['data'] ?? [], $body['visits']['pagination'] ?? []];
        };
    }
}
