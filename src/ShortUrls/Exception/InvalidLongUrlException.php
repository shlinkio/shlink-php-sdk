<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class InvalidLongUrlException extends RuntimeException implements ExceptionInterface
{
    private function __construct(HttpException $previous, private string $longUrl)
    {
        parent::__construct($previous->detail(), $previous->status(), $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $longUrl = $prev->additional()['url'] ?? '';
        return new self($prev, $longUrl);
    }

    public function longUrl(): string
    {
        return $this->longUrl;
    }
}
