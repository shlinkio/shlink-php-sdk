<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Http;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use JsonSerializable;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

class HttpClientTest extends TestCase
{
    private HttpClient $httpClient;
    private MockObject & ClientInterface $client;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);

        $requestFactory = $this->createStub(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturnCallback(
            fn (string $method, string|UriInterface $uri) => new Request($method, $uri),
        );

        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->willReturnCallback(fn (string $content) => Utils::streamFor($content));

        $this->httpClient = new HttpClient(
            $this->client,
            $requestFactory,
            $streamFactory,
            ShlinkConfig::fromBaseUrlAndApiKey('https://s.test', '123'),
        );
    }

    #[Test, DataProvider('provideGetRequests')]
    public function getFromShlinkSendsExpectedRequest(
        string $path,
        array|ArraySerializable $query,
        string $expectedUri,
    ): void {
        $this->client->expects($this->once())->method('sendRequest')->with($this->callback(
            function (RequestInterface $req) use ($expectedUri) {
                Assert::assertEquals($expectedUri, $req->getUri()->__toString());
                Assert::assertEquals('GET', $req->getMethod());
                Assert::assertTrue($req->hasHeader('X-Api-Key'));
                Assert::assertEquals('123', $req->getHeaderLine('X-Api-Key'));

                return true;
            },
        ))->willReturn(new Response(200, [], '{}'));

        $this->httpClient->getFromShlink($path, $query);
    }

    public static function provideGetRequests(): iterable
    {
        yield 'no query' => ['/foo/bar', [], 'https://s.test/rest/v3/foo/bar'];
        yield 'array query' => ['/foo/bar', ['some' => 'thing'], 'https://s.test/rest/v3/foo/bar?some=thing'];
        yield 'serializable query' => [
            '/foo/bar',
            new class implements ArraySerializable {
                public function toArray(): array
                {
                    return ['foo' => 'bar', 'tags' => ['one', 'two']];
                }
            },
            'https://s.test/rest/v3/foo/bar?foo=bar&tags%5B0%5D=one&tags%5B1%5D=two',
        ];
    }

    #[Test, DataProvider('provideNonSuccessfulStatuses')]
    public function nonSuccessfulResponseResultsInException(int $status): void
    {
        $this->client->expects($this->once())->method('sendRequest')->willReturn(new Response($status, [], '{}'));

        $this->expectException(HttpException::class);

        $this->httpClient->getFromShlink('');
    }

    public static function provideNonSuccessfulStatuses(): iterable
    {
        yield 'status 400' => [400];
        yield 'status 401' => [401];
        yield 'status 403' => [403];
        yield 'status 404' => [404];
        yield 'status 500' => [500];
        yield 'status 501' => [501];
    }

    #[Test, DataProvider('provideSuccessfulStatuses')]
    public function returnsExpectedResultBasedOnResponseStatus(int $status, array $expectedResult): void
    {
        $this->client->expects($this->once())->method('sendRequest')->willReturn(
            new Response($status, [], '{"foo": "bar"}'),
        );

        $result = $this->httpClient->getFromShlink('');

        self::assertEquals($expectedResult, $result);
    }

    public static function provideSuccessfulStatuses(): iterable
    {
        yield 'status 200' => [200, ['foo' => 'bar']];
        yield 'status 201' => [201, ['foo' => 'bar']];
        yield 'status 204' => [204, []];
        yield 'status 399' => [399, ['foo' => 'bar']];
    }

    #[Test, DataProvider('provideNonGetRequests')]
    public function callShlinkWithBodySendsExpectedRequest(
        string $method,
        array|JsonSerializable $body,
        string $expectedBody,
    ): void {
        $this->client->expects($this->once())->method('sendRequest')->with(
            $this->callback(function (RequestInterface $req) use ($expectedBody, $method) {
                Assert::assertEquals($expectedBody, $req->getBody()->__toString());
                Assert::assertEquals($method, $req->getMethod());
                Assert::assertTrue($req->hasHeader('X-Api-Key'));
                Assert::assertEquals('123', $req->getHeaderLine('X-Api-Key'));

                return true;
            }),
        )->willReturn(new Response(200, [], '{}'));

        $this->httpClient->callShlinkWithBody('/foo/bar', $method, $body);
    }

    public static function provideNonGetRequests(): iterable
    {
        yield 'empty body' => ['POST', [], '[]'];
        yield 'array body' => ['PATCH', ['some' => 'thing'], '{"some":"thing"}'];
        yield 'serializable body' => [
            'PUT',
            new class implements JsonSerializable {
                public function jsonSerialize(): array
                {
                    return ['foo' => 'bar', 'tags' => ['one', 'two']];
                }
            },
            '{"foo":"bar","tags":["one","two"]}',
        ];
    }
}
