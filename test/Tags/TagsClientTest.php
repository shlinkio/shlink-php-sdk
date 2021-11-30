<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Tags\TagsClient;

class TagsClientTest extends TestCase
{
    use ProphecyTrait;

    private TagsClient $tagsClient;
    private ObjectProphecy $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->prophesize(HttpClientInterface::class);
        $this->tagsClient = new TagsClient($this->httpClient->reveal());
    }

    /** @test */
    public function listTagsReturnsDataProp(): void
    {
        $get = $this->httpClient->getFromShlink('/tags', [])->willReturn([
            'tags' => [
                'data' => ['foo', 'bar', 'baz'],
            ],
        ]);

        $result = $this->tagsClient->listTags();

        self::assertEquals(['foo', 'bar', 'baz'], $result);
        $get->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listTagsWithStatsReturnsStatsProp(): void
    {
        $get = $this->httpClient->getFromShlink('/tags', ['withStats' => 'true'])->willReturn([
            'tags' => [
                'stats' => [[], [], [], [], [], ],
            ],
        ]);

        $result = $this->tagsClient->listTagsWithStats();

        self::assertCount(5, $result);
        $get->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function renameTagCallsApi(): void
    {
        $call = $this->httpClient->callShlinkWithBody('/tags', 'PUT', [
            'oldName' => 'foo',
            'newName' => 'bar',
        ]);

        $this->tagsClient->renameTag('foo', 'bar');

        $call->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function deleteTagsCallsApi(): void
    {
        $tags = ['foo', 'bar', 'baz'];
        $call = $this->httpClient->callShlinkWithBody('/tags', 'DELETE', [], ['tags' => $tags]);

        $this->tagsClient->deleteTags(...$tags);

        $call->shouldHaveBeenCalledOnce();
    }
}
