<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

use JsonSerializable;

use function array_values;
use function count;
use function ksort;

final readonly class SetRedirectRules implements JsonSerializable
{
    /**
     * @param array<positive-int, RedirectRuleData> $rules
     */
    private function __construct(private array $rules)
    {
    }

    public static function fromScratch(): self
    {
        return new self([]);
    }

    public static function fromRedirectRulesList(RedirectRulesList $list): self
    {
        $priorityIndexedRules = [];
        foreach ($list->redirectRules as $rule) {
            $priorityIndexedRules[$rule->priority] = RedirectRuleData::fromRedirectRule($rule);
        }

        return new self($priorityIndexedRules);
    }

    /**
     * @param positive-int $priority
     */
    public function withoutRule(int $priority): self
    {
        // Unset rule at requested priority
        $tempRules = $this->rules;
        unset($tempRules[$priority]);

        // Recalculate priorities
        $newRules = [];
        foreach ($tempRules as $index => $rule) {
            $newRules[$index + 1] = $rule;
        }

        return new self($newRules);
    }

    /**
     * @param positive-int $priority
     */
    public function withRule(int $priority, RedirectRuleData $newRule): self
    {
        $newRules = [];
        foreach ($this->rules as $rulePriority => $rule) {
            if ($rulePriority < $priority) {
                $newRules[$rulePriority] = $rule;
            } else {
                $newRules[$rulePriority + 1] = $rule;
            }
        }
        $newRules[$priority] = $newRule;

        // Sort rules by their key, which is the priority
        ksort($newRules);

        return new self($newRules);
    }

    public function withPushedRule(RedirectRuleData $newRule): self
    {
        return $this->withRule(count($this->rules) + 1, $newRule);
    }

    public function jsonSerialize(): array
    {
        return [
            'redirectRules' => array_values($this->rules),
        ];
    }
}
