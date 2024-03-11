<?php

declare(strict_types=1);

namespace RedirectRules\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRule;

class RedirectRuleTest extends TestCase
{
    #[Test]
    #[TestWith(['0', 1])]
    #[TestWith([-8, 1])]
    #[TestWith([1, 1])]
    #[TestWith([5, 5])]
    #[TestWith(['foo', 1])]
    #[TestWith([true, 1])]
    #[TestWith([false, 1])]
    #[TestWith([null, 1])]
    public function priorityIsAlwaysPositive(mixed $providedPriority, int $expectedPriority): void
    {
        $rule = RedirectRule::fromArray(['priority' => $providedPriority]);
        self::assertEquals($expectedPriority, $rule->priority);
    }
}
