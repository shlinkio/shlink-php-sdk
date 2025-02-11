<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlMeta;

class ShortUrlMetaTest extends TestCase
{
    #[Test, DataProvider('providePayloads')]
    public function properObjectIsCreatedFromArray(
        array $payload,
        DateTimeInterface|null $expectedValidSince,
        DateTimeInterface|null $expectedValidUntil,
        int|null $expectedMaxVisits,
    ): void {
        $meta = ShortUrlMeta::fromArray($payload);

        self::assertEquals($expectedValidSince, $meta->validSince);
        self::assertEquals($expectedValidUntil, $meta->validUntil);
        self::assertEquals($expectedMaxVisits, $meta->maxVisits);
    }

    public static function providePayloads(): iterable
    {
        $now = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');
        $formattedDate = $now->format(DateTimeInterface::ATOM); // @phpstan-ignore-line

        yield 'defaults' => [[], null, null, null];
        yield 'all data' => [[
            'validSince' => $formattedDate,
            'validUntil' => $formattedDate,
            'maxVisits' => 35,
        ], $now, $now, 35];
    }
}
