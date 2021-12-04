<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class NonUniqueSlugException extends RuntimeException implements ExceptionInterface
{
    private function __construct(HttpException $previous, private string $customSlug, private ?string $domain)
    {
        parent::__construct($previous->detail(), $previous->status(), $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $customSlug = $additional['customSlug'] ?? '';
        $domain = $additional['domain'] ?? null;

        return new self($prev, $customSlug, $domain);
    }

    public function customSlug(): string
    {
        return $this->customSlug;
    }

    public function domain(): ?string
    {
        return $this->domain;
    }
}
