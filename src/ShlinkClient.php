<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK;

use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisit;
use Shlinkio\Shlink\SDK\Visits\Model\Visit;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

class ShlinkClient implements
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

    public function listDomains(): iterable
    {
        return $this->domainsClient->listDomains();
    }

    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects
    {
        return $this->domainsClient->configureDomainRedirects($redirects);
    }

    public function listShortUrls(): ShortUrlsList
    {
        return $this->shortUrlsClient->listShortUrls();
    }

    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl
    {
        return $this->shortUrlsClient->getShortUrl($identifier);
    }

    public function deleteShortUrl(ShortUrlIdentifier $identifier): void
    {
        $this->shortUrlsClient->deleteShortUrl($identifier);
    }

    public function createShortUrl(ShortUrlCreation $creation): ShortUrl
    {
        return $this->shortUrlsClient->createShortUrl($creation);
    }

    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl
    {
        return $this->shortUrlsClient->editShortUrl($identifier, $edition);
    }

    public function listTags(): array
    {
        return $this->tagsClient->listTags();
    }

    /**
     * @return iterable<TagWithStats>
     */
    public function listTagsWithStats(): iterable
    {
        return $this->tagsClient->listTagsWithStats();
    }

    public function renameTag(string $oldName, string $newName): void
    {
        $this->tagsClient->renameTag($oldName, $newName);
    }

    public function deleteTags(array $tags): void
    {
        $this->tagsClient->deleteTags($tags);
    }

    public function getVisitsSummary(): VisitsSummary
    {
        return $this->visitsClient->getVisitsSummary();
    }

    /**
     * @return VisitsList|Visit[]
     */
    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList
    {
        return $this->visitsClient->listShortUrlVisits($shortUrlIdentifier);
    }

    /**
     * @return VisitsList|Visit[]
     */
    public function listShortUrlVisitsWithFilter(
        ShortUrlIdentifier $shortUrlIdentifier,
        VisitsFilter $filter,
    ): VisitsList {
        return $this->visitsClient->listShortUrlVisitsWithFilter($shortUrlIdentifier, $filter);
    }

    /**
     * @return VisitsList|Visit[]
     */
    public function listTagVisits(string $tag): VisitsList
    {
        return $this->visitsClient->listTagVisits($tag);
    }

    /**
     * @return VisitsList|Visit[]
     */
    public function listTagVisitsWithFilter(string $tag, VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listTagVisitsWithFilter($tag, $filter);
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
}
