<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsListOrderField;
use Shlinkio\Shlink\SDK\Tags\TagsClient;
use Throwable;

class TagsClientTest extends TestCase
{
    use ArraySubsetAsserts;

    private TagsClient $tagsClient;
    private MockObject & HttpClientInterface $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->tagsClient = new TagsClient($this->httpClient);
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
            ['/tags/stats', $this->isType('array')],
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
        $filter = TagsFilter::create()->searchingBy('foo')->orderingAscBy(TagsListOrderField::TAG);
        $test = $this;
        $this->assertListTags(
            ['/tags/stats', $this->callback(function (array $arg) use ($filter, $test) {
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
        $this->httpClient->expects($this->once())->method('getFromShlink')->with(...$expectedArgs)->willReturn([
            'tags' => [
                'data' => $expectedData,
            ],
        ]);

        $result = $listTags();

        self::assertEquals($expectedData, $result);
    }

    /** @test */
    public function renameTagCallsApi(): void
    {
        $renaming = TagRenaming::fromOldNameAndNewName('foo', 'bar');
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with('/tags', 'PUT', $renaming);

        $this->tagsClient->renameTag($renaming);
    }

    /**
     * @param class-string<Throwable> $expectedException
     * @test
     * @dataProvider provideRenameExceptions
     */
    public function renameTagThrowsProperExceptionOnError(HttpException $original, string $expectedException): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expectedException);

        $this->tagsClient->renameTag(TagRenaming::fromOldNameAndNewName('', ''));
    }

    public function provideRenameExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_ARGUMENT v2 type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_ARGUMENT']),
            InvalidDataException::class,
        ];
        yield 'INVALID_ARGUMENT v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_ARGUMENT->value]),
            InvalidDataException::class,
        ];
        yield 'FORBIDDEN_OPERATION v2 type' =>  [
            HttpException::fromPayload(['type' => 'FORBIDDEN_OPERATION']),
            ForbiddenTagOperationException::class,
        ];
        yield 'FORBIDDEN_OPERATION v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::FORBIDDEN_OPERATION->value]),
            ForbiddenTagOperationException::class,
        ];
        yield 'TAG_NOT_FOUND v2 type' =>  [
            HttpException::fromPayload(['type' => 'TAG_NOT_FOUND']),
            TagNotFoundException::class,
        ];
        yield 'TAG_NOT_FOUND v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::TAG_NOT_FOUND->value]),
            TagNotFoundException::class,
        ];
        yield 'TAG_CONFLICT v2 type' =>  [
            HttpException::fromPayload(['type' => 'TAG_CONFLICT']),
            TagConflictException::class,
        ];
        yield 'TAG_CONFLICT v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::TAG_CONFLICT->value]),
            TagConflictException::class,
        ];
    }

    /** @test */
    public function deleteTagsCallsApi(): void
    {
        $tags = ['foo', 'bar', 'baz'];
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with(
            '/tags',
            'DELETE',
            [],
            ['tags' => $tags],
        );

        $this->tagsClient->deleteTags(...$tags);
    }

    /**
     * @param class-string<Throwable> $expectedException
     * @test
     * @dataProvider provideDeleteExceptions
     */
    public function deleteTagsThrowsProperExceptionOnError(HttpException $original, string $expectedException): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expectedException);

        $this->tagsClient->deleteTags('foo');
    }

    public function provideDeleteExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'FORBIDDEN_OPERATION v2 type' =>  [
            HttpException::fromPayload(['type' => 'FORBIDDEN_OPERATION']),
            ForbiddenTagOperationException::class,
        ];
        yield 'FORBIDDEN_OPERATION v3 type' =>  [
            HttpException::fromPayload(['type' => ErrorType::FORBIDDEN_OPERATION->value]),
            ForbiddenTagOperationException::class,
        ];
    }
}
