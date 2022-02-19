<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
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
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsListOrderFields;
use Shlinkio\Shlink\SDK\Tags\TagsClient;

class TagsClientTest extends TestCase
{
    use ArraySubsetAsserts;
    use ProphecyTrait;

    private TagsClient $tagsClient;
    private ObjectProphecy $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->prophesize(HttpClientInterface::class);
        $this->tagsClient = new TagsClient($this->httpClient->reveal());
    }

    /** @test */
    public function listTagsReturnsExpectedResponse(): void
    {
        $this->assertListTags(
            ['/tags', TagsFilter::create()],
            ['foo', 'bar', 'baz'],
            fn () => $this->tagsClient->listTags(),
        );
    }

    /** @test */
    public function listTagsWithFilterReturnsExpectedResponse(): void
    {
        $filter = TagsFilter::create()->searchingBy('foo');
        $this->assertListTags(
            ['/tags', $filter],
            ['foo', 'bar', 'baz'],
            fn () => $this->tagsClient->listTagsWithFilter($filter),
        );
    }

    /** @test */
    public function listTagsWithStatsReturnsExpectedResponse(): void
    {
        $this->assertListTags(
            ['/tags/stats', Argument::type('array')],
            [[], [], [], [], []],
            function (): array {
                $iterable = $this->tagsClient->listTagsWithStats();
                $result = [];

                foreach ($iterable as $value) {
                    $result[] = [];
                }

                return $result;
            },
        );
    }

    /** @test */
    public function listTagsWithStatsWithFilterReturnsExpectedResponse(): void
    {
        $filter = TagsFilter::create()->searchingBy('foo')->orderingAscBy(TagsListOrderFields::TAG);
        $test = $this;
        $this->assertListTags(
            ['/tags/stats', Argument::that(function (array $arg) use ($filter, $test) {
                $test->assertArraySubset($filter->toArray(), $arg);
                return true;
            })],
            [[], [], [], [], []],
            function () use ($filter): array {
                $iterable = $this->tagsClient->listTagsWithStatsWithFilter($filter);
                $result = [];

                foreach ($iterable as $value) {
                    $result[] = [];
                }

                return $result;
            },
        );
    }

    private function assertListTags(array $expectedArgs, array $expectedData, callable $listTags): void
    {
        $get = $this->httpClient->getFromShlink(...$expectedArgs)->willReturn([
            'tags' => [
                'data' => $expectedData,
            ],
        ]);

        $result = $listTags();

        self::assertEquals($expectedData, $result);
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
