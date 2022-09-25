<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;

class TagNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExceptions
     */
    public function exceptionIsProperlyCreated(
        HttpException $prev,
        string $expectedTag,
        string $expectedMessage,
        int $expectedCode,
    ): void {
        $e = TagNotFoundException::fromHttpException($prev);

        self::assertEquals($expectedTag, $e->tag);
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
    }

    public function provideExceptions(): iterable
    {
        yield [HttpException::fromPayload([]), '', '', -1];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 400,
        ]), '', $message, $code];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 404,
            'tag' => $tag = 'foo',
        ]), $tag, $message, $code];
    }
}
