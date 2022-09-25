<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class InvalidDataException extends RuntimeException implements ExceptionInterface
{
    /**
     * @param string[] $invalidElements
     */
    private function __construct(HttpException $previous, public readonly array $invalidElements)
    {
        parent::__construct($previous->detail, $previous->status, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $invalidElements = $prev->additional['invalidElements'] ?? [];
        return new self($prev, $invalidElements);
    }
}
