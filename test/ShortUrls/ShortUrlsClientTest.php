<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
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

    #[Test]
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

    #[Test, DataProvider('provideIdentifiers')]
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

    #[Test, DataProvider('provideIdentifiers')]
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

    #[Test, DataProvider('provideIdentifiers')]
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

    public static function provideIdentifiers(): iterable
    {
        yield 'no domain' => [ShortUrlIdentifier::fromShortCode('foo')];
        yield 'domain' => [ShortUrlIdentifier::fromShortCodeAndDomain('foo', 's.test')];
    }

    #[Test]
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
     */
    #[Test, DataProvider('provideGetExceptions')]
    public function getShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('getFromShlink')->willThrowException($original);
        $this->expectException($expected);

        $this->client->getShortUrl(ShortUrlIdentifier::fromShortCode('foo'));
    }

    public static function provideGetExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_SHORTCODE v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE']),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_SHORTCODE v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_SHORTCODE->value]),
            ShortUrlNotFoundException::class,
        ];
    }

    /**
     * @param class-string<Throwable> $expected
     */
    #[Test, DataProvider('provideDeleteExceptions')]
    public function deleteShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expected);

        $this->client->deleteShortUrl(ShortUrlIdentifier::fromShortCode('foo'));
    }

    public static function provideDeleteExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_SHORTCODE v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE']),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_SHORTCODE v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_SHORTCODE->value]),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_SHORTCODE_DELETION type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE_DELETION']),
            DeleteShortUrlThresholdException::class,
        ];
        yield 'INVALID_SHORT_URL_DELETION v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORT_URL_DELETION']),
            DeleteShortUrlThresholdException::class,
        ];
        yield 'INVALID_SHORT_URL_DELETION v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_SHORT_URL_DELETION->value]),
            DeleteShortUrlThresholdException::class,
        ];
    }

    /**
     * @param class-string<Throwable> $expected
     */
    #[Test, DataProvider('provideCreateExceptions')]
    public function createShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expected);

        $this->client->createShortUrl(ShortUrlCreation::forLongUrl('https://foof.com'));
    }

    public static function provideCreateExceptions(): iterable
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
        yield 'INVALID_URL v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_URL']),
            InvalidLongUrlException::class,
        ];
        yield 'INVALID_URL v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_URL->value]),
            InvalidLongUrlException::class,
        ];
        yield 'INVALID_SLUG v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SLUG']),
            NonUniqueSlugException::class,
        ];
        yield 'INVALID_SLUG v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_SLUG->value]),
            NonUniqueSlugException::class,
        ];
    }

    /**
     * @param class-string<Throwable> $expected
     */
    #[Test, DataProvider('provideEditExceptions')]
    public function editShortUrlThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expected);

        $this->client->editShortUrl(ShortUrlIdentifier::fromShortCode('foo'), ShortUrlEdition::create());
    }

    public static function provideEditExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_SHORTCODE v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE']),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_SHORTCODE v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_SHORTCODE->value]),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_ARGUMENT type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_ARGUMENT']),
            InvalidDataException::class,
        ];
    }
}
