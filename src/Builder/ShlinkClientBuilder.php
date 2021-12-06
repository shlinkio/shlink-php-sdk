<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Builder;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;
use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\Http\Debug\HttpDebuggerInterface;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\TagsClient;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
use Shlinkio\Shlink\SDK\Visits\VisitsClient;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

class ShlinkClientBuilder implements ShlinkClientBuilderInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private ?HttpDebuggerInterface $debugger = null,
    ) {
    }

    public function buildShortUrlsClient(ShlinkConfigInterface $config): ShortUrlsClientInterface
    {
        return new ShortUrlsClient($this->createHttpClient($config));
    }

    public function buildVisitsClient(ShlinkConfigInterface $config): VisitsClientInterface
    {
        return new VisitsClient($this->createHttpClient($config));
    }

    public function buildTagsClient(ShlinkConfigInterface $config): TagsClientInterface
    {
        return new TagsClient($this->createHttpClient($config));
    }

    public function buildDomainsClient(ShlinkConfigInterface $config): DomainsClientInterface
    {
        return new DomainsClient($this->createHttpClient($config));
    }

    private function createHttpClient(ShlinkConfigInterface $config): HttpClientInterface
    {
        return new HttpClient($this->client, $this->requestFactory, $this->streamFactory, $config, $this->debugger);
    }
}
