<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;

class HttpException extends RuntimeException implements ExceptionInterface
{
    public static function fromNonSuccessfulResponse(ResponseInterface $resp): self
    {
        return new self('HTTP Error'); // TODO
    }
}
