<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Throwable;

use function sprintf;

class DeleteShortUrlThresholdException extends RuntimeException implements ExceptionInterface
{
    private ShortUrlIdentifier $identifier;
    private int $threshold;

    private function __construct(string $message, int $code, Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $shortCode = $additional['shortCode'] ?? '';
        $domain = $additional['domain'] ?? null;
        $threshold = $additional['threshold'] ?? 0;
        $suffix = $domain === null ? '' : sprintf(' for domain "%s"', $domain);

        $e = new self(sprintf(
            'Impossible to delete short URL with short code "%s"%s, since it has more than "%s" visits.',
            $shortCode,
            $suffix,
            $threshold,
        ), $prev->status(), $prev);
        $e->threshold = $threshold;
        $e->identifier = $domain === null
            ? ShortUrlIdentifier::fromShortCode($shortCode)
            : ShortUrlIdentifier::fromShortCodeAndDomain($suffix, $domain);

        return $e;
    }

    public function identifier(): ShortUrlIdentifier
    {
        return $this->identifier;
    }

    public function threshold(): int
    {
        return $this->threshold;
    }
}
