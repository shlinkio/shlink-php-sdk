<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\RedirectRules;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectConditionType;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
use Shlinkio\Shlink\SDK\RedirectRules\RedirectRulesClient;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\Device;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Throwable;

use function sprintf;

class RedirectRulesClientTest extends TestCase
{
    private RedirectRulesClient $client;
    private MockObject & HttpClientInterface $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = new RedirectRulesClient($this->httpClient);
    }

    #[Test, DataProvider('provideShortUrls')]
    public function getShortUrlRedirectRulesCallsHttpClient(ShortUrlIdentifier $identifier): void
    {
        [$shortCode, $query] = $identifier->toShortCodeAndQuery();

        $this->httpClient->expects($this->once())->method('getFromShlink')->with(
            sprintf('/short-urls/%s/redirect-rules', $shortCode),
            $query,
        )->willReturn([
            'defaultLongUrl' => 'https://example.com/default',
            'redirectRules' => [
                [
                    'longUrl' => 'https://example.com/android',
                    'priority' => 1,
                    'conditions' => [
                        [
                            'type' => RedirectConditionType::DEVICE->value,
                            'matchValue' => Device::ANDROID->value,
                            'matchKey' => null,
                        ],
                    ],
                ],
                [
                    'longUrl' => 'https://example.com/freanch-and-foo-bar-query',
                    'priority' => 2,
                    'conditions' => [
                        [
                            'type' => RedirectConditionType::LANGUAGE->value,
                            'matchValue' => 'fr',
                            'matchKey' => null,
                        ],
                        [
                            'type' => RedirectConditionType::QUERY_PARAM->value,
                            'matchValue' => 'bar',
                            'matchKey' => 'foo',
                        ],
                    ],
                ],
            ],
        ]);

        $result = $this->client->getShortUrlRedirectRules($identifier);

        self::assertCount(2, $result);
        self::assertEquals('https://example.com/default', $result->defaultLongUrl);

        self::assertCount(1, $result->redirectRules[0]);
        self::assertEquals('https://example.com/android', $result->redirectRules[0]->longUrl);
        self::assertEquals(1, $result->redirectRules[0]->priority);
        self::assertEquals(RedirectConditionType::DEVICE, $result->redirectRules[0]->conditions[0]->type);
        self::assertEquals(Device::ANDROID->value, $result->redirectRules[0]->conditions[0]->matchValue);
        self::assertNull($result->redirectRules[0]->conditions[0]->matchKey);

        self::assertCount(2, $result->redirectRules[1]);
        self::assertEquals('https://example.com/freanch-and-foo-bar-query', $result->redirectRules[1]->longUrl);
        self::assertEquals(2, $result->redirectRules[1]->priority);
        self::assertEquals(RedirectConditionType::LANGUAGE, $result->redirectRules[1]->conditions[0]->type);
        self::assertEquals('fr', $result->redirectRules[1]->conditions[0]->matchValue);
        self::assertNull($result->redirectRules[1]->conditions[0]->matchKey);
        self::assertEquals(RedirectConditionType::QUERY_PARAM, $result->redirectRules[1]->conditions[1]->type);
        self::assertEquals('bar', $result->redirectRules[1]->conditions[1]->matchValue);
        self::assertEquals('foo', $result->redirectRules[1]->conditions[1]->matchKey);
    }

    /**
     * @param class-string<Throwable> $expectedException
     */
    #[Test, DataProvider('provideGetException')]
    public function getShortUrlRedirectRulesThrowsExpectedException(
        HttpException $httpException,
        string $expectedException,
    ): void {
        $this->httpClient->expects($this->once())->method('getFromShlink')->willThrowException($httpException);
        $this->expectException($expectedException);

        $this->client->getShortUrlRedirectRules(ShortUrlIdentifier::fromShortCode('foo'));
    }

    public static function provideGetException(): iterable
    {
        yield 'SHORT_URL_NOT_FOUND' => [
            HttpException::fromPayload(['type' => ErrorType::SHORT_URL_NOT_FOUND->value]),
            ShortUrlNotFoundException::class,
        ];
        yield 'unknown' => [HttpException::fromPayload(['type' => 'unknown']), HttpException::class];
    }

    #[Test, DataProvider('provideShortUrls')]
    public function setShortUrlRedirectRulesCallsHttpClient(ShortUrlIdentifier $identifier): void
    {
        [$shortCode, $query] = $identifier->toShortCodeAndQuery();
        $rules = SetRedirectRules::fromScratch();

        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->with(
            sprintf('/short-urls/%s/redirect-rules', $shortCode),
            'POST',
            $rules,
            $query,
        )->willReturn([
            'defaultLongUrl' => 'https://example.com/default',
            'redirectRules' => [],
        ]);

        $result = $this->client->setShortUrlRedirectRules($identifier, $rules);

        self::assertCount(0, $result);
        self::assertEquals('https://example.com/default', $result->defaultLongUrl);
    }

    public static function provideShortUrls(): iterable
    {
        yield 'short code' => [ShortUrlIdentifier::fromShortCode('foo')];
        yield 'short code and domain' => [ShortUrlIdentifier::fromShortCodeAndDomain('bar', 's.test')];
    }

    /**
     * @param class-string<Throwable> $expectedException
     */
    #[Test, DataProvider('provideSetException')]
    public function setShortUrlRedirectRulesThrowsExpectedException(
        HttpException $httpException,
        string $expectedException,
    ): void {
        $this->httpClient->expects($this->once())->method('callShlinkWithBody')->willThrowException($httpException);
        $this->expectException($expectedException);

        $this->client->setShortUrlRedirectRules(
            ShortUrlIdentifier::fromShortCode('foo'),
            SetRedirectRules::fromScratch(),
        );
    }

    public static function provideSetException(): iterable
    {
        yield 'SHORT_URL_NOT_FOUND' => [
            HttpException::fromPayload(['type' => ErrorType::SHORT_URL_NOT_FOUND->value]),
            ShortUrlNotFoundException::class,
        ];
        yield 'INVALID_DATA' => [
            HttpException::fromPayload(['type' => ErrorType::INVALID_DATA->value]),
            InvalidDataException::class,
        ];
        yield 'unknown' => [HttpException::fromPayload(['type' => 'unknown']), HttpException::class];
    }
}
