<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

use JsonSerializable;

final readonly class RedirectRuleData implements JsonSerializable
{
    /**
     * @param RedirectCondition[] $conditions
     */
    private function __construct(public string $longUrl, public array $conditions)
    {
    }

    public static function forLongUrl(string $longUrl): self
    {
        return new self($longUrl, []);
    }

    public static function fromRedirectRule(RedirectRule $redirectRule): self
    {
        return new self($redirectRule->longUrl, $redirectRule->conditions);
    }

    public function withCondition(RedirectCondition $newCondition): self
    {
        return new self($this->longUrl, [...$this->conditions, $newCondition]);
    }

    public function jsonSerialize(): array
    {
        return [
            'longUrl' => $this->longUrl,
            'conditions' => $this->conditions,
        ];
    }
}
