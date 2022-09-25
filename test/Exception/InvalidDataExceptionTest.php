<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class InvalidDataExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExceptions
     */
    public function exceptionIsBuiltAsExpected(
        HttpException $prev,
        string $expectedMessage,
        int $expectedCode,
        array $expectedInvalidElements,
    ): void {
        $e = InvalidDataException::fromHttpException($prev);

        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
        self::assertEquals($expectedInvalidElements, $e->invalidElements);
    }

    public function provideExceptions(): iterable
    {
        yield 'no invalidElements' => [
            HttpException::fromPayload(['detail' => $message = 'This is the message']),
            $message,
            -1,
            [],
        ];
        yield 'invalidElements' => [
            HttpException::fromPayload(
                ['detail' => $message = 'Foobar', 'invalidElements' => $invalidElements = ['foo', 'bar']],
            ),
            $message,
            -1,
            $invalidElements,
        ];
        yield 'custom status' => [
            HttpException::fromPayload(['status' => $status = 404]),
            '',
            $status,
            [],
        ];
    }
}
