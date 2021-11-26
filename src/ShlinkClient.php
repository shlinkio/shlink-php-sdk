<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK;

use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
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

    public function listShortUrlVisits(string $shortCode, ?string $domain = null): VisitsList
    {
        return $this->visitsClient->listShortUrlVisits($shortCode, $domain);
    }

    public function listTagVisits(string $tag): VisitsList
    {
        return $this->visitsClient->listTagVisits($tag);
    }

    public function listOrphanVisits(): VisitsList
    {
        return $this->visitsClient->listOrphanVisits();
    }
}
