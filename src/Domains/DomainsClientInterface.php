<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains;

use Shlinkio\Shlink\SDK\Domains\Model\Domain;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

interface DomainsClientInterface
{
    /**
     * @return iterable<Domain>
     */
    public function listDomains(): iterable;

    /**
     * @throws HttpException
     * @throws InvalidDataException
     */
    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects;
}
