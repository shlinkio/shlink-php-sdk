<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\ShortUrls\Exception;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\DeleteShortUrlThresholdException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class DeleteShortUrlThresholdExceptionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideExceptions
     */
    public function exceptionIsProperlyCreated(
        HttpException $prev,
        ShortUrlIdentifier $expectedIdentifier,
        int $expectedThreshold,
        string $expectedMessage,
        int $expectedCode,
    ): void {
        $e = DeleteShortUrlThresholdException::fromHttpException($prev);

        self::assertEquals($expectedIdentifier, $e->identifier());
        self::assertEquals($expectedThreshold, $e->threshold());
        self::assertEquals($expectedMessage, $e->getMessage());
        self::assertEquals($expectedCode, $e->getCode());
    }

    public function provideExceptions(): iterable
    {
        yield [HttpException::fromPayload([]), ShortUrlIdentifier::fromShortCode(''), 0, '', -1];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 404,
        ]), ShortUrlIdentifier::fromShortCode(''), 0, $message, $code];
        yield [HttpException::fromPayload([
            'detail' => $message = 'This is the message',
            'status' => $code = 400,
            'shortCode' => $shortCode = 'foo',
            'domain' => $domain = 'doma.in',
            'threshold' => $threshold = 15,
        ]), ShortUrlIdentifier::fromShortCodeAndDomain($shortCode, $domain), $threshold, $message, $code];
    }
}
