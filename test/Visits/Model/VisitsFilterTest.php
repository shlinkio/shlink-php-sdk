<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Visits\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

class VisitsFilterTest extends TestCase
{
    /**
     * @test
     * @dataProvider providePayloads
     */
    public function payloadIsBuiltAsExpected(callable $createFilter, array $expected): void
    {
        /** @var VisitsFilter $filter */
        $filter = $createFilter();
        self::assertEquals($expected, $filter->toArray());
    }

    public function providePayloads(): iterable
    {
        $now = new DateTimeImmutable();

        yield [fn () => VisitsFilter::create(), []];
        yield [fn () => VisitsFilter::create()->since($now), ['startDate' => $now->format(DateTimeInterface::ATOM)]];
        yield [fn () => VisitsFilter::create()->excludingBots(), ['excludeBots' => 'true']];
        yield [
            fn () => VisitsFilter::create()
                ->excludingBots()
                ->since($now)
                ->until($now),
            [
                'startDate' => $now->format(DateTimeInterface::ATOM),
                'endDate' => $now->format(DateTimeInterface::ATOM),
                'excludeBots' => 'true',
            ],
        ];
    }
}
