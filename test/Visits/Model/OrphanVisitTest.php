<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Visits\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisit;
use Shlinkio\Shlink\SDK\Visits\Model\VisitLocation;

class OrphanVisitTest extends TestCase
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
        string $expectedVisitedUrl,
        string $expectedType,
    ): void {
        $visit = OrphanVisit::fromArray($payload);

        self::assertEquals($expectedReferer, $visit->referer());
        self::assertEquals($expectedDate, $visit->date());
        self::assertEquals($expectedUserAgent, $visit->userAgent());
        self::assertEquals($expectedPotentialBot, $visit->potentialBot());
        self::assertEquals($expectedLocation, $visit->location());
        self::assertEquals($expectedVisitedUrl, $visit->visitedUrl());
        self::assertEquals($expectedType, $visit->type());
    }

    public function providePayloads(): iterable
    {
        $now = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');
        $formattedDate = $now->format(DateTimeInterface::ATOM);

        yield 'defaults' => [['date' => $formattedDate], '', $now, '', false, null, '', ''];
        yield 'all data' => [
            [
                'referer' => 'referer',
                'date' => $formattedDate,
                'userAgent' => 'userAgent',
                'potentialBot' => true,
                'visitLocation' => [],
                'visitedUrl' => 'https://doma.in/foo/bar',
                'type' => 'REGULAR_NOT_FOUND',
            ],
            'referer',
            $now,
            'userAgent',
            true,
            VisitLocation::fromArray([]),
            'https://doma.in/foo/bar',
            'REGULAR_NOT_FOUND',
        ];
    }
}
