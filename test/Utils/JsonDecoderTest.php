<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Utils;

use JsonException;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Utils\JsonDecoder;

class JsonDecoderTest extends TestCase
{
    /** @test */
    public function anExceptionIsThrownWhenProvidedDataIsNotValidJson(): void
    {
        $this->expectException(JsonException::class);
        JsonDecoder::decode('invalid_json}');
    }

    /** @test */
    public function validJsonIsProperlyDecoded(): void
    {
        $result = JsonDecoder::decode('{"foo": "bar", "baz": [1, 2, 3]}');

        self::assertEquals(['foo' => 'bar', 'baz' => [1, 2, 3]], $result);
    }
}
