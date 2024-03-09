<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Http\Exception;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

use function json_encode;

use const JSON_THROW_ON_ERROR;

class HttpExceptionTest extends TestCase
{
    #[Test, DataProvider('providePayloads')]
    public function exceptionIsCreatedAsExpectedFromPayload(
        array $payload,
        ErrorType $expectedType,
        string $expectedTitle,
        string $expectedDetail,
        int $expectedStatus,
        array $expectedAdditional,
    ): void {
        $e = HttpException::fromPayload($payload);
        $this->runAssertions($e, $expectedType, $expectedTitle, $expectedDetail, $expectedStatus, $expectedAdditional);
    }

    #[Test, DataProvider('providePayloads')]
    public function exceptionIsCreatedAsExpectedFromResponse(
        array $payload,
        ErrorType $expectedType,
        string $expectedTitle,
        string $expectedDetail,
        int $expectedStatus,
        array $expectedAdditional,
    ): void {
        $e = HttpException::fromNonSuccessfulResponse(
            (new Response())->withBody(Utils::streamFor(json_encode($payload, JSON_THROW_ON_ERROR))),
        );
        $this->runAssertions($e, $expectedType, $expectedTitle, $expectedDetail, $expectedStatus, $expectedAdditional);
    }

    private function runAssertions(
        HttpException $e,
        ErrorType $expectedType,
        string $expectedTitle,
        string $expectedDetail,
        int $expectedStatus,
        array $expectedAdditional,
    ): void {
        $message = $e->getMessage();

        self::assertStringStartsWith('An HTTP error response was returned from Shlink: ', $message);
        if ($expectedDetail !== '') {
            self::assertStringEndsWith($expectedDetail, $message);
        }
        self::assertEquals($expectedType, $e->type);
        self::assertEquals($expectedTitle, $e->title);
        self::assertEquals($expectedDetail, $e->detail);
        self::assertEquals($expectedStatus, $e->status);
        self::assertEquals($expectedStatus, $e->getCode());
        self::assertEquals($expectedAdditional, $e->additional);
    }

    public static function providePayloads(): iterable
    {
        yield 'no payload' => [[], ErrorType::UNKNOWN, '', '', -1, []];
        yield 'no additional props' => [
            [
                'type' => ErrorType::SHORT_URL_NOT_FOUND->value,
                'title' => 'bar',
                'detail' => 'baz',
                'status' => 500,
            ],
            ErrorType::SHORT_URL_NOT_FOUND,
            'bar',
            'baz',
            500,
            [],
        ];
        yield 'additional props' => [
            [
                'foo' => 'foo',
                'title' => 'bar',
                'bar' => 'baz',
                'status' => 500,
            ],
            ErrorType::UNKNOWN,
            'bar',
            '',
            500,
            [
                'foo' => 'foo',
                'bar' => 'baz',
            ],
        ];
    }
}
