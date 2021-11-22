<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;
use Shlinkio\Shlink\SDK\Utils\JsonDecoder;

class ShortUrlsClient implements ShortUrlsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listShortUrls(): ShortUrlsList
    {
        return new ShortUrlsList(function (int $page): array {
            $resp = $this->httpClient->getFromShlink(
                '/short-urls',
                ['page' => $page, 'itemsPerPage' => ShortUrlsList::ITEMS_PER_PAGE],
            );
            $body = JsonDecoder::decode($resp->getBody()->__toString());

            return [$body['shortUrls']['data'] ?? [], $body['shortUrls']['pagination'] ?? []];
        });
    }
}
