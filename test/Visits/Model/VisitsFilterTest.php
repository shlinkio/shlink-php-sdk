<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Visits\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

class VisitsFilterTest extends TestCase
{
    #[Test, DataProvider('providePayloads')]
    public function payloadIsBuiltAsExpected(callable $createFilter, array $expected): void
    {
        /** @var VisitsFilter $filter */
        $filter = $createFilter();
        self::assertEquals($expected, $filter->toArray());
    }

    public static function providePayloads(): iterable
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
