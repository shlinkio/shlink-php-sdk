<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

use Countable;

use function array_map;
use function count;
use function max;

final readonly class RedirectRule implements Countable
{
    /**
     * @param RedirectCondition[] $conditions
     * @param positive-int $priority
     */
    private function __construct(public string $longUrl, public int $priority, public array $conditions)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            longUrl: $payload['longUrl'] ?? '',
            priority: max((int) ($payload['priority'] ?? 1), 1),
            conditions: array_map(
                static fn (array $condition) => RedirectCondition::fromArray($condition),
                $payload['conditions'] ?? [],
            ),
        );
    }

    public function count(): int
    {
        return count($this->conditions);
    }
}
