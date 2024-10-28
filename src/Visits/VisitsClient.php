<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits;

use Closure;
use Shlinkio\Shlink\SDK\Domains\Exception\DomainNotFoundException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisitType;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsDeletion;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsOverview;

use function sprintf;

readonly class VisitsClient implements VisitsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function getVisitsOverview(): VisitsOverview
    {
        return VisitsOverview::fromArray($this->httpClient->getFromShlink('/visits')['visits'] ?? []);
    }

    /**
     * @inheritDoc
     */
    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList
    {
        return $this->listShortUrlVisitsWithFilter($shortUrlIdentifier, VisitsFilter::create());
    }

    /**
     * @inheritDoc
     */
    public function listShortUrlVisitsWithFilter(
        ShortUrlIdentifier $shortUrlIdentifier,
        VisitsFilter $filter,
    ): VisitsList {
        [$shortCode, $query] = $shortUrlIdentifier->toShortCodeAndQuery($filter->toArray());

        try {
            return VisitsList::forTupleLoader(
                $this->createVisitsLoaderForUrl(sprintf('/short-urls/%s/visits', $shortCode), $query),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::SHORT_URL_NOT_FOUND => ShortUrlNotFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @inheritDoc
     */
    public function listTagVisits(string $tag): VisitsList
    {
        return $this->listTagVisitsWithFilter($tag, VisitsFilter::create());
    }

    /**
     * @inheritDoc
     */
    public function listTagVisitsWithFilter(string $tag, VisitsFilter $filter): VisitsList
    {
        try {
            return VisitsList::forTupleLoader(
                $this->createVisitsLoaderForUrl(sprintf('/tags/%s/visits', $tag), $filter->toArray()),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::TAG_NOT_FOUND => TagNotFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @inheritDoc
     */
    public function listDefaultDomainVisits(): VisitsList
    {
        return $this->listDomainVisits('DEFAULT');
    }

    /**
     * @inheritDoc
     */
    public function listDefaultDomainVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return $this->listDomainVisitsWithFilter('DEFAULT', $filter);
    }

    /**
     * @inheritDoc
     */
    public function listDomainVisits(string $domain): VisitsList
    {
        return $this->listDomainVisitsWithFilter($domain, VisitsFilter::create());
    }

    /**
     * @inheritDoc
     */
    public function listDomainVisitsWithFilter(string $domain, VisitsFilter $filter): VisitsList
    {
        try {
            return VisitsList::forTupleLoader(
                $this->createVisitsLoaderForUrl(sprintf('/domains/%s/visits', $domain), $filter->toArray()),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::DOMAIN_NOT_FOUND => DomainNotFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @inheritDoc
     */
    public function listOrphanVisits(): VisitsList
    {
        return $this->listOrphanVisitsWithFilter(VisitsFilter::create());
    }

    /**
     * @inheritDoc
     */
    public function listOrphanVisitsWithFilter(VisitsFilter $filter, OrphanVisitType|null $type = null): VisitsList
    {
        $query = $filter->toArray();
        if ($type !== null) {
            $query['type'] = $type->value;
        }

        return VisitsList::forOrphanVisitsTupleLoader($this->createVisitsLoaderForUrl('/visits/orphan', $query));
    }

    /**
     * @inheritDoc
     */
    public function listNonOrphanVisits(): VisitsList
    {
        return $this->listNonOrphanVisitsWithFilter(VisitsFilter::create());
    }

    /**
     * @inheritDoc
     */
    public function listNonOrphanVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return VisitsList::forTupleLoader(
            $this->createVisitsLoaderForUrl('/visits/non-orphan', $filter->toArray()),
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

    public function deleteOrphanVisits(): VisitsDeletion
    {
        return VisitsDeletion::fromArray($this->httpClient->callShlinkWithBody('/visits/orphan', 'DELETE', []));
    }

    public function deleteShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsDeletion
    {
        [$shortCode, $query] = $shortUrlIdentifier->toShortCodeAndQuery();

        return VisitsDeletion::fromArray(
            $this->httpClient->callShlinkWithBody(sprintf('/short-urls/%s/visits', $shortCode), 'DELETE', $query),
        );
    }
}
