<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class TagConflictException extends RuntimeException implements ExceptionInterface
{
    private function __construct(HttpException $previous, private string $oldName, private string $newName)
    {
        parent::__construct($previous->detail, $previous->status, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $oldName = $prev->additional['oldName'] ?? '';
        $newName = $prev->additional['newName'] ?? '';

        return new self($prev, $oldName, $newName);
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
