<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;

use function sprintf;

class ShortUrlsClient implements ShortUrlsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listShortUrls(): ShortUrlsList
    {
        return $this->listShortUrlsWithFilter(ShortUrlsFilter::create());
    }

    public function listShortUrlsWithFilter(ShortUrlsFilter $filter): ShortUrlsList
    {
        $buildQueryWithPage = static function (int $page) use ($filter): array {
            $query = $filter->toArray();
            $query['itemsPerPage'] = ShortUrlsList::ITEMS_PER_PAGE;
            $query['page'] = $page;

            return $query;
        };

        return ShortUrlsList::forTupleLoader(function (int $page) use ($buildQueryWithPage): array {
            $payload = $this->httpClient->getFromShlink(
                '/short-urls',
                $buildQueryWithPage($page),
            );

            return [$payload['shortUrls']['data'] ?? [], $payload['shortUrls']['pagination'] ?? []];
        });
    }

    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl
    {
        return ShortUrl::fromArray($this->httpClient->getFromShlink(...$this->identifierToUrlAndQuery($identifier)));
    }

    public function deleteShortUrl(ShortUrlIdentifier $identifier): void
    {
        [$url, $query] = $this->identifierToUrlAndQuery($identifier);
        $this->httpClient->callShlinkWithBody($url, 'DELETE', [], $query);
    }

    public function createShortUrl(ShortUrlCreation $creation): ShortUrl
    {
        return ShortUrl::fromArray($this->httpClient->callShlinkWithBody('/short-urls', 'POST', $creation));
    }

    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl
    {
        [$url, $query] = $this->identifierToUrlAndQuery($identifier);
        return ShortUrl::fromArray($this->httpClient->callShlinkWithBody($url, 'PATCH', $edition, $query));
    }

    /**
     * @return array{string, array}
     */
    private function identifierToUrlAndQuery(ShortUrlIdentifier $identifier): array
    {
        return [
            sprintf('/short-urls/%s', $identifier->shortCode()),
            ['domain' => $identifier->domain()],
        ];
    }
}
