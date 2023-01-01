<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Utils\JsonDecoder;

use function array_filter;
use function in_array;
use function sprintf;

use const ARRAY_FILTER_USE_KEY;

class HttpException extends RuntimeException implements ExceptionInterface
{
    private const STANDARD_PROBLEM_DETAILS_PROPS = ['type', 'title', 'detail', 'status'];

    private function __construct(
        public readonly string $type, // TODO Make this ErrorType instead of string for v2.0.0
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
            type: self::normalizeType($payload['type'] ?? ''),
            title: $payload['title'] ?? '',
            detail: $payload['detail'] ?? '',
            status: $payload['status'] ?? -1,
            additional: $additional,
        );
    }

    /**
     * For compatibility between API v2 and v3 error types
     */
    private static function normalizeType(string $type): string
    {
        return match ($type) {
            'INVALID_ARGUMENT' => ErrorType::INVALID_ARGUMENT->value,
            'INVALID_SHORT_URL_DELETION' => ErrorType::INVALID_SHORT_URL_DELETION->value,
            'DOMAIN_NOT_FOUND' => ErrorType::DOMAIN_NOT_FOUND->value,
            'FORBIDDEN_OPERATION' => ErrorType::FORBIDDEN_OPERATION->value,
            'INVALID_URL' => ErrorType::INVALID_URL->value,
            'INVALID_SLUG' => ErrorType::INVALID_SLUG->value,
            'INVALID_SHORTCODE' => ErrorType::INVALID_SHORTCODE->value,
            'TAG_CONFLICT' => ErrorType::TAG_CONFLICT->value,
            'TAG_NOT_FOUND' => ErrorType::TAG_NOT_FOUND->value,
            'MERCURE_NOT_CONFIGURED' => ErrorType::MERCURE_NOT_CONFIGURED->value,
            'INVALID_AUTHORIZATION' => ErrorType::INVALID_AUTHORIZATION->value,
            'INVALID_API_KEY' => ErrorType::INVALID_API_KEY->value,
            default => $type,
        };
    }
}
