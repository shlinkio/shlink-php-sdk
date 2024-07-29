<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK;

use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRulesList;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
use Shlinkio\Shlink\SDK\RedirectRules\RedirectRulesClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagsWithStatsList;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisitType;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsDeletion;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsOverview;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

readonly class ShlinkClient implements
    ShortUrlsClientInterface,
    VisitsClientInterface,
    TagsClientInterface,
    DomainsClientInterface,
    RedirectRulesClientInterface
{
    public function __construct(
        private ShortUrlsClientInterface $shortUrlsClient,
        private VisitsClientInterface $visitsClient,
        private TagsClientInterface $tagsClient,
        private DomainsClientInterface $domainsClient,
        private RedirectRulesClientInterface $redirectRulesClient,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function listDomains(): iterable
    {
        return $this->domainsClient->listDomains();
    }

    /**
     * @inheritDoc
     */
    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects
    {
        return $this->domainsClient->configureDomainRedirects($redirects);
    }

    /**
     * @inheritDoc
     */
    public function listShortUrls(): ShortUrlsList
    {
        return $this->shortUrlsClient->listShortUrls();
    }

    /**
     * @inheritDoc
     */
    public function listShortUrlsWithFilter(ShortUrlsFilter $filter): ShortUrlsList
    {
        return $this->shortUrlsClient->listShortUrlsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl
    {
        return $this->shortUrlsClient->getShortUrl($identifier);
    }

    /**
     * @inheritDoc
     */
    public function deleteShortUrl(ShortUrlIdentifier $identifier): void
    {
        $this->shortUrlsClient->deleteShortUrl($identifier);
    }

    /**
     * @inheritDoc
     */
    public function createShortUrl(ShortUrlCreation $creation): ShortUrl
    {
        return $this->shortUrlsClient->createShortUrl($creation);
    }

    /**
     * @inheritDoc
     */
    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl
    {
        return $this->shortUrlsClient->editShortUrl($identifier, $edition);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function listTagsWithStats(): TagsWithStatsList
    {
        return $this->tagsClient->listTagsWithStats();
    }

    /**
     * @inheritDoc
     */
    public function listTagsWithStatsWithFilter(TagsFilter $filter): TagsWithStatsList
    {
        return $this->tagsClient->listTagsWithStatsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    public function renameTag(TagRenaming $tagRenaming): void
    {
        $this->tagsClient->renameTag($tagRenaming);
    }

    /**
     * @inheritDoc
     */
    public function deleteTags(string ...$tags): void
    {
        $this->tagsClient->deleteTags(...$tags);
    }

    public function getVisitsOverview(): VisitsOverview
    {
        return $this->visitsClient->getVisitsOverview();
    }

    /**
     * @inheritDoc
     */
    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList
    {
        return $this->visitsClient->listShortUrlVisits($shortUrlIdentifier);
    }

    /**
     * @inheritDoc
     */
    public function listShortUrlVisitsWithFilter(
        ShortUrlIdentifier $shortUrlIdentifier,
        VisitsFilter $filter,
    ): VisitsList {
        return $this->visitsClient->listShortUrlVisitsWithFilter($shortUrlIdentifier, $filter);
    }

    /**
     * @inheritDoc
     */
    public function listTagVisits(string $tag): VisitsList
    {
        return $this->visitsClient->listTagVisits($tag);
    }

    /**
     * @inheritDoc
     */
    public function listTagVisitsWithFilter(string $tag, VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listTagVisitsWithFilter($tag, $filter);
    }

    /**
     * @inheritDoc
     */
    public function listDefaultDomainVisits(): VisitsList
    {
        return $this->visitsClient->listDefaultDomainVisits();
    }

    /**
     * @inheritDoc
     */
    public function listDefaultDomainVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listDefaultDomainVisitsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    public function listDomainVisits(string $domain): VisitsList
    {
        return $this->visitsClient->listDomainVisits($domain);
    }

    /**
     * @inheritDoc
     */
    public function listDomainVisitsWithFilter(string $domain, VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listDomainVisitsWithFilter($domain, $filter);
    }

    /**
     * @inheritDoc
     */
    public function listOrphanVisits(): VisitsList
    {
        return $this->visitsClient->listOrphanVisits();
    }

    /**
     * @inheritDoc
     */
    public function listOrphanVisitsWithFilter(VisitsFilter $filter, ?OrphanVisitType $type = null): VisitsList
    {
        return $this->visitsClient->listOrphanVisitsWithFilter($filter, $type);
    }

    /**
     * @inheritDoc
     */
    public function listNonOrphanVisits(): VisitsList
    {
        return $this->visitsClient->listNonOrphanVisits();
    }

    /**
     * @inheritDoc
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

    /**
     * @inheritDoc
     */
    public function getShortUrlRedirectRules(ShortUrlIdentifier $identifier): RedirectRulesList
    {
        return $this->redirectRulesClient->getShortUrlRedirectRules($identifier);
    }

    /**
     * @inheritDoc
     */
    public function setShortUrlRedirectRules(
        ShortUrlIdentifier $identifier,
        SetRedirectRules $rules,
    ): RedirectRulesList {
        return $this->redirectRulesClient->setShortUrlRedirectRules($identifier, $rules);
    }
}
