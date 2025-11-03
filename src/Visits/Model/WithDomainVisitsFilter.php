<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

final class WithDomainVisitsFilter implements ArraySerializable
{
    use WithDomainVisitsFilterPayloadTrait;
}
