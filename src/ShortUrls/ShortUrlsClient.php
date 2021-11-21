<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class ShortUrlsClient implements ShortUrlsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function listShortUrls(): ShortUrlsList
    {
        return new ShortUrlsList(function (int $page): array {
            $resp = $this->httpClient->getFromShlink('/short-urls', ['page' => $page]);
            return json_decode($resp->getBody()->__toString(), true, 512, JSON_THROW_ON_ERROR);
        });
    }
}
