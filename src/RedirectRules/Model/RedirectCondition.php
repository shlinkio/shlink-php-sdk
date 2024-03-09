<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

final readonly class RedirectCondition
{
    private function __construct(
        public RedirectConditionType $type,
        public ?string $matchKey,
        public string $matchValue,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            type: RedirectConditionType::tryFrom($payload['type'] ?? '') ?? RedirectConditionType::UNKNOWN,
            matchKey: $payload['matchKey'] ?? null,
            matchValue:  $payload['matchValue'] ?? '',
        );
    }
}
