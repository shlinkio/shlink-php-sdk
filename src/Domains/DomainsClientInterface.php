<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains;

use Shlinkio\Shlink\SDK\Domains\Model\Domain;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;

interface DomainsClientInterface
{
    /**
     * @return iterable<Domain>
     */
    public function listDomains(): iterable;

    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects;
}
