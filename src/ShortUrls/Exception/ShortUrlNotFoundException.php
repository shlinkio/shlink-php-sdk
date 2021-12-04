<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Exception;

use RuntimeException;
use Shlinkio\Shlink\SDK\Exception\ExceptionInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class ShortUrlNotFoundException extends RuntimeException implements ExceptionInterface
{
    private function __construct(HttpException $previous, private ShortUrlIdentifier $identifier)
    {
        parent::__construct($previous->detail(), $previous->status(), $previous);
    }

    public static function fromHttpException(HttpException $prev): self
    {
        $additional = $prev->additional();
        $shortCode = $additional['shortCode'] ?? '';
        $domain = $additional['domain'] ?? null;

        return new self(
            $prev,
            $domain === null
                ? ShortUrlIdentifier::fromShortCode($shortCode)
                : ShortUrlIdentifier::fromShortCodeAndDomain($shortCode, $domain),
        );
    }

    public function identifier(): ShortUrlIdentifier
    {
        return $this->identifier;
    }
}
