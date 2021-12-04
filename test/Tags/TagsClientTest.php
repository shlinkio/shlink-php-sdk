<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
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
        $renaming = TagRenaming::fromOldNameAndNewName('foo', 'bar');
        $call = $this->httpClient->callShlinkWithBody('/tags', 'PUT', $renaming);

        $this->tagsClient->renameTag($renaming);

        $call->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideRenameExceptions
     */
    public function renameTagThrowsProperExceptionOnError(HttpException $original, string $expectedException): void
    {
        $call = $this->httpClient->callShlinkWithBody(Argument::cetera())->willThrow($original);

        $call->shouldBeCalledOnce();
        $this->expectException($expectedException);

        $this->tagsClient->renameTag(TagRenaming::fromOldNameAndNewName('', ''));
    }

    public function provideRenameExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_ARGUMENT type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_ARGUMENT']),
            InvalidDataException::class,
        ];
        yield 'FORBIDDEN_OPERATION type' =>  [
            HttpException::fromPayload(['type' => 'FORBIDDEN_OPERATION']),
            ForbiddenTagOperationException::class,
        ];
        yield 'TAG_NOT_FOUND type' =>  [
            HttpException::fromPayload(['type' => 'TAG_NOT_FOUND']),
            TagNotFoundException::class,
        ];
        yield 'TAG_CONFLICT type' =>  [
            HttpException::fromPayload(['type' => 'TAG_CONFLICT']),
            TagConflictException::class,
        ];
    }

    /** @test */
    public function deleteTagsCallsApi(): void
    {
        $tags = ['foo', 'bar', 'baz'];
        $call = $this->httpClient->callShlinkWithBody('/tags', 'DELETE', [], ['tags' => $tags]);

        $this->tagsClient->deleteTags(...$tags);

        $call->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideDeleteExceptions
     */
    public function deleteTagsThrowsProperExceptionOnError(HttpException $original, string $expectedException): void
    {
        $call = $this->httpClient->callShlinkWithBody(Argument::cetera())->willThrow($original);

        $call->shouldBeCalledOnce();
        $this->expectException($expectedException);

        $this->tagsClient->deleteTags('foo');
    }

    public function provideDeleteExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'FORBIDDEN_OPERATION type' =>  [
            HttpException::fromPayload(['type' => 'FORBIDDEN_OPERATION']),
            ForbiddenTagOperationException::class,
        ];
    }
}
