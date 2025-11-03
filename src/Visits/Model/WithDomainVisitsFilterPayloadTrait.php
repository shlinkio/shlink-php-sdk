<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

trait WithDomainVisitsFilterPayloadTrait
{
    use VisitsFilterPayloadTrait;

    public function forDomain(string $domain): self
    {
        return $this->cloneWithProp('domain', $domain);
    }
}
