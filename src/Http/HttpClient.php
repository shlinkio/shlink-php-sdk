<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http;

use JsonException;
use JsonSerializable;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Utils\JsonDecoder;

use function http_build_query;
use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class HttpClient implements HttpClientInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private ShlinkConfigInterface $config,
    ) {
    }

    /**
     * @throws HttpException
     */
    public function getFromShlink(string $path, array $query = []): array
    {
        return $this->callShlink($path, 'GET', null, $query);
    }

    /**
     * @throws HttpException
     */
    public function callShlinkWithBody(
        string $path,
        string $method,
        array|JsonSerializable $body,
        array $query = []
    ): array {
        return $this->callShlink($path, $method, $body, $query);
    }

    /**
     * @throws HttpException
     * @throws JsonException
     */
    private function callShlink(
        string $path,
        string $method,
        array|JsonSerializable|null $body = null,
        array $query = []
    ): array {
        $uri = sprintf('%s/rest/v2%s', $this->config->baseUrl(), $path);
        if (! empty($query)) {
            $uri = sprintf('%s?%s', $uri, http_build_query($query));
        }

        $req = $this->requestFactory->createRequest($method, $uri)
                                    ->withHeader('X-Api-Key', $this->config->apiKey());

        if ($body !== null) {
            $req = $req->withHeader('Content-Type', 'application/json')
                       ->withBody($this->streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR)));
        }

        $resp = $this->client->sendRequest($req);
        $status = $resp->getStatusCode();

        if ($status >= 400) {
            throw HttpException::fromNonSuccessfulResponse($resp);
        }

        return $status === 204 ? [] : JsonDecoder::decode($resp->getBody()->__toString());
    }
}
