<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class TagNotFoundException extends RuntimeException implements ExceptionInterface
{
    private function __construct(HttpException $previous, private string $tag)
    {
        parent::__construct($previous->detail, $previous->status, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $tag = $prev->additional['tag'] ?? '';
        return new self($prev, $tag);
    }

    public function tag(): string
    {
        return $this->tag;
    }
}
