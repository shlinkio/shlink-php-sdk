<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Throwable;

use function sprintf;

class InvalidLongUrlException extends RuntimeException implements ExceptionInterface
{
    private string $longUrl;

    private function __construct(string $message, int $code, Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $longUrl = $additional['url'] ?? '';

        $e = new self(
            sprintf('Provided URL %s is invalid. Try with a different one.', $longUrl),
            $prev->status(),
            $prev,
        );
        $e->longUrl = $longUrl;

        return $e;
    }

    public function longUrl(): string
    {
        return $this->longUrl;
    }
}
