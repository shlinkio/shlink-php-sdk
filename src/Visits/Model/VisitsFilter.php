<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

final class VisitsFilter implements ArraySerializable
{
    use VisitsFilterPayloadTrait;
}
