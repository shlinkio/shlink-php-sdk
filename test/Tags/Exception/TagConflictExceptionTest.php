<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Tags\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;

class TagConflictExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExceptions
     */
    public function exceptionIsProperlyCreated(
        HttpException $prev,
        string $expectedOldName,
        string $expectedNewName,
        string $expectedMessage,
        int $expectedCode,
    ): void {
        $e = TagConflictException::fromHttpException($prev);

        self::assertEquals($expectedOldName, $e->oldName());
        self::assertEquals($expectedNewName, $e->newName());
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
    }

    public function provideExceptions(): iterable
    {
        yield [HttpException::fromPayload([]), '', '', '', -1];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 404,
        ]), '', '', $message, $code];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 409,
            'oldName' => $oldName = 'old',
            'newName' => $newName = 'new',
        ]), $oldName, $newName, $message, $code];
    }
}
