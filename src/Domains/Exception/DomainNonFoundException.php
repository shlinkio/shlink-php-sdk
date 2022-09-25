<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class DomainNonFoundException extends RuntimeException implements ExceptionInterface
{
    private function __construct(HttpException $previous, public readonly string $authority)
    {
        parent::__construct($previous->detail, $previous->status, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $authority = $prev->additional['authority'] ?? '';
        return new self($prev, $authority);
    }
}
