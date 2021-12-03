<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Utils\JsonDecoder;

use function array_filter;
use function in_array;

use const ARRAY_FILTER_USE_KEY;

class HttpException extends RuntimeException implements ExceptionInterface
{
    private const STANDARD_PROBLEM_DETAILS_PROPS = ['type', 'title', 'detail', 'status'];

    private function __construct(
        private string $type,
        private string $title,
        private string $detail,
        private int $status,
        private array $additional,
    ) {
        parent::__construct('An HTTP error response was returned from Shlink', $status);
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

    public function type(): string
    {
        return $this->type;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function detail(): string
    {
        return $this->detail;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function additional(): array
    {
        return $this->additional;
    }
}
