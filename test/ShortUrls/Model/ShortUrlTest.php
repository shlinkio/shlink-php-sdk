<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlMeta;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlVisitsSummary;

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
        ShortUrlVisitsSummary $expectedVisitsSummary,
    ): void {
        $shortUrl = ShortUrl::fromArray($payload);

        self::assertEquals($expectedShortCode, $shortUrl->shortCode);
        self::assertEquals($expectedShortUrl, $shortUrl->shortUrl);
        self::assertEquals($expectedLongUrl, $shortUrl->longUrl);
        self::assertEquals($expectedDateCreated, $shortUrl->dateCreated);
        self::assertEquals($expectedVisitsCount, $shortUrl->visitsCount);
        self::assertEquals($expectedDomain, $shortUrl->domain);
        self::assertEquals($expectedTitle, $shortUrl->title);
        self::assertEquals($expectedCrawlable, $shortUrl->crawlable);
        self::assertEquals($expectedForwardQuery, $shortUrl->forwardQuery);
        self::assertEquals($expectedTags, $shortUrl->tags);
        self::assertEquals($expectedMeta, $shortUrl->meta);
        self::assertEquals($expectedVisitsSummary, $shortUrl->visitsSummary);
    }

    public function providePayloads(): iterable
    {
        $now = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');
        $formattedDate = $now->format(DateTimeInterface::ATOM); // @phpstan-ignore-line

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
            ShortUrlVisitsSummary::fromArrayWithFallback([], 0),
        ];
        yield 'all values' => [
            [
                'shortCode' => 'foo',
                'shortUrl' => 'https://s.test/foo',
                'longUrl' => 'https://foo.com/bar',
                'dateCreated' => $formattedDate,
                'visitsCount' => 35,
                'domain' => 'domain',
                'title' => 'title',
                'crawlable' => true,
                'forwardQuery' => true,
                'tags' => ['foo', 'bar'],
                'meta' => $meta = [
                    'maxVisits' => 30,
                ],
                'visitsSummary' => $visitsSummary = [
                    'total' => 3,
                    'nonBots' => 3,
                    'bots' => 3,
                ],
            ],
            'foo',
            'https://s.test/foo',
            'https://foo.com/bar',
            $now,
            35,
            'domain',
            'title',
            true,
            true,
            ['foo', 'bar'],
            ShortUrlMeta::fromArray($meta),
            ShortUrlVisitsSummary::fromArrayWithFallback($visitsSummary, 5),
        ];
        yield 'visits total fallback' => [
            ['dateCreated' => $formattedDate, 'visitsCount' => 35],
            '',
            '',
            '',
            $now,
            35,
            null,
            null,
            false,
            false,
            [],
            ShortUrlMeta::fromArray([]),
            ShortUrlVisitsSummary::fromArrayWithFallback([], 35),
        ];
    }
}
