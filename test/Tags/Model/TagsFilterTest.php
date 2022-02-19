<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags\Model;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsListOrderFields;

class TagsFilterTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideOrderings
     * @param callable(): TagsFilter $buildFilter
     */
    public function requiresPaginationBasedOnTheOrderingFields(callable $buildFilter, bool $shouldPaginate): void
    {
        $tagsFilter = $buildFilter();
        self::assertEquals($shouldPaginate, $tagsFilter->shouldPaginateRequest());
    }

    public function provideOrderings(): iterable
    {
        yield 'no order' => [fn () => TagsFilter::create(), true];
        yield 'tag ASC' => [fn () => TagsFilter::create()->orderingAscBy(TagsListOrderFields::TAG), true];
        yield 'tag DESC' => [fn () => TagsFilter::create()->orderingDescBy(TagsListOrderFields::TAG), true];
        yield 'shortUrlsCount ASC' => [
            fn () => TagsFilter::create()->orderingAscBy(TagsListOrderFields::SHORT_URLS_COUNT),
            false,
        ];
        yield 'shortUrlsCount DESC' => [
            fn () => TagsFilter::create()->orderingDescBy(TagsListOrderFields::SHORT_URLS_COUNT),
            false,
        ];
        yield 'visitsCount ASC' => [
            fn () => TagsFilter::create()->orderingAscBy(TagsListOrderFields::VISITS_COUNT),
            false,
        ];
        yield 'visitsCount DESC' => [
            fn () => TagsFilter::create()->orderingDescBy(TagsListOrderFields::VISITS_COUNT),
            false,
        ];
        yield 'override towards paginable' => [
            fn () => TagsFilter::create()->orderingAscBy(TagsListOrderFields::SHORT_URLS_COUNT)
                                         ->orderingDescBy(TagsListOrderFields::TAG),
            true,
        ];
        yield 'override towards non-paginable' => [
            fn () => TagsFilter::create()->orderingDescBy(TagsListOrderFields::TAG)
                                         ->orderingAscBy(TagsListOrderFields::VISITS_COUNT),
            false,
        ];
    }
}
