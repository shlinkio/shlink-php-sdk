<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlMeta;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

class ShortUrlTest extends TestCase
{
    #[Test, DataProvider('providePayloads')]
    public function properObjectIsCreatedFromArray(
        array $payload,
        string $expectedShortCode,
        string $expectedShortUrl,
        string $expectedLongUrl,
        DateTimeInterface $expectedDateCreated,
        string|null $expectedDomain,
        string|null $expectedTitle,
        bool $expectedCrawlable,
        bool $expectedForwardQuery,
        array $expectedTags,
        ShortUrlMeta $expectedMeta,
        VisitsSummary $expectedVisitsSummary,
    ): void {
        $shortUrl = ShortUrl::fromArray($payload);

        self::assertEquals($expectedShortCode, $shortUrl->shortCode);
        self::assertEquals($expectedShortUrl, $shortUrl->shortUrl);
        self::assertEquals($expectedLongUrl, $shortUrl->longUrl);
        self::assertEquals($expectedDateCreated, $shortUrl->dateCreated);
        self::assertEquals($expectedDomain, $shortUrl->domain);
        self::assertEquals($expectedTitle, $shortUrl->title);
        self::assertEquals($expectedCrawlable, $shortUrl->crawlable);
        self::assertEquals($expectedForwardQuery, $shortUrl->forwardQuery);
        self::assertEquals($expectedTags, $shortUrl->tags);
        self::assertEquals($expectedMeta, $shortUrl->meta);
        self::assertEquals($expectedVisitsSummary, $shortUrl->visitsSummary);
    }

    public static function providePayloads(): iterable
    {
        $now = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');
        $formattedDate = $now->format(DateTimeInterface::ATOM); // @phpstan-ignore-line

        yield 'defaults' => [
            ['dateCreated' => $formattedDate],
            '',
            '',
            '',
            $now,
            null,
            null,
            false,
            false,
            [],
            ShortUrlMeta::fromArray([]),
            VisitsSummary::fromArray([]),
        ];
        yield 'all values' => [
            [
                'shortCode' => 'foo',
                'shortUrl' => 'https://s.test/foo',
                'longUrl' => 'https://foo.com/bar',
                'dateCreated' => $formattedDate,
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
            'domain',
            'title',
            true,
            true,
            ['foo', 'bar'],
            ShortUrlMeta::fromArray($meta),
            VisitsSummary::fromArray($visitsSummary),
        ];
    }
}
