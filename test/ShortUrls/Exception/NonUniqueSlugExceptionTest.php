<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\NonUniqueSlugException;

class NonUniqueSlugExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExceptions
     */
    public function exceptionIsProperlyCreated(
        HttpException $prev,
        string $expectedCustomSlug,
        ?string $expectedDomain,
        string $expectedMessage,
        int $expectedCode,
    ): void {
        $e = NonUniqueSlugException::fromHttpException($prev);

        self::assertEquals($expectedCustomSlug, $e->customSlug());
        self::assertEquals($expectedDomain, $e->domain());
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
    }

    public function provideExceptions(): iterable
    {
        yield [HttpException::fromPayload([]), '', null, '', -1];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 400,
        ]), '', null, $message, $code];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 404,
            'customSlug' => $customSlug = 'baz',
            'domain' => $domain = 'doma.in',
        ]), $customSlug, $domain, $message, $code];
    }
}
