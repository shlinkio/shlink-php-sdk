<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

/**
 * @deprecated This is no longer used in Shlink 4.0.0
 */
final readonly class DeviceLongUrls
{
    public function __construct(
        public string|null $android,
        public string|null $ios,
        public string|null $desktop,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['android'] ?? null,
            $payload['ios'] ?? null,
            $payload['desktop'] ?? null,
        );
    }
}
