<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlMeta;

class ShortUrlTest extends TestCase
{
    /**
     * @test
     * @dataProvider providePayloads
     */
    public function properObjectIsCreatedFromArray(
        array $payload,
        string $expectedShortCode,
        string $expectedShortUrl,
        string $expectedLongUrl,
        DateTimeInterface $expectedDateCreated,
        int $expectedVisitsCount,
        ?string $expectedDomain,
        ?string $expectedTitle,
        bool $expectedCrawlable,
        bool $expectedForwardQuery,
        array $expectedTags,
        ShortUrlMeta $expectedMeta,
    ): void {
        $shortUrl = ShortUrl::fromArray($payload);

        self::assertEquals($expectedShortCode, $shortUrl->shortCode());
        self::assertEquals($expectedShortUrl, $shortUrl->shortUrl());
        self::assertEquals($expectedLongUrl, $shortUrl->longUrl());
        self::assertEquals($expectedDateCreated, $shortUrl->dateCreated());
        self::assertEquals($expectedVisitsCount, $shortUrl->visitsCount());
        self::assertEquals($expectedDomain, $shortUrl->domain());
        self::assertEquals($expectedTitle, $shortUrl->title());
        self::assertEquals($expectedCrawlable, $shortUrl->crawlable());
        self::assertEquals($expectedForwardQuery, $shortUrl->forwardQuery());
        self::assertEquals($expectedTags, $shortUrl->tags());
        self::assertEquals($expectedMeta, $shortUrl->meta());
    }

    public function providePayloads(): iterable
    {
        $now = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');
        $formattedDate = $now->format(DateTimeInterface::ATOM);

        yield 'defaults' => [
            ['dateCreated' => $formattedDate],
            '',
            '',
            '',
            $now,
            0,
            null,
            null,
            false,
            false,
            [],
            ShortUrlMeta::fromArray([]),
        ];
        yield 'all values' => [
            [
                'shortCode' => 'foo',
                'shortUrl' => 'https://doma.in/foo',
                'longUrl' => 'https://foo.com/bar',
                'dateCreated' => $formattedDate,
                'visitsCount' => 35,
                'domain' => 'domain',
                'title' => 'title',
                'crawlable' => true,
                'forwardQuery' => true,
                'tags' => ['foo', 'bar'],
                'meta' => $meta = [
                    'maxVisits' => 30
                ],
            ],
            'foo',
            'https://doma.in/foo',
            'https://foo.com/bar',
            $now,
            35,
            'domain',
            'title',
            true,
            true,
            ['foo', 'bar'],
            ShortUrlMeta::fromArray($meta),
        ];
    }
}