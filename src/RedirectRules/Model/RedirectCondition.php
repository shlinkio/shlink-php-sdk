<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

use JsonSerializable;
use Shlinkio\Shlink\SDK\ShortUrls\Model\Device;

final readonly class RedirectCondition implements JsonSerializable
{
    private function __construct(
        public RedirectConditionType $type,
        public string $matchValue,
        public ?string $matchKey = null,
        private ?string $originalType = null,
    ) {
    }

    public static function forQueryParam(string $param, string $value): self
    {
        return new self(RedirectConditionType::QUERY_PARAM, $value, $param);
    }

    public static function forLanguage(string $language): self
    {
        return new self(RedirectConditionType::LANGUAGE, $language);
    }

    public static function forDevice(Device $device): self
    {
        return new self(RedirectConditionType::DEVICE, $device->value);
    }

    public static function fromArray(array $payload): self
    {
        $originalType = $payload['type'] ?? '';
        return new self(
            type: RedirectConditionType::tryFrom($originalType) ?? RedirectConditionType::UNKNOWN,
            matchValue:  $payload['matchValue'] ?? '',
            matchKey: $payload['matchKey'] ?? null,
            originalType: $originalType,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type !== RedirectConditionType::UNKNOWN ? $this->type->value : $this->originalType,
            'matchValue' => $this->matchValue,
            'matchKey' => $this->matchKey,
        ];
    }
}
