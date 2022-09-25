<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains;

use Shlinkio\Shlink\SDK\Domains\Model\Domain;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;

class DomainsClient implements DomainsClientInterface
{
    public function __construct(private readonly HttpClientInterface $httpClient)
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

    /**
     * @throws HttpException
     * @throws InvalidDataException
     */
    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects
    {
        try {
            return DomainRedirects::fromArray(
                $this->httpClient->callShlinkWithBody('/domains/redirects', 'PATCH', $redirects),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                'INVALID_ARGUMENT' => InvalidDataException::fromHttpException($e),
                default => $e,
            };
        }
    }
}
