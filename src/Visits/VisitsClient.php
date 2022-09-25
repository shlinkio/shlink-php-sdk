<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits;

use Closure;
use Shlinkio\Shlink\SDK\Domains\Exception\DomainNonFoundException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisit;
use Shlinkio\Shlink\SDK\Visits\Model\Visit;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

use function sprintf;

class VisitsClient implements VisitsClientInterface
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function getVisitsSummary(): VisitsSummary
    {
        return VisitsSummary::fromArray($this->httpClient->getFromShlink('/visits')['visits'] ?? []);
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList
    {
        return $this->listShortUrlVisitsWithFilter($shortUrlIdentifier, VisitsFilter::create());
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function listShortUrlVisitsWithFilter(
        ShortUrlIdentifier $shortUrlIdentifier,
        VisitsFilter $filter,
    ): VisitsList {
        $shortCode = $shortUrlIdentifier->shortCode;
        $domain = $shortUrlIdentifier->domain;
        $query = $filter->toArray();

        if ($domain !== null) {
            $query['domain'] = $domain;
        }

        try {
            return VisitsList::forTupleLoader(
                $this->createVisitsLoaderForUrl(sprintf('/short-urls/%s/visits', $shortCode), $query),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                'INVALID_SHORTCODE' => ShortUrlNotFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws TagNotFoundException
     */
    public function listTagVisits(string $tag): VisitsList
    {
        return $this->listTagVisitsWithFilter($tag, VisitsFilter::create());
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws TagNotFoundException
     */
    public function listTagVisitsWithFilter(string $tag, VisitsFilter $filter): VisitsList
    {
        try {
            return VisitsList::forTupleLoader(
                $this->createVisitsLoaderForUrl(sprintf('/tags/%s/visits', $tag), $filter->toArray()),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                'TAG_NOT_FOUND' => TagNotFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNonFoundException
     */
    public function listDefaultDomainVisits(): VisitsList
    {
        return $this->listDomainVisits('DEFAULT');
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNonFoundException
     */
    public function listDefaultDomainVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return $this->listDomainVisitsWithFilter('DEFAULT', $filter);
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNonFoundException
     */
    public function listDomainVisits(string $domain): VisitsList
    {
        return $this->listDomainVisitsWithFilter($domain, VisitsFilter::create());
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNonFoundException
     */
    public function listDomainVisitsWithFilter(string $domain, VisitsFilter $filter): VisitsList
    {
        try {
            return VisitsList::forTupleLoader(
                $this->createVisitsLoaderForUrl(sprintf('/domains/%s/visits', $domain), $filter->toArray()),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                'DOMAIN_NOT_FOUND' => DomainNonFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @return VisitsList|OrphanVisit[]
     */
    public function listOrphanVisits(): VisitsList
    {
        return $this->listOrphanVisitsWithFilter(VisitsFilter::create());
    }

    /**
     * @return VisitsList|OrphanVisit[]
     */
    public function listOrphanVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return VisitsList::forOrphanVisitsTupleLoader(
            $this->createVisitsLoaderForUrl('/visits/orphan', $filter->toArray()),
        );
    }

    private function createVisitsLoaderForUrl(string $url, array $query): Closure
    {
        return function (int $page, int $itemsPerPage) use ($url, $query): array {
            $query['page'] = $page;
            $query['itemsPerPage'] = $itemsPerPage;
            $body = $this->httpClient->getFromShlink($url, $query);

            return [$body['visits']['data'] ?? [], $body['visits']['pagination'] ?? []];
        };
    }
}
