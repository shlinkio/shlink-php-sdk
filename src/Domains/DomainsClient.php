<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains;

use Shlinkio\Shlink\SDK\Domains\Model\Domain;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;

class DomainsClient implements DomainsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @return iterable<Domain>
     */
    public function listDomains(): iterable
    {
        $domains = $this->httpClient->getFromShlink('/domains')['domains']['data'] ?? [];
        foreach ($domains as $index => $domain) {
            yield $index => Domain::fromArray($domain);
        }
    }

    public function configureNotFoundRedirects(DomainRedirectsConfig $redirects): DomainRedirects
    {
        $payload = $this->httpClient->callShlinkWithBody('/domains/redirects', 'PATCH', $redirects);
        return DomainRedirects::fromArray($payload);
    }
}
