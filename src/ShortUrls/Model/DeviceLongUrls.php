<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

final class DeviceLongUrls
{
    public function __construct(
        public readonly ?string $android,
        public readonly ?string $ios,
        public readonly ?string $desktop,
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
