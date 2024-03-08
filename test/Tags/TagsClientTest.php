<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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
use Shlinkio\Shlink\SDK\Tags\Model\TagsWithStatsList;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;
use Shlinkio\Shlink\SDK\Tags\TagsClient;
use Throwable;

use function array_map;

class TagsClientTest extends TestCase
{
    private TagsClient $tagsClient;
    private MockObject & HttpClientInterface $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->tagsClient = new TagsClient($this->httpClient);
    }

    #[Test]
    public function listTagsReturnsExpectedResponse(): void
    {
        $this->assertListTags(
            ['/tags', TagsFilter::create()],
            ['foo', 'bar', 'baz'],
            fn () => $this->tagsClient->listTags(),
        );
    }

    #[Test]
    public function listTagsWithFilterReturnsExpectedResponse(): void
    {
        $filter = TagsFilter::create()->searchingBy('foo');
        $this->assertListTags(
            ['/tags', $filter],
            ['foo', 'bar', 'baz'],
            fn () => $this->tagsClient->listTagsWithFilter($filter),
        );
    }

    #[Test]
    public function listTagsWithStatsReturnsExpectedResponse(): void
    {
        $this->assertListTags(
            ['/tags/stats', $this->isType('array')],
            [[], [], [], [], []],
            fn (): array => array_map(fn () => [], [...$this->tagsClient->listTagsWithStats()]),
        );
    }

    #[Test, DataProvider('provideTagsWithStats')]
    public function listTagsWithStatsReturnsExpectedVisitsCount(array $resp, callable $assert): void
    {
        $this->httpClient->expects($this->once())->method('getFromShlink')->willReturn([
            'tags' => [
                'data' => [$resp],
            ],
        ]);
        $assert($this->tagsClient->listTagsWithStats());
    }

    public static function provideTagsWithStats(): iterable
    {
        yield 'legacy response' => [[
            'tag' => 'foo',
            'shortUrlsCount' => 1,
            'visitsCount' => 3,
        ], function (TagsWithStatsList $list): void {
            /** @var TagWithStats $item */
            foreach ($list as $item) {
                self::assertEquals(3, $item->visitsCount);
                self::assertEquals(3, $item->visitsSummary->total);
                self::assertNull($item->visitsSummary->nonBots);
                self::assertNull($item->visitsSummary->bots);
                break;
            }
        }];
        yield 'current response' => [[
            'tag' => 'foo',
            'shortUrlsCount' => 1,
            'visitsCount' => 3,
            'visitsSummary' => [
                'total' => 3,
                'nonBots' => 2,
                'bots' => 1,
            ],
        ], function (TagsWithStatsList $list): void {
            /** @var TagWithStats $item */
            foreach ($list as $item) {
                self::assertEquals(3, $item->visitsCount);
                self::assertEquals(3, $item->visitsSummary->total);
                self::assertEquals(2, $item->visitsSummary->nonBots);
                self::assertEquals(1, $item->visitsSummary->bots);
                break;
            }
        }];
        yield 'future response' => [[
            'tag' => 'foo',
            'shortUrlsCount' => 1,
            'visitsSummary' => [
                'total' => 3,
                'nonBots' => 2,
                'bots' => 1,
            ],
        ], function (TagsWithStatsList $list): void {
            /** @var TagWithStats $item */
            foreach ($list as $item) {
                self::assertEquals(3, $item->visitsCount);
                self::assertEquals(3, $item->visitsSummary->total);
                self::assertEquals(2, $item->visitsSummary->nonBots);
                self::assertEquals(1, $item->visitsSummary->bots);
                break;
            }
        }];
    }

    #[Test]
    public function listTagsWithStatsWithFilterReturnsExpectedResponse(): void
    {
        $filter = TagsFilter::create()->searchingBy('foo')->orderingAscBy(TagsListOrderField::TAG);
        $this->assertListTags(
            ['/tags/stats', $this->callback(function (array $arg) use ($filter) {
                $filterArray = $filter->toArray();
                foreach ($filterArray as $key => $expectedValue) {
                    Assert::assertEquals($expectedValue, $arg[$key]);
                }

                return true;
            })],
            [[], [], [], [], []],
            fn (): array => array_map(fn () => [], [...$this->tagsClient->listTagsWithStatsWithFilter($filter)]),
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

    #[Test]
    public function renameTagCallsApi(): void
    {
        $renaming = TagRenaming::fromOldNameAndNewName('foo', 'bar');
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with('/tags', 'PUT', $renaming);

        $this->tagsClient->renameTag($renaming);
    }

    /**
     * @param class-string<Throwable> $expectedException
     */
    #[Test, DataProvider('provideRenameExceptions')]
    public function renameTagThrowsProperExceptionOnError(HttpException $original, string $expectedException): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expectedException);

        $this->tagsClient->renameTag(TagRenaming::fromOldNameAndNewName('', ''));
    }

    public static function provideRenameExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_ARGUMENT' =>  [
            HttpException::fromPayload(['type' => ErrorType::INVALID_ARGUMENT->value]),
            InvalidDataException::class,
        ];
        yield 'FORBIDDEN_OPERATION' =>  [
            HttpException::fromPayload(['type' => ErrorType::FORBIDDEN_OPERATION->value]),
            ForbiddenTagOperationException::class,
        ];
        yield 'TAG_NOT_FOUND' =>  [
            HttpException::fromPayload(['type' => ErrorType::TAG_NOT_FOUND->value]),
            TagNotFoundException::class,
        ];
        yield 'TAG_CONFLICT' =>  [
            HttpException::fromPayload(['type' => ErrorType::TAG_CONFLICT->value]),
            TagConflictException::class,
        ];
    }

    #[Test]
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
     */
    #[Test, DataProvider('provideDeleteExceptions')]
    public function deleteTagsThrowsProperExceptionOnError(HttpException $original, string $expectedException): void
    {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($original);
        $this->expectException($expectedException);

        $this->tagsClient->deleteTags('foo');
    }

    public static function provideDeleteExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'FORBIDDEN_OPERATION' =>  [
            HttpException::fromPayload(['type' => ErrorType::FORBIDDEN_OPERATION->value]),
            ForbiddenTagOperationException::class,
        ];
    }
}
