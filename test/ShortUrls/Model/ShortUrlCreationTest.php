<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Model;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;

class ShortUrlCreationTest extends TestCase
{
    #[Test, DataProvider('provideConfigs')]
    public function payloadIsBuiltAsExpected(callable $createObject, array $expectedPayload): void
    {
        /** @var ShortUrlCreation $creation */
        $creation = $createObject();

        self::assertEquals($expectedPayload, $creation->jsonSerialize());
    }

    public static function provideConfigs(): iterable
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01');

        yield [fn () => ShortUrlCreation::forLongUrl('https://foo.com'), ['longUrl' => 'https://foo.com']];
        yield [
            fn () => ShortUrlCreation::forLongUrl('https://foo.com')->returnExistingMatchingShortUrl(),
            ['longUrl' => 'https://foo.com', 'findIfExists' => true],
        ];
        yield [
            fn () => ShortUrlCreation::forLongUrl('https://foo.com')
                ->withTags('foo', 'bar')
                ->validSince($date) // @phpstan-ignore-line
                ->withCustomSlug('some-slug'),
            [
                'longUrl' => 'https://foo.com',
                'tags' => ['foo', 'bar'],
                'customSlug' => 'some-slug',
                'validSince' => $date->format(DateTimeInterface::ATOM), // @phpstan-ignore-line
            ],
        ];
        yield [
            fn () => ShortUrlCreation::forLongUrl('https://foo.com')
                ->withCustomSlug('some-slug')
                ->withShortCodeLength(50),
            ['longUrl' => 'https://foo.com', 'shortCodeLength' => 50],
        ];
        yield [
            fn () => ShortUrlCreation::forLongUrl('https://foo.com')
                ->withShortCodeLength(50)
                ->withCustomSlug('some-slug'),
            ['longUrl' => 'https://foo.com', 'customSlug' => 'some-slug'],
        ];
        yield [
            fn () => ShortUrlCreation::forLongUrl('https://foo.com')
                ->notValidatingTheLongUrl()
                ->crawlable(),
            ['longUrl' => 'https://foo.com', 'validateUrl' => false, 'crawlable' => true],
        ];
        yield [
            fn () => ShortUrlCreation::forLongUrl('https://foo.com')->validatingTheLongUrl(),
            ['longUrl' => 'https://foo.com', 'validateUrl' => true],
        ];
        yield [
            fn () => ShortUrlCreation::forLongUrl('https://foo.com')
                ->withoutQueryForwardingOnRedirect()
                ->forDomain('s.test'),
            ['longUrl' => 'https://foo.com', 'forwardQuery' => false, 'domain' => 's.test'],
        ];
    }
}
