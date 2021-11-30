<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Throwable;

use function sprintf;

class NonUniqueSlugException extends RuntimeException implements ExceptionInterface
{
    private string $customSlug;
    private ?string $domain;

    private function __construct(string $message, int $code, Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $customSlug = $additional['customSlug'] ?? '';
        $domain = $additional['domain'] ?? null;
        $suffix = $domain === null ? '' : sprintf(' for domain "%s"', $domain);

        $e = new self(sprintf('Provided slug "%s" is already in use%s.', $customSlug, $suffix), $prev->status(), $prev);
        $e->customSlug = $customSlug;
        $e->domain = $domain;

        return $e;
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
