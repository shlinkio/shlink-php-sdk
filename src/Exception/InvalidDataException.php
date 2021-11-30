<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Throwable;

class InvalidDataException extends RuntimeException implements ExceptionInterface
{
    /** @var string[] */
    private array $invalidElements;

    private function __construct(string $message, int $code, Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $invalidElements = $prev->additional()['invalidElements'];

        $e = new self('Provided data is not valid', $prev->status(), $prev);
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
