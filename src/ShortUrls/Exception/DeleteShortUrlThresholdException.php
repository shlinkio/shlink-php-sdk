<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class DeleteShortUrlThresholdException extends RuntimeException implements ExceptionInterface
{
    private function __construct(
        HttpException $previous,
        public readonly ShortUrlIdentifier $identifier,
        public readonly int $threshold,
    ) {
        parent::__construct($previous->detail, $previous->status, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $shortCode = $prev->additional['shortCode'] ?? '';
        $domain = $prev->additional['domain'] ?? null;
        $threshold = $prev->additional['threshold'] ?? 0;

        return new self(
            $prev,
            $domain === null
                ? ShortUrlIdentifier::fromShortCode($shortCode)
                : ShortUrlIdentifier::fromShortCodeAndDomain($shortCode, $domain),
            $threshold,
        );
    }
}
