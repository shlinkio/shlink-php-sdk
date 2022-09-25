<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Visits;

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Visits\Model\VisitInterface;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\VisitsClient;

use function array_key_exists;
use function count;
use function sprintf;

class VisitsClientTest extends TestCase
{
    use ProphecyTrait;

    private VisitsClient $visitsClient;
    private ObjectProphecy $httpClient;
    private string $now;

    public function setUp(): void
    {
        $this->httpClient = $this->prophesize(HttpClientInterface::class);
        $this->visitsClient = new VisitsClient($this->httpClient->reveal());
        $this->now = (new DateTimeImmutable())->format(DateTimeInterface::ATOM);
    }

    /** @test */
    public function getVisitsSummaryPerformsExpectedCall(): void
    {
        $call = $this->httpClient->getFromShlink('/visits')->willReturn([
            'visits' => [
                'visitsCount' => 200,
                'orphanVisitsCount' => 38,
            ],
        ]);

        $result = $this->visitsClient->getVisitsSummary();

        self::assertEquals(200, $result->visitsCount());
        self::assertEquals(38, $result->orphanVisitsCount());
        self::assertCount(238, $result);
        $call->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideShortUrls
     */
    public function listShortUrlVisitsPerformsExpectedCall(ShortUrlIdentifier $identifier): void
    {
        $amountOfPages = 3;

        $get = $this->httpClient->getFromShlink(
            sprintf('/short-urls/%s/visits', $identifier->shortCode),
            Argument::that(function (array $query) use ($identifier) {
                $domain = $identifier->domain;
                return $domain === null ? ! array_key_exists('domain', $query) : $query['domain'] === $domain;
            }),
        )->will($this->buildPaginationImplementation($amountOfPages));

        $result = $this->visitsClient->listShortUrlVisits($identifier);

        $this->assertPaginator($result, $amountOfPages);
        $get->shouldHaveBeenCalledTimes($amountOfPages);
    }

    public function provideShortUrls(): iterable
    {
        yield [ShortUrlIdentifier::fromShortCode('foo')];
        yield [ShortUrlIdentifier::fromShortCodeAndDomain('bar', 'doma.in')];
    }

    /**
     * @test
     * @dataProvider provideShortUrlExceptions
     */
    public function listShortUrlVisitsThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $get = $this->httpClient->getFromShlink(Argument::cetera())->willThrow($original);

        $get->shouldBeCalledOnce();
        $this->expectException($expected);

        $this->visitsClient->listShortUrlVisits(ShortUrlIdentifier::fromShortCode('foo'));
    }

    public function provideShortUrlExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'INVALID_SHORTCODE type' =>  [
            HttpException::fromPayload(['type' => 'INVALID_SHORTCODE']),
            ShortUrlNotFoundException::class,
        ];
    }

    /** @test */
    public function listTagVisitsPerformsExpectedCall(): void
    {
        $amountOfPages = 5;
        $get = $this->httpClient->getFromShlink('/tags/foo/visits', Argument::cetera())->will(
            $this->buildPaginationImplementation($amountOfPages),
        );

        $result = $this->visitsClient->listTagVisits('foo');

        $this->assertPaginator($result, $amountOfPages);
        $get->shouldHaveBeenCalledTimes($amountOfPages);
    }

    /**
     * @test
     * @dataProvider provideTagExceptions
     */
    public function listTagVisitsThrowsProperExceptionOnError(HttpException $original, string $expected): void
    {
        $get = $this->httpClient->getFromShlink(Argument::cetera())->willThrow($original);

        $get->shouldBeCalledOnce();
        $this->expectException($expected);

        $this->visitsClient->listTagVisits('foo');
    }

    public function provideTagExceptions(): iterable
    {
        yield 'no type' => [HttpException::fromPayload([]), HttpException::class];
        yield 'not expected type' =>  [HttpException::fromPayload(['type' => 'something else']), HttpException::class];
        yield 'TAG_NOT_FOUND type' =>  [
            HttpException::fromPayload(['type' => 'TAG_NOT_FOUND']),
            TagNotFoundException::class,
        ];
    }

    /** @test */
    public function listOrphanVisitsPerformsExpectedCall(): void
    {
        $amountOfPages = 1;
        $get = $this->httpClient->getFromShlink('/visits/orphan', Argument::cetera())->will(
            $this->buildPaginationImplementation($amountOfPages),
        );

        $result = $this->visitsClient->listOrphanVisits();

        $this->assertPaginator($result, $amountOfPages);
        $get->shouldHaveBeenCalledTimes($amountOfPages);
    }

    /**
     * @param VisitsList|VisitInterface[] $result
     */
    private function assertPaginator(VisitsList $result, int $amountOfPages): void
    {
        self::assertCount($amountOfPages * 2, $result);

        $count = 0;
        foreach ($result as $index => $visit) {
            $count++;
            self::assertStringStartsWith('referer_', $visit->referer());
            self::assertStringStartsWith('userAgent_', $visit->userAgent());
            self::assertStringEndsWith($index % 2 === 0 ? '_1' : '_2', $visit->referer());
            self::assertStringEndsWith($index % 2 === 0 ? '_1' : '_2', $visit->userAgent());
            self::assertStringStartsWith($visit->date()->format('Y-m-d'), $this->now);
        }

        self::assertEquals($amountOfPages * 2, $count);
    }

    private function buildPaginationImplementation(int $amountOfPages): Closure
    {
        $now = $this->now;
        return function (array $args) use ($amountOfPages, $now) {
            $page = $args[1]['page'];
            $data = [
                [
                    'referer' => 'referer_' . $page . '_1',
                    'userAgent' => 'userAgent_' . $page . '_1',
                    'date' => $now,
                ],
                [
                    'referer' => 'referer_' . $page . '_2',
                    'userAgent' => 'userAgent_' . $page . '_2',
                    'date' => $now,
                ],
            ];

            return [
                'visits' => [
                    'data' => $data,
                    'pagination' => [
                        'currentPage' => $page,
                        'pagesCount' => $amountOfPages,
                        'totalItems' => $amountOfPages * count($data),
                    ],
                ],
            ];
        };
    }
}
