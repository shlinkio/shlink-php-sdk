<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Http;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use JsonSerializable;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

class HttpClientTest extends TestCase
{
    use ProphecyTrait;

    private HttpClient $httpClient;
    private ObjectProphecy $client;

    public function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);

        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $requestFactory->createRequest(Argument::cetera())->will(fn (array $args) => new Request($args[0], $args[1]));

        $streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $streamFactory->createStream(Argument::any())->will(fn (array $args) => Utils::streamFor($args[0]));

        $this->httpClient = new HttpClient(
            $this->client->reveal(),
            $requestFactory->reveal(),
            $streamFactory->reveal(),
            ShlinkConfig::fromBaseUrlAndApiKey('https://doma.in', '123'),
        );
    }

    /**
     * @test
     * @dataProvider provideGetRequests
     */
    public function getFromShlinkSendsExpectedRequest(
        string $path,
        array|ArraySerializable $query,
        string $expectedUri,
    ): void {
        $sendRequest = $this->client->sendRequest(Argument::that(function (RequestInterface $req) use ($expectedUri) {
            Assert::assertEquals($expectedUri, $req->getUri()->__toString());
            Assert::assertEquals('GET', $req->getMethod());
            Assert::assertTrue($req->hasHeader('X-Api-Key'));
            Assert::assertEquals('123', $req->getHeaderLine('X-Api-Key'));

            return true;
        }))->willReturn(new Response(200, [], '{}'));

        $this->httpClient->getFromShlink($path, $query);

        $sendRequest->shouldHaveBeenCalledOnce();
    }

    public function provideGetRequests(): iterable
    {
        yield 'no query' => ['/foo/bar', [], 'https://doma.in/rest/v2/foo/bar'];
        yield 'array query' => ['/foo/bar', ['some' => 'thing'], 'https://doma.in/rest/v2/foo/bar?some=thing'];
        yield 'serializable query' => [
            '/foo/bar',
            new class implements ArraySerializable {
                public function toArray(): array
                {
                    return ['foo' => 'bar', 'tags' => ['one', 'two']];
                }
            },
            'https://doma.in/rest/v2/foo/bar?foo=bar&tags%5B0%5D=one&tags%5B1%5D=two',
        ];
    }

    /**
     * @test
     * @dataProvider provideNonSuccessfulStatuses
     */
    public function nonSuccessfulResponseResultsInException(int $status): void
    {
        $sendRequest = $this->client->sendRequest(Argument::cetera())->willReturn(new Response($status, [], '{}'));

        $sendRequest->shouldBeCalledOnce();
        $this->expectException(HttpException::class);

        $this->httpClient->getFromShlink('');
    }

    public function provideNonSuccessfulStatuses(): iterable
    {
        yield 'status 400' => [400];
        yield 'status 401' => [401];
        yield 'status 403' => [403];
        yield 'status 404' => [404];
        yield 'status 500' => [500];
        yield 'status 501' => [501];
    }

    /**
     * @test
     * @dataProvider provideSuccessfulStatuses
     */
    public function returnsExpectedResultBasedOnResponseStatus(int $status, array $expectedResult): void
    {
        $sendRequest = $this->client->sendRequest(Argument::cetera())->willReturn(
            new Response($status, [], '{"foo": "bar"}'),
        );

        $result = $this->httpClient->getFromShlink('');

        self::assertEquals($expectedResult, $result);
        $sendRequest->shouldHaveBeenCalledOnce();
    }

    public function provideSuccessfulStatuses(): iterable
    {
        yield 'status 200' => [200, ['foo' => 'bar']];
        yield 'status 201' => [201, ['foo' => 'bar']];
        yield 'status 204' => [204, []];
        yield 'status 399' => [399, ['foo' => 'bar']];
    }

    /**
     * @test
     * @dataProvider provideNonGetRequests
     */
    public function callShlinkWithBodySendsExpectedRequest(
        string $method,
        array|JsonSerializable $body,
        string $expectedBody,
    ): void {
        $sendRequest = $this->client->sendRequest(
            Argument::that(function (RequestInterface $req) use ($expectedBody, $method) {
                Assert::assertEquals($expectedBody, $req->getBody()->__toString());
                Assert::assertEquals($method, $req->getMethod());
                Assert::assertTrue($req->hasHeader('X-Api-Key'));
                Assert::assertEquals('123', $req->getHeaderLine('X-Api-Key'));

                return true;
            }),
        )->willReturn(new Response(200, [], '{}'));

        $this->httpClient->callShlinkWithBody('/foo/bar', $method, $body);

        $sendRequest->shouldHaveBeenCalledOnce();
    }

    public function provideNonGetRequests(): iterable
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
