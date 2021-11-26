<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Utils;

interface ArraySerializable
{
    public function toArray(): array;
}
