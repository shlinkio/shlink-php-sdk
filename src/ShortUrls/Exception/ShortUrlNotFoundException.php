<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Throwable;

use function sprintf;

class ShortUrlNotFoundException extends RuntimeException implements ExceptionInterface
{
    private ShortUrlIdentifier $identifier;

    private function __construct(string $message, int $code, Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $shortCode = $additional['shortCode'] ?? '';
        $domain = $additional['domain'] ?? null;
        $suffix = $domain === null ? '' : sprintf(' for domain "%s"', $domain);

        $e = new self(sprintf('No URL found with short code "%s"%s', $shortCode, $suffix), $prev->status(), $prev);
        $e->identifier = $domain === null
            ? ShortUrlIdentifier::fromShortCode($shortCode)
            : ShortUrlIdentifier::fromShortCodeAndDomain($suffix, $domain);

        return $e;
    }

    public function identifier(): ShortUrlIdentifier
    {
        return $this->identifier;
    }
}
