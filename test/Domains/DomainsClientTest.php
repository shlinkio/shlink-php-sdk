<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Domains;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectProps;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Throwable;

use function count;

class DomainsClientTest extends TestCase
{
    private DomainsClient $domainsClient;
    private MockObject & HttpClientInterface $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->domainsClient = new DomainsClient($this->httpClient);
    }

    /** @test */
    public function expectedDomainsAreReturnedOnList(): void
    {
        $payload = [
            [
                'domain' => 'foo.com',
                'isDefault' => true,
                'redirects' => [
                    DomainRedirectProps::BASE_URL->value => null,
                    DomainRedirectProps::REGULAR_NOT_FOUND->value => null,
                    DomainRedirectProps::INVALID_SHORT_URL->value => null,
                ],
            ],
            [
                'domain' => 'bar.com',
                'isDefault' => false,
                'redirects' => [
                    DomainRedirectProps::BASE_URL->value => 'somewhere.com',
                    DomainRedirectProps::REGULAR_NOT_FOUND->value => null,
                    DomainRedirectProps::INVALID_SHORT_URL->value => 'somewhere-else.com',
                ],
            ],
            [
                'domain' => 'baz.com',
                'isDefault' => false,
                'redirects' => [
                    DomainRedirectProps::BASE_URL->value => null,
                    DomainRedirectProps::REGULAR_NOT_FOUND->value => 'my-redirect.net',
                    DomainRedirectProps::INVALID_SHORT_URL->value => null,
                ],
            ],
        ];

        $this->httpClient->expects($this->once())->method('getFromShlink')->with('/domains')->willReturn([
            'domains' => ['data' => $payload],
        ]);

        $result = $this->domainsClient->listDomains();
        $count = 0;

        foreach ($result as $index => $domain) {
            self::assertEquals($payload[$index]['domain'], $domain->domain);
            self::assertEquals($payload[$index]['isDefault'], $domain->isDefault);
            self::assertEquals(
                $payload[$index]['redirects'][DomainRedirectProps::BASE_URL->value],
                $domain->redirects->baseUrlRedirect,
            );
            self::assertEquals(
                $payload[$index]['redirects'][DomainRedirectProps::REGULAR_NOT_FOUND->value],
                $domain->redirects->regularNotFoundRedirect,
            );
            self::assertEquals(
                $payload[$index]['redirects'][DomainRedirectProps::INVALID_SHORT_URL->value],
                $domain->redirects->invalidShortUrlRedirect,
            );
            $count++;
        }

        self::assertEquals(count($payload), $count);
    }

    /** @test */
    public function configureDomainRedirectsSendsExpectedRequestAndReceivesExpectedResponse(): void
    {
        $config = DomainRedirectsConfig::forDomain('foo.com')
            ->withRegularNotFoundRedirect('somewhere.com')
            ->removingBaseUrlRedirect()
            ->removingInvalidShortUrlRedirect();

        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with(
            '/domains/redirects',
            'PATCH',
            $config,
        )->willReturn([
            DomainRedirectProps::BASE_URL->value => null,
            DomainRedirectProps::REGULAR_NOT_FOUND->value => 'somewhere.com',
            DomainRedirectProps::INVALID_SHORT_URL->value => null,
        ]);

        $result = $this->domainsClient->configureDomainRedirects($config);

        self::assertNull($result->baseUrlRedirect);
        self::assertNull($result->invalidShortUrlRedirect);
        self::assertEquals('somewhere.com', $result->regularNotFoundRedirect);
    }

    /**
     * @param class-string<Throwable> $expectedException
     * @test
     * @dataProvider provideExceptions
     */
    public function configureDomainRedirectsThrowsProperExceptionIfSomethingGoesWrong(
        HttpException $originalException,
        string $expectedException,
    ): void {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($originalException);
        $this->expectException($expectedException);

        $this->domainsClient->configureDomainRedirects(DomainRedirectsConfig::forDomain('foo'));
    }

    public static function provideExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_ARGUMENT v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_ARGUMENT']),
            InvalidDataException::class,
        ];
        yield 'INVALID_ARGUMENT v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_ARGUMENT->value]),
            InvalidDataException::class,
        ];
    }
}
