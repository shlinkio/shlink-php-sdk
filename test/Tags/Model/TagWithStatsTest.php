<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

class TagWithStatsTest extends TestCase
{
    #[Test, DataProvider('providePayloads')]
    public function properObjectIsCreatedFromArray(
        array $payload,
        string $expectedTag,
        int $expectedShortUrlsCount,
        int $expectedVisitsCount,
    ): void {
        $stats = TagWithStats::fromArray($payload);

        self::assertEquals($expectedTag, $stats->tag);
        self::assertEquals($expectedShortUrlsCount, $stats->shortUrlsCount);
        self::assertEquals($expectedVisitsCount, $stats->visitsCount);
    }

    public static function providePayloads(): iterable
    {
        yield [[], '', 0, 0];
        yield [
            [
                'tag' => 'some-tag',
                'shortUrlsCount' => 23,
                'visitsCount' => 5471,
            ],
            'some-tag',
            23,
            5471,
        ];
    }
}
