<?php

declare(strict_types=1);

namespace ShlinkioIntegrationTest\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;
use ShlinkioIntegrationTest\Shlink\SDK\TestCase\AbstractTestCase;

use function count;

class ShortUrlsClientTest extends AbstractTestCase
{
    private ShortUrlsClient $client;

    protected function setUp(): void
    {
        $this->client = new ShortUrlsClient(self::httpClient());
    }

    /** @test */
    public function shortUrlCanBeCreated(): void
    {
        $createdShortUrl = $this->client->createShortUrl(ShortUrlCreation::forLongUrl('https://shlink.io'));
        $existingShortUrl = $this->client->getShortUrl(ShortUrlIdentifier::fromShortUrl($createdShortUrl));

        self::assertEquals('https://shlink.io', $existingShortUrl->longUrl);
        self::assertEquals($createdShortUrl, $existingShortUrl);

        $this->client->deleteShortUrl(ShortUrlIdentifier::fromShortUrl($createdShortUrl));
    }

    /**
     * @test
     * @dataProvider provideIdentifiableMethods
     */
    public function throwsExceptionWhenTryingToActOnNotFoundSHortUrl(string $method, mixed ...$extraArgs): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('invalid');

        $this->expectException(ShortUrlNotFoundException::class);

        $this->client->{$method}($identifier, ...$extraArgs);
    }

    public static function provideIdentifiableMethods(): iterable
    {
        yield 'getShortUrl' => ['getShortUrl'];
        yield 'deleteShortUrl' => ['deleteShortUrl'];
        yield 'editShortUrl' => ['editShortUrl', ShortUrlEdition::create()];
    }

    /** @test */
    public function shortUrlsCanBeListed(): void
    {
        $urls = [
            $this->client->createShortUrl(ShortUrlCreation::forLongUrl('https://shlink.io')),
            $this->client->createShortUrl(ShortUrlCreation::forLongUrl('https://shlink.io')),
            $this->client->createShortUrl(ShortUrlCreation::forLongUrl('https://shlink.io')),
        ];

        $result = $this->client->listShortUrls();

        self::assertCount(count($urls), $result);

        foreach ($urls as $url) {
            $this->client->deleteShortUrl(ShortUrlIdentifier::fromShortUrl($url));
        }
    }
}
