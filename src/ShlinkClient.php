<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK;

use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\Domains\Exception\DomainNotFoundException;
use Shlinkio\Shlink\SDK\Domains\Model\Domain;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\DeleteShortUrlThresholdException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\InvalidLongUrlException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\NonUniqueSlugException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsWithStatsList;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisit;
use Shlinkio\Shlink\SDK\Visits\Model\Visit;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsAmounts;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsDeletion;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

readonly class ShlinkClient implements
    ShortUrlsClientInterface,
    VisitsClientInterface,
    TagsClientInterface,
    DomainsClientInterface
{
    public function __construct(
        private ShortUrlsClientInterface $shortUrlsClient,
        private VisitsClientInterface $visitsClient,
        private TagsClientInterface $tagsClient,
        private DomainsClientInterface $domainsClient,
    ) {
    }

    /**
     * @return iterable<Domain>
     */
    public function listDomains(): iterable
    {
        return $this->domainsClient->listDomains();
    }

    /**
     * @throws HttpException
     * @throws InvalidDataException
     */
    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects
    {
        return $this->domainsClient->configureDomainRedirects($redirects);
    }

    /**
     * @return ShortUrlsList|ShortUrl[]
     */
    public function listShortUrls(): ShortUrlsList
    {
        return $this->shortUrlsClient->listShortUrls();
    }

    /**
     * @return ShortUrlsList|ShortUrl[]
     */
    public function listShortUrlsWithFilter(ShortUrlsFilter $filter): ShortUrlsList
    {
        return $this->shortUrlsClient->listShortUrlsWithFilter($filter);
    }

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl
    {
        return $this->shortUrlsClient->getShortUrl($identifier);
    }

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     * @throws DeleteShortUrlThresholdException
     */
    public function deleteShortUrl(ShortUrlIdentifier $identifier): void
    {
        $this->shortUrlsClient->deleteShortUrl($identifier);
    }

    /**
     * @throws HttpException
     * @throws NonUniqueSlugException
     * @throws InvalidLongUrlException
     * @throws InvalidDataException
     */
    public function createShortUrl(ShortUrlCreation $creation): ShortUrl
    {
        return $this->shortUrlsClient->createShortUrl($creation);
    }

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     * @throws InvalidDataException
     */
    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl
    {
        return $this->shortUrlsClient->editShortUrl($identifier, $edition);
    }

    /**
     * @return string[]
     */
    public function listTags(): array
    {
        return $this->tagsClient->listTags();
    }

    public function listTagsWithFilter(TagsFilter $filter): array
    {
        return $this->tagsClient->listTagsWithFilter($filter);
    }

    /**
     * @return TagsWithStatsList|TagWithStats[]
     */
    public function listTagsWithStats(): TagsWithStatsList
    {
        return $this->tagsClient->listTagsWithStats();
    }

    /**
     * @return TagsWithStatsList|TagWithStats[]
     */
    public function listTagsWithStatsWithFilter(TagsFilter $filter): TagsWithStatsList
    {
        return $this->tagsClient->listTagsWithStatsWithFilter($filter);
    }

    /**
     * @throws HttpException
     * @throws InvalidDataException
     * @throws ForbiddenTagOperationException
     * @throws TagNotFoundException
     * @throws TagConflictException
     */
    public function renameTag(TagRenaming $tagRenaming): void
    {
        $this->tagsClient->renameTag($tagRenaming);
    }

    /**
     * @throws HttpException
     * @throws ForbiddenTagOperationException
     */
    public function deleteTags(string ...$tags): void
    {
        $this->tagsClient->deleteTags(...$tags);
    }

    public function getVisitsSummary(): VisitsAmounts
    {
        return $this->visitsClient->getVisitsSummary();
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList
    {
        return $this->visitsClient->listShortUrlVisits($shortUrlIdentifier);
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
        return $this->visitsClient->listShortUrlVisitsWithFilter($shortUrlIdentifier, $filter);
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws TagNotFoundException
     */
    public function listTagVisits(string $tag): VisitsList
    {
        return $this->visitsClient->listTagVisits($tag);
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws TagNotFoundException
     */
    public function listTagVisitsWithFilter(string $tag, VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listTagVisitsWithFilter($tag, $filter);
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDefaultDomainVisits(): VisitsList
    {
        return $this->visitsClient->listDefaultDomainVisits();
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDefaultDomainVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listDefaultDomainVisitsWithFilter($filter);
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDomainVisits(string $domain): VisitsList
    {
        return $this->visitsClient->listDomainVisits($domain);
    }

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDomainVisitsWithFilter(string $domain, VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listDomainVisitsWithFilter($domain, $filter);
    }

    /**
     * @return VisitsList|OrphanVisit[]
     */
    public function listOrphanVisits(): VisitsList
    {
        return $this->visitsClient->listOrphanVisits();
    }

    /**
     * @return VisitsList|OrphanVisit[]
     */
    public function listOrphanVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listOrphanVisitsWithFilter($filter);
    }

    /**
     * @return VisitsList|Visit[]
     */
    public function listNonOrphanVisits(): VisitsList
    {
        return $this->visitsClient->listNonOrphanVisits();
    }

    /**
     * @return VisitsList|Visit[]
     */
    public function listNonOrphanVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listNonOrphanVisitsWithFilter($filter);
    }

    public function deleteOrphanVisits(): VisitsDeletion
    {
        return $this->visitsClient->deleteOrphanVisits();
    }

    public function deleteShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsDeletion
    {
        return $this->visitsClient->deleteShortUrlVisits($shortUrlIdentifier);
    }
}
