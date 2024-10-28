<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

class NonUniqueSlugException extends RuntimeException implements ExceptionInterface
{
    private function __construct(
        HttpException $previous,
        public readonly string $customSlug,
        public readonly string|null $domain,
    ) {
        parent::__construct($previous->detail, $previous->status, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $customSlug = $prev->additional['customSlug'] ?? '';
        $domain = $prev->additional['domain'] ?? null;

        return new self($prev, $customSlug, $domain);
    }
}
