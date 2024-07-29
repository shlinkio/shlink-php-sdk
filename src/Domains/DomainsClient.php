<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Domains;

use Shlinkio\Shlink\SDK\Domains\Model\Domain;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;

readonly class DomainsClient implements DomainsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @inheritDoc
     */
    public function listDomains(): iterable
    {
        $domains = $this->httpClient->getFromShlink('/domains')['domains']['data'] ?? [];
        foreach ($domains as $index => $domain) {
            yield $index => Domain::fromArray($domain);
        }
    }

    /**
     * @inheritDoc
     */
    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects
    {
        try {
            return DomainRedirects::fromArray(
                $this->httpClient->callShlinkWithBody('/domains/redirects', 'PATCH', $redirects),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::INVALID_DATA => InvalidDataException::fromHttpException($e),
                default => $e,
            };
        }
    }
}
