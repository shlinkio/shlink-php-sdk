<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class InvalidDataException extends RuntimeException implements ExceptionInterface
{
    /** @var string[] */
    private array $invalidElements;

    private function __construct(HttpException $previous)
    {
        parent::__construct($previous->detail(), $previous->status(), $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $invalidElements = $prev->additional()['invalidElements'];

        $e = new self($prev);
        $e->invalidElements = $invalidElements;

        return $e;
    }

    /**
     * @return string[]
     */
    public function invalidElements(): array
    {
        return $this->invalidElements;
    }
}
