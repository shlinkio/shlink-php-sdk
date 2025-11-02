<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

final class WithDomainVisitsFilter implements ArraySerializable
{
    use VisitsFilterPayloadTrait;

    public function forDomain(string $domain): self
    {
        return $this->cloneWithProp('domain', $domain);
    }
}
