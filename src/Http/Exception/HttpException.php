<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Utils\JsonDecoder;

use function array_filter;
use function in_array;
use function sprintf;

use const ARRAY_FILTER_USE_KEY;

class HttpException extends RuntimeException implements ExceptionInterface
{
    private const STANDARD_PROBLEM_DETAILS_PROPS = ['type', 'title', 'detail', 'status'];

    private function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly string $detail,
        public readonly int $status,
        public readonly array $additional,
    ) {
        parent::__construct(sprintf('An HTTP error response was returned from Shlink: %s', $this->detail), $status);
    }

    public static function fromNonSuccessfulResponse(ResponseInterface $resp): self
    {
        return self::fromPayload(JsonDecoder::decode($resp->getBody()->__toString()));
    }

    public static function fromPayload(array $payload): self
    {
        $additional = array_filter(
            $payload,
            static fn (string $key) => ! in_array($key, self::STANDARD_PROBLEM_DETAILS_PROPS, true),
            ARRAY_FILTER_USE_KEY,
        );

        return new self(
            $payload['type'] ?? '',
            $payload['title'] ?? '',
            $payload['detail'] ?? '',
            $payload['status'] ?? -1,
            $additional,
        );
    }
}
