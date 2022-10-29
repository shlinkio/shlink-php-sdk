<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\DeleteShortUrlThresholdException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\InvalidLongUrlException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\NonUniqueSlugException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;
use Throwable;

use function count;
use function sprintf;

class ShortUrlsClientTest extends TestCase
{
    private ShortUrlsClient $client;
    private MockObject & HttpClientInterface $httpClient;
    private string $now;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = new ShortUrlsClient($this->httpClient);
        $this->now = (new DateTimeImmutable())->format(DateTimeInterface::ATOM);
        ;
    }

    /** @test */
    public function listShortUrlVisitsPerformsExpectedCall(): void
    {
        $amountOfPages = 3;
        $now = $this->now;

        $this->httpClient->expects($this->exactly($amountOfPages))->method('getFromShlink')->with(
            '/short-urls',
            $this->anything(),
        )->willReturnCallback(
            function ($_, array $query) use ($amountOfPages, $now) {
                $page = $query['page'];
                $data = [
                    [
                        'shortCode' => 'shortCode_' . $page . '_1',
                        'longUrl' => 'longUrl_' . $page . '_1',
                        'dateCreated' => $now,
                    ],
                    [
                        'shortCode' => 'shortCode_' . $page . '_2',
                        'longUrl' => 'longUrl_' . $page . '_2',
                        'dateCreated' => $now,
                    ],
                ];

                return [
                    'shortUrls' => [
                        'data' => $data,
                        'pagination' => [
                            'currentPage' => $page,
                            'pagesCount' => $amountOfPages,
                            'totalItems' => $amountOfPages * count($data),
                        ],
                    ],
                ];
            },
        );

        $result = $this->client->listShortUrls();

        self::assertCount($amountOfPages * 2, $result);

        $count = 0;
        foreach ($result as $index => $shortUrl) {
            $count++;
            self::assertStringStartsWith('shortCode_', $shortUrl->shortCode);
            self::assertStringStartsWith('longUrl_', $shortUrl->longUrl);
            self::assertStringEndsWith($index % 2 === 0 ? '_1' : '_2', $shortUrl->shortCode);
            self::assertStringEndsWith($index % 2 === 0 ? '_1' : '_2', $shortUrl->longUrl);
            self::assertStringStartsWith($shortUrl->dateCreated->format('Y-m-d'), $now);
        }

        self::assertEquals($amountOfPages * 2, $count);
    }

    /**
     * @test
     * @dataProvider provideIdentifiers
     */
    public function getShortUrlPerformsExpectedCall(ShortUrlIdentifier $identifier): void
    {
        $expected = ['dateCreated' => $this->now];
        $this->httpClient->expects($this->once())->method('getFromShlink')->with(
            sprintf('/short-urls/%s', $identifier->shortCode),
            $this->callback(fn (array $query): bool => $query['domain'] === $identifier->domain),
        )->willReturn($expected);

        $result = $this->client->getShortUrl($identifier);

        self::assertEquals(ShortUrl::fromArray($expected), $result);
    }

    /**
     * @test
     * @dataProvider provideIdentifiers
     */
    public function deleteShortUrlPerformsExpectedCall(ShortUrlIdentifier $identifier): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with(
            sprintf('/short-urls/%s', $identifier->shortCode),
            'DELETE',
            [],
            $this->callback(fn (array $query): bool => $query['domain'] === $identifier->domain),
        );

        $this->client->deleteShortUrl($identifier);
    }

    /**
     * @test
     * @dataProvider provideIdentifiers
     */
    public function editShortUrlPerformsExpectedCall(ShortUrlIdentifier $identifier): void
    {
        $expected = ['dateCreated' => $this->now];
        $edit = ShortUrlEdition::create();
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with(
            sprintf('/short-urls/%s', $identifier->shortCode),
            'PATCH',
            $edit,
            $this->callback(fn (array $query): bool => $query['domain'] === $identifier->domain),
        )->willReturn($expected);

        $result = $this->client->editShortUrl($identifier, $edit);

        self::assertEquals(ShortUrl::fromArray($expected), $result);
    }

    public function provideIdentifiers(): iterable
    {
        yield 'no domain' => [ShortUrlIdentifier::fromShortCode('foo')];
        yield 'domain' => [ShortUrlIdentifier::fromShortCodeAndDomain('foo', 'doma.in')];
    }

    /** @test */
    public function createShortUrlPerformsExpectedCall(): void
    {
        $expected = ['dateCreated' => $this->now];
        $create = ShortUrlCreation::forLongUrl('https://foo.com');
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with(
            '/short-urls',
            'POST',
            $create,
        )->willReturn($expected);

        $result = $this->client->createShortUrl($create);

        self::assertEquals(ShortUrl::fromArray($expected), $result);
    }

    /**
     * @param class-string<Throwable> $expected
     * @test
     * @dataProvider provideGetExceptions
     */
    public function getShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('getFromShlink')->willThrowException($original);
        $this->expectException($expected);

        $this->client->getShortUrl(ShortUrlIdentifier::fromShortCode('foo'));
    }

    public function provideGetExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_SHORTCODE type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE']),
            ShortUrlNotFoundException::class,
        ];
    }

    /**
     * @param class-string<Throwable> $expected
     * @test
     * @dataProvider provideDeleteExceptions
     */
    public function deleteShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expected);

        $this->client->deleteShortUrl(ShortUrlIdentifier::fromShortCode('foo'));
    }

    public function provideDeleteExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_SHORTCODE type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE']),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_SHORTCODE_DELETION type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE_DELETION']),
            DeleteShortUrlThresholdException::class,
        ];
    }

    /**
     * @param class-string<Throwable> $expected
     * @test
     * @dataProvider provideCreateExceptions
     */
    public function createShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expected);

        $this->client->createShortUrl(ShortUrlCreation::forLongUrl('https://foof.com'));
    }

    public function provideCreateExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_ARGUMENT type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_ARGUMENT']),
            InvalidDataException::class,
        ];
        yield 'INVALID_URL type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_URL']),
            InvalidLongUrlException::class,
        ];
        yield 'INVALID_SLUG type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SLUG']),
            NonUniqueSlugException::class,
        ];
    }

    /**
     * @param class-string<Throwable> $expected
     * @test
     * @dataProvider provideEditExceptions
     */
    public function editShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expected);

        $this->client->editShortUrl(ShortUrlIdentifier::fromShortCode('foo'), ShortUrlEdition::create());
    }

    public function provideEditExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_SHORTCODE type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE']),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_ARGUMENT type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_ARGUMENT']),
            InvalidDataException::class,
        ];
    }
}
