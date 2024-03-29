<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class ShortUrlNotFoundExceptionTest extends TestCase
{
    #[Test, DataProvider('provideExceptions')]
    public function exceptionIsProperlyCreated(
        HttpException $prev,
        ShortUrlIdentifier $expectedIdentifier,
        string $expectedMessage,
        int $expectedCode,
    ): void {
        $e = ShortUrlNotFoundException::fromHttpException($prev);

        self::assertEquals($expectedIdentifier, $e->identifier);
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
    }

    public static function provideExceptions(): iterable
    {
        yield [HttpException::fromPayload([]), ShortUrlIdentifier::fromShortCode(''), '', -1];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 404,
        ]), ShortUrlIdentifier::fromShortCode(''), $message, $code];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 400,
            'shortCode' => $shortCode = 'foo',
        ]), ShortUrlIdentifier::fromShortCode($shortCode), $message, $code];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 400,
            'shortCode' => $shortCode = 'foo',
            'domain' => $domain = 's.test',
        ]), ShortUrlIdentifier::fromShortCodeAndDomain($shortCode, $domain), $message, $code];
    }
}
