<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

use Countable;

use function array_map;
use function count;

final readonly class RedirectRulesList implements Countable
{
    /**
     * @param RedirectRule[] $redirectRules
     */
    private function __construct(public string $defaultLongUrl, public array $redirectRules) {}

    /**
     * @param array{defaultLongUrl?: string, redirectRules?: list<mixed>} $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            defaultLongUrl: $payload['defaultLongUrl'] ?? '',
            redirectRules: array_map(
                RedirectRule::fromArray(...),
                $payload['redirectRules'] ?? [],
            ),
        );
    }

    public function count(): int
    {
        return count($this->redirectRules);
    }
}
