<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags\Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;

class ForbiddenTagOperationExceptionTest extends TestCase
{
    #[Test, DataProvider('provideExceptions')]
    public function exceptionIsProperlyCreated(HttpException $prev, string $expectedMessage, int $expectedCode): void
    {
        $e = ForbiddenTagOperationException::fromHttpException($prev);

        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
    }

    public static function provideExceptions(): iterable
    {
        yield [HttpException::fromPayload([]), '', -1];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 404,
        ]), $message, $code];
    }
}
