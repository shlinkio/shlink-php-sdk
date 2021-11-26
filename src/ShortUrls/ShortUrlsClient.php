<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;

class ShortUrlsClient implements ShortUrlsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listShortUrls(): ShortUrlsList
    {
        return new ShortUrlsList(function (int $page): array {
            $payload = $this->httpClient->getFromShlink(
                '/short-urls',
                ['page' => $page, 'itemsPerPage' => ShortUrlsList::ITEMS_PER_PAGE],
            );

            return [$payload['shortUrls']['data'] ?? [], $payload['shortUrls']['pagination'] ?? []];
        });
    }
}
