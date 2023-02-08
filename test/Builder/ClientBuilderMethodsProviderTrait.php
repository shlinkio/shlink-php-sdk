<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Builder;

use ReflectionClass;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilderInterface;

trait ClientBuilderMethodsProviderTrait
{
    public static function provideMethods(): iterable
    {
        $ref = new ReflectionClass(ShlinkClientBuilderInterface::class);

        foreach ($ref->getMethods() as $method) {
            $name = $method->getName();
            yield $name => [$name];
        }
    }
}
