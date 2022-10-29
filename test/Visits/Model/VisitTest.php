<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Visits\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Visits\Model\Visit;
use Shlinkio\Shlink\SDK\Visits\Model\VisitLocation;

class VisitTest extends TestCase
{
    /**
     * @test
     * @dataProvider providePayloads
     */
    public function properObjectIsCreatedFromArray(
        array $payload,
        string $expectedReferer,
        DateTimeInterface $expectedDate,
        string $expectedUserAgent,
        bool $expectedPotentialBot,
        ?VisitLocation $expectedLocation,
    ): void {
        $visit = Visit::fromArray($payload);

        self::assertEquals($expectedReferer, $visit->referer());
        self::assertEquals($expectedDate, $visit->date());
        self::assertEquals($expectedUserAgent, $visit->userAgent());
        self::assertEquals($expectedPotentialBot, $visit->potentialBot());
        self::assertEquals($expectedLocation, $visit->location());
    }

    public function providePayloads(): iterable
    {
        $now = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');
        $formattedDate = $now->format(DateTimeInterface::ATOM); // @phpstan-ignore-line

        yield 'defaults' => [['date' => $formattedDate], '', $now, '', false, null];
        yield 'all data' => [
            [
                'referer' => 'referer',
                'date' => $formattedDate,
                'userAgent' => 'userAgent',
                'potentialBot' => true,
                'visitLocation' => [],
            ],
            'referer',
            $now,
            'userAgent',
            true,
            VisitLocation::fromArray([]),
        ];
    }
}
