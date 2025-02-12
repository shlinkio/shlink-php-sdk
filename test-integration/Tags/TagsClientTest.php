<?php

declare(strict_types=1);

namespace ShlinkioIntegrationTest\Shlink\SDK\Tags;

use PHPUnit\Framework\Attributes\Test;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\TagsClient;
use ShlinkioIntegrationTest\Shlink\SDK\TestCase\AbstractTestCase;

use function sort;

class TagsClientTest extends AbstractTestCase
{
    private const array BASE_TAGS = ['foo', 'bar', 'baz'];

    private TagsClient $client;

    protected function setUp(): void
    {
        $this->client = new TagsClient(self::httpClient());

        self::shlinkClient()->createShortUrl(
            ShortUrlCreation::forLongUrl('https://example.com')->withTags(...self::BASE_TAGS),
        );
    }

    #[Test]
    public function tagsCanBeListed(): void
    {
        $tagsList = $this->client->listTags();
        $expectedTags = [...self::BASE_TAGS];
        sort($expectedTags);
        self::assertEquals($expectedTags, $tagsList);
    }

    #[Test]
    public function tagsCanBeRenamed(): void
    {
        $this->client->renameTag(TagRenaming::fromOldNameAndNewName(oldName: 'bar', newName: 'bar_renamed'));
        self::assertEquals(['bar_renamed', 'baz', 'foo'], $this->client->listTags());
    }

    #[Test]
    public function tagsCanBeDeleted(): void
    {
        $this->client->deleteTags('foo', 'bar');
        self::assertEquals(['baz'], $this->client->listTags());
    }
}
