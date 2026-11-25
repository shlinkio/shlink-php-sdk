<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK;

use Override;
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
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsDeletion;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsOverview;
use Shlinkio\Shlink\SDK\Visits\Model\WithDomainVisitsFilter;
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
    ) {}

    /**
     * @inheritDoc
     */
    #[Override]
    public function listDomains(): iterable
    {
        return $this->domainsClient->listDomains();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function configureDomainRedirects(DomainRedirectsConfig $redirects): DomainRedirects
    {
        return $this->domainsClient->configureDomainRedirects($redirects);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listShortUrls(): ShortUrlsList
    {
        return $this->shortUrlsClient->listShortUrls();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listShortUrlsWithFilter(ShortUrlsFilter $filter): ShortUrlsList
    {
        return $this->shortUrlsClient->listShortUrlsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl
    {
        return $this->shortUrlsClient->getShortUrl($identifier);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function deleteShortUrl(ShortUrlIdentifier $identifier): void
    {
        $this->shortUrlsClient->deleteShortUrl($identifier);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function createShortUrl(ShortUrlCreation $creation): ShortUrl
    {
        return $this->shortUrlsClient->createShortUrl($creation);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl
    {
        return $this->shortUrlsClient->editShortUrl($identifier, $edition);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listTags(): array
    {
        return $this->tagsClient->listTags();
    }

    #[Override]
    public function listTagsWithFilter(TagsFilter $filter): array
    {
        return $this->tagsClient->listTagsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listTagsWithStats(): TagsWithStatsList
    {
        return $this->tagsClient->listTagsWithStats();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listTagsWithStatsWithFilter(TagsFilter $filter): TagsWithStatsList
    {
        return $this->tagsClient->listTagsWithStatsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function renameTag(TagRenaming $tagRenaming): void
    {
        $this->tagsClient->renameTag($tagRenaming);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function deleteTags(string ...$tags): void
    {
        $this->tagsClient->deleteTags(...$tags);
    }

    #[Override]
    public function getVisitsOverview(): VisitsOverview
    {
        return $this->visitsClient->getVisitsOverview();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList
    {
        return $this->visitsClient->listShortUrlVisits($shortUrlIdentifier);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listShortUrlVisitsWithFilter(
        ShortUrlIdentifier $shortUrlIdentifier,
        VisitsFilter $filter,
    ): VisitsList {
        return $this->visitsClient->listShortUrlVisitsWithFilter($shortUrlIdentifier, $filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listTagVisits(string $tag): VisitsList
    {
        return $this->visitsClient->listTagVisits($tag);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listTagVisitsWithFilter(string $tag, WithDomainVisitsFilter|VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listTagVisitsWithFilter($tag, $filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listDefaultDomainVisits(): VisitsList
    {
        return $this->visitsClient->listDefaultDomainVisits();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listDefaultDomainVisitsWithFilter(VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listDefaultDomainVisitsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listDomainVisits(string $domain): VisitsList
    {
        return $this->visitsClient->listDomainVisits($domain);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listDomainVisitsWithFilter(string $domain, VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listDomainVisitsWithFilter($domain, $filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listOrphanVisits(): VisitsList
    {
        return $this->visitsClient->listOrphanVisits();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listOrphanVisitsWithFilter(
        OrphanVisitsFilter|WithDomainVisitsFilter|VisitsFilter $filter,
    ): VisitsList {
        return $this->visitsClient->listOrphanVisitsWithFilter($filter);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listNonOrphanVisits(): VisitsList
    {
        return $this->visitsClient->listNonOrphanVisits();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listNonOrphanVisitsWithFilter(WithDomainVisitsFilter|VisitsFilter $filter): VisitsList
    {
        return $this->visitsClient->listNonOrphanVisitsWithFilter($filter);
    }

    #[Override]
    public function deleteOrphanVisits(): VisitsDeletion
    {
        return $this->visitsClient->deleteOrphanVisits();
    }

    #[Override]
    public function deleteShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsDeletion
    {
        return $this->visitsClient->deleteShortUrlVisits($shortUrlIdentifier);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getShortUrlRedirectRules(ShortUrlIdentifier $identifier): RedirectRulesList
    {
        return $this->redirectRulesClient->getShortUrlRedirectRules($identifier);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function setShortUrlRedirectRules(
        ShortUrlIdentifier $identifier,
        SetRedirectRules $rules,
    ): RedirectRulesList {
        return $this->redirectRulesClient->setShortUrlRedirectRules($identifier, $rules);
    }
}
