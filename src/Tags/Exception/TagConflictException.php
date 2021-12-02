<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class TagConflictException extends RuntimeException implements ExceptionInterface
{
    private string $oldName;
    private string $newName;

    private function __construct(HttpException $previous)
    {
        parent::__construct($previous->detail(), $previous->status(), $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $oldName = $additional['oldName'] ?? '';
        $newName = $additional['newName'] ?? '';

        $e = new self($prev);
        $e->oldName = $oldName;
        $e->newName = $newName;

        return $e;
    }

    public function oldName(): string
    {
        return $this->oldName;
    }

    public function newName(): string
    {
        return $this->newName;
    }
}
