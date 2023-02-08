<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Domains\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Domains\Exception\DomainNotFoundException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class DomainNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExceptions
     */
    public function exceptionIsProperlyCreated(
        HttpException $prev,
        string $expectedDomain,
        string $expectedMessage,
        int $expectedCode,
    ): void {
        $e = DomainNotFoundException::fromHttpException($prev);

        self::assertEquals($expectedDomain, $e->authority);
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
    }

    public static function provideExceptions(): iterable
    {
        yield [HttpException::fromPayload([]), '', '', -1];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 400,
        ]), '', $message, $code];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message again',
            'status' => $code = 404,
            'authority' => $authority = 'authority',
        ]), $authority, $message, $code];
    }
}
