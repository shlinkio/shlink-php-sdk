<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;

use function sprintf;

class ShortUrlsClient implements ShortUrlsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listShortUrls(): ShortUrlsList
    {
        return ShortUrlsList::forTupleLoader(function (int $page): array {
            $payload = $this->httpClient->getFromShlink(
                '/short-urls',
                ['page' => $page, 'itemsPerPage' => ShortUrlsList::ITEMS_PER_PAGE],
            );

            return [$payload['shortUrls']['data'] ?? [], $payload['shortUrls']['pagination'] ?? []];
        });
    }

    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl
    {
        return ShortUrl::fromArray($this->httpClient->getFromShlink(
            sprintf('/short-urls/%s', $identifier->shortCode()),
            ['domain' => $identifier->domain()],
        ));
    }
}
