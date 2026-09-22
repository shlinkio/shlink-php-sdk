<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;

class ShortUrlEditionTest extends TestCase
{
    #[Test, DataProvider('provideConfigs')]
    public function payloadIsBuiltAsExpected(callable $createObject, array $expectedPayload): void
    {
        /** @var ShortUrlEdition $creation */
        $creation = $createObject();

        self::assertEquals($expectedPayload, $creation->jsonSerialize());
    }

    public static function provideConfigs(): iterable
    {
        /** @var DateTimeImmutable $date */
        $date = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');

        yield [ShortUrlEdition::create(...), []];
        yield [
            static fn () => ShortUrlEdition::create()
                ->withTags('foo', 'bar')
                ->validUntil($date)
                ->withTitle('the title')
                ->withMaxVisits(50),
            [
                'tags' => ['foo', 'bar'],
                'maxVisits' => 50,
                'validUntil' => $date->format(DateTimeInterface::ATOM),
                'title' => 'the title',
            ],
        ];
        yield [
            static fn () => ShortUrlEdition::create()
                ->withLongUrl('https://edited.com/foo/bar')
                ->withoutTags(),
            ['longUrl' => 'https://edited.com/foo/bar', 'tags' => []],
        ];
        yield [
            static fn () => ShortUrlEdition::create()
                ->removingValidUntil()
                ->removingValidSince()
                ->removingMaxVisits()
                ->removingTitle()
                ->notCrawlable()
                ->withQueryForwardingOnRedirect(),
            [
                'maxVisits' => null,
                'validUntil' => null,
                'validSince' => null,
                'title' => null,
                'forwardQuery' => true,
                'crawlable' => false,
            ],
        ];
    }
}
