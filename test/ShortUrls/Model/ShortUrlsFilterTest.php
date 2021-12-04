<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlListOrderFields;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;

class ShortUrlsFilterTest extends TestCase
{
    /**
     * @test
     * @dataProvider providePayloads
     */
    public function payloadIsBuiltAsExpected(callable $createFilter, array $expected): void
    {
        /** @var ShortUrlsFilter $filter */
        $filter = $createFilter();

        self::assertEquals($expected, $filter->toArray());
    }

    public function providePayloads()
    {
        $date = new DateTimeImmutable();

        yield [fn () => ShortUrlsFilter::create(), []];
        yield [
            fn () => ShortUrlsFilter::create()
                ->since($date)
                ->until($date),
            ['startDate' => $formatted = $date->format(DateTimeInterface::ATOM), 'endDate' => $formatted],
        ];
        yield [
            fn () => ShortUrlsFilter::create()
                ->containingTags('foo', 'bar')
                ->searchingBy('searching'),
            ['tags' => ['foo', 'bar'], 'searchTerm' => 'searching'],
        ];
        yield [
            fn () => ShortUrlsFilter::create()->orderingAscBy(ShortUrlListOrderFields::VISITS),
            ['orderBy' => 'visits-ASC'],
        ];
        yield [
            fn () => ShortUrlsFilter::create()->orderingDescBy(ShortUrlListOrderFields::LONG_URL),
            ['orderBy' => 'longUrl-DESC'],
        ];
    }
}
