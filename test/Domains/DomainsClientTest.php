<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Domains;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectProps;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;

use function count;

class DomainsClientTest extends TestCase
{
    use ProphecyTrait;

    private DomainsClient $domainsClient;
    private ObjectProphecy $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->prophesize(HttpClientInterface::class);
        $this->domainsClient = new DomainsClient($this->httpClient->reveal());
    }

    /** @test */
    public function expectedDomainsAreReturnedOnList(): void
    {
        $payload = [
            [
                'domain' => 'foo.com',
                'isDefault' => true,
                'redirects' => [
                    DomainRedirectProps::BASE_URL => null,
                    DomainRedirectProps::REGULAR_NOT_FOUND => null,
                    DomainRedirectProps::INVALID_SHORT_URL => null,
                ],
            ],
            [
                'domain' => 'bar.com',
                'isDefault' => false,
                'redirects' => [
                    DomainRedirectProps::BASE_URL => 'somewhere.com',
                    DomainRedirectProps::REGULAR_NOT_FOUND => null,
                    DomainRedirectProps::INVALID_SHORT_URL => 'somewhere-else.com',
                ],
            ],
            [
                'domain' => 'baz.com',
                'isDefault' => false,
                'redirects' => [
                    DomainRedirectProps::BASE_URL => null,
                    DomainRedirectProps::REGULAR_NOT_FOUND => 'my-redirect.net',
                    DomainRedirectProps::INVALID_SHORT_URL => null,
                ],
            ],
        ];

        $get = $this->httpClient->getFromShlink('/domains')->willReturn([
            'domains' => ['data' => $payload],
        ]);

        $result = $this->domainsClient->listDomains();
        $count = 0;

        foreach ($result as $index => $domain) {
            self::assertEquals($payload[$index]['domain'], $domain->domain());
            self::assertEquals($payload[$index]['isDefault'], $domain->isDefault());
            self::assertEquals(
                $payload[$index]['redirects'][DomainRedirectProps::BASE_URL],
                $domain->redirects()->baseUrlRedirect(),
            );
            self::assertEquals(
                $payload[$index]['redirects'][DomainRedirectProps::REGULAR_NOT_FOUND],
                $domain->redirects()->regularNotFoundRedirect(),
            );
            self::assertEquals(
                $payload[$index]['redirects'][DomainRedirectProps::INVALID_SHORT_URL],
                $domain->redirects()->invalidShortUrlRedirect(),
            );
            $count++;
        }

        self::assertEquals(count($payload), $count);
        $get->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function configureDomainRedirectsSendsExpectedRequestAndReceivesExpectedResponse(): void
    {
        $config = DomainRedirectsConfig::forDomain('foo.com')
            ->withRegularNotFoundRedirect('somewhere.com')
            ->removingBaseUrlRedirect()
            ->removingInvalidShortUrlRedirect();

        $call = $this->httpClient->callShlinkWithBody('/domains/redirects', 'PATCH', $config)->willReturn([
            DomainRedirectProps::BASE_URL => null,
            DomainRedirectProps::REGULAR_NOT_FOUND => 'somewhere.com',
            DomainRedirectProps::INVALID_SHORT_URL => null,
        ]);

        $result = $this->domainsClient->configureDomainRedirects($config);

        self::assertNull($result->baseUrlRedirect());
        self::assertNull($result->invalidShortUrlRedirect());
        self::assertEquals('somewhere.com', $result->regularNotFoundRedirect());
        $call->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideExceptions
     */
    public function configureDomainRedirectsThrowsProperExceptionIfSomethingGoesWrong(
        HttpException $originalException,
        string $expectedException,
    ): void {
        $call = $this->httpClient->callShlinkWithBody(Argument::cetera())->willThrow($originalException);

        $call->shouldBeCalledOnce();
        $this->expectException($expectedException);

        $this->domainsClient->configureDomainRedirects(DomainRedirectsConfig::forDomain('foo'));
    }

    public function provideExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_ARGUMENT type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_ARGUMENT']),
            InvalidDataException::class,
        ];
    }
}
