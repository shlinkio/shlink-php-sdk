<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;

class ShortUrlEditionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideConfigs
     */
    public function payloadIsBuiltAsExpected(callable $createObject, array $expectedPayload): void
    {
        /** @var ShortUrlEdition $creation */
        $creation = $createObject();

        self::assertEquals($expectedPayload, $creation->jsonSerialize());
    }

    public function provideConfigs(): iterable
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');

        yield [fn () => ShortUrlEdition::create(), []];
        yield [
            fn () => ShortUrlEdition::create()
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
            fn () => ShortUrlEdition::create()
                ->withLongUrl('https://edited.com/foo/bar')
                ->notValidatingTheLongUrl()
                ->withoutTags(),
            ['longUrl' => 'https://edited.com/foo/bar', 'validateUrl' => false, 'tags' => []],
        ];
        yield [
            fn () => ShortUrlEdition::create()
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
