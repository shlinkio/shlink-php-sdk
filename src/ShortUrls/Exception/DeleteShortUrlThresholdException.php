<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class DeleteShortUrlThresholdException extends RuntimeException implements ExceptionInterface
{
    private ShortUrlIdentifier $identifier;
    private int $threshold;

    private function __construct(HttpException $previous)
    {
        parent::__construct($previous->detail(), $previous->status(), $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $shortCode = $additional['shortCode'] ?? '';
        $domain = $additional['domain'] ?? null;
        $threshold = $additional['threshold'] ?? 0;

        $e = new self($prev);
        $e->threshold = $threshold;
        $e->identifier = $domain === null
            ? ShortUrlIdentifier::fromShortCode($shortCode)
            : ShortUrlIdentifier::fromShortCodeAndDomain($shortCode, $domain);

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
