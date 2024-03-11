<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRulesList;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
use Shlinkio\Shlink\SDK\RedirectRules\RedirectRulesClientInterface;
use Shlinkio\Shlink\SDK\ShlinkClient;
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

class ShlinkClientTest extends TestCase
{
    private ShlinkClient $shlinkClient;
    private MockObject & ShortUrlsClientInterface $shortUrlsClient;
    private MockObject & VisitsClientInterface $visitsClient;
    private MockObject & TagsClientInterface $tagsClient;
    private MockObject & DomainsClientInterface $domainsClient;
    private MockObject & RedirectRulesClientInterface $redirectRulesClient;

    public function setUp(): void
    {
        $this->shortUrlsClient = $this->createMock(ShortUrlsClientInterface::class);
        $this->visitsClient = $this->createMock(VisitsClientInterface::class);
        $this->tagsClient = $this->createMock(TagsClientInterface::class);
        $this->domainsClient = $this->createMock(DomainsClientInterface::class);
        $this->redirectRulesClient = $this->createMock(RedirectRulesClientInterface::class);

        $this->shlinkClient = new ShlinkClient(
            $this->shortUrlsClient,
            $this->visitsClient,
            $this->tagsClient,
            $this->domainsClient,
            $this->redirectRulesClient,
        );
    }

    #[Test]
    public function listDomainsDelegatesCallToProperClient(): void
    {
        $this->domainsClient->expects($this->once())->method('listDomains')->willReturn([]);
        $this->shlinkClient->listDomains();
    }

    #[Test]
    public function configureDomainRedirectsDelegatesCallToProperClient(): void
    {
        $this->domainsClient->expects($this->once())->method('configureDomainRedirects')->willReturn(
            DomainRedirects::fromArray([]),
        );
        $this->shlinkClient->configureDomainRedirects(DomainRedirectsConfig::forDomain('foo.com'));
    }

    #[Test]
    public function listShortUrlsDelegatesCallToProperClient(): void
    {
        $this->shortUrlsClient->expects($this->once())->method('listShortUrls')->willReturn(
            ShortUrlsList::forTupleLoader(static fn () => [[], []]),
        );
        $this->shlinkClient->listShortUrls();
    }

    #[Test]
    public function listShortUrlsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = ShortUrlsFilter::create();
        $this->shortUrlsClient->expects($this->once())->method('listShortUrlsWithFilter')->with($filter)->willReturn(
            ShortUrlsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listShortUrlsWithFilter($filter);
    }

    #[Test]
    public function getShortUrlDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $this->shortUrlsClient->expects($this->once())->method('getShortUrl')->with($identifier)->willReturn(
            ShortUrl::fromArray(['dateCreated' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM)]),
        );

        $this->shlinkClient->getShortUrl($identifier);
    }

    #[Test]
    public function deleteShortUrlDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $this->shortUrlsClient->expects($this->once())->method('deleteShortUrl')->with($identifier);

        $this->shlinkClient->deleteShortUrl($identifier);
    }

    #[Test]
    public function createShortUrlDelegatesCallToProperClient(): void
    {
        $data = ShortUrlCreation::forLongUrl('https://foo.com');
        $this->shortUrlsClient->expects($this->once())->method('createShortUrl')->with($data)->willReturn(
            ShortUrl::fromArray(['dateCreated' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM)]),
        );

        $this->shlinkClient->createShortUrl($data);
    }

    #[Test]
    public function editShortUrlDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $data = ShortUrlEdition::create();
        $this->shortUrlsClient->expects($this->once())->method('editShortUrl')->with($identifier, $data)->willReturn(
            ShortUrl::fromArray(['dateCreated' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM)]),
        );

        $this->shlinkClient->editShortUrl($identifier, $data);
    }

    #[Test]
    public function listTagsDelegatesCallToProperClient(): void
    {
        $this->tagsClient->expects($this->once())->method('listTags')->willReturn([]);
        $this->shlinkClient->listTags();
    }

    #[Test]
    public function listTagsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = TagsFilter::create();
        $this->tagsClient->expects($this->once())->method('listTagsWithFilter')->with($filter)->willReturn([]);

        $this->shlinkClient->listTagsWithFilter($filter);
    }

    #[Test]
    public function listTagsWithStatsDelegatesCallToProperClient(): void
    {
        $this->tagsClient->expects($this->once())->method('listTagsWithStats')->willReturn(
            TagsWithStatsList::forTupleLoader(static fn () => [[], []]),
        );
        $this->shlinkClient->listTagsWithStats();
    }

    #[Test]
    public function listTagsWithStatsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = TagsFilter::create();
        $this->tagsClient->expects($this->once())->method('listTagsWithStatsWithFilter')->with($filter)->willReturn(
            TagsWithStatsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listTagsWithStatsWithFilter($filter);
    }

    #[Test]
    public function renameTagDelegatesCallToProperClient(): void
    {
        $tagRenaming = TagRenaming::fromOldNameAndNewName('foo', 'bar');
        $this->tagsClient->expects($this->once())->method('renameTag')->with($tagRenaming);

        $this->shlinkClient->renameTag($tagRenaming);
    }

    #[Test]
    public function deleteTagsDelegatesCallToProperClient(): void
    {
        $this->tagsClient->expects($this->once())->method('deleteTags')->with('foo', 'bar');
        $this->shlinkClient->deleteTags('foo', 'bar');
    }

    #[Test]
    public function getVisitsOverviewDelegatesCallToProperClient(): void
    {
        $this->visitsClient->expects($this->once())->method('getVisitsOverview')->willReturn(
            VisitsOverview::fromArray([]),
        );
        $this->shlinkClient->getVisitsOverview();
    }

    #[Test]
    public function listShortUrlVisitsDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $this->visitsClient->expects($this->once())->method('listShortUrlVisits')->with($identifier)->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listShortUrlVisits($identifier);
    }

    #[Test]
    public function listShortUrlVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $filter = VisitsFilter::create();
        $this->visitsClient->expects($this->once())->method('listShortUrlVisitsWithFilter')->with(
            $identifier,
            $filter,
        )->willReturn(VisitsList::forTupleLoader(static fn () => [[], []]));

        $this->shlinkClient->listShortUrlVisitsWithFilter($identifier, $filter);
    }

    #[Test]
    public function listTagVisitsDelegatesCallToProperClient(): void
    {
        $this->visitsClient->expects($this->once())->method('listTagVisits')->with('foo')->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );
        $this->shlinkClient->listTagVisits('foo');
    }

    #[Test]
    public function listTagVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $this->visitsClient->expects($this->once())->method('listTagVisitsWithFilter')->with(
            'foo',
            $filter,
        )->willReturn(VisitsList::forTupleLoader(static fn () => [[], []]));

        $this->shlinkClient->listTagVisitsWithFilter('foo', $filter);
    }

    #[Test]
    public function listDefaultDomainVisitsDelegatesCallToProperClient(): void
    {
        $this->visitsClient->expects($this->once())->method('listDefaultDOmainVisits')->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );
        $this->shlinkClient->listDefaultDOmainVisits();
    }

    #[Test]
    public function listDefaultDomainVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $this->visitsClient->expects($this->once())->method('listDefaultDOmainVisitsWithFilter')->with(
            $filter,
        )->willReturn(VisitsList::forTupleLoader(static fn () => [[], []]));

        $this->shlinkClient->listDefaultDOmainVisitsWithFilter($filter);
    }

    #[Test]
    public function listDomainVisitsDelegatesCallToProperClient(): void
    {
        $this->visitsClient->expects($this->once())->method('listDomainVisits')->with('foo.com')->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );
        $this->shlinkClient->listDomainVisits('foo.com');
    }

    #[Test]
    public function listDomainVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $this->visitsClient->expects($this->once())->method('listDomainVisitsWithFilter')->with(
            'foo.com',
            $filter,
        )->willReturn(VisitsList::forTupleLoader(static fn () => [[], []]));

        $this->shlinkClient->listDomainVisitsWithFilter('foo.com', $filter);
    }

    #[Test]
    public function listOrphanVisitsDelegatesCallToProperClient(): void
    {
        $this->visitsClient->expects($this->once())->method('listOrphanVisits')->willReturn(
            VisitsList::forOrphanVisitsTupleLoader(static fn () => [[], []]),
        );
        $this->shlinkClient->listOrphanVisits();
    }

    #[Test]
    #[TestWith([null])]
    #[TestWith([OrphanVisitType::BASE_URL])]
    #[TestWith([OrphanVisitType::REGULAR_NOT_FOUND])]
    #[TestWith([OrphanVisitType::INVALID_SHORT_URL])]
    public function listOrphanVisitsWithFilterDelegatesCallToProperClient(?OrphanVisitType $type): void
    {
        $filter = VisitsFilter::create();
        $this->visitsClient->expects($this->once())->method('listOrphanVisitsWithFilter')->with(
            $filter,
            $type,
        )->willReturn(VisitsList::forOrphanVisitsTupleLoader(static fn () => [[], []]));

        $this->shlinkClient->listOrphanVisitsWithFilter($filter, $type);
    }

    #[Test]
    public function listNonOrphanVisitsDelegatesCallToProperClient(): void
    {
        $this->visitsClient->expects($this->once())->method('listNonOrphanVisits')->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );
        $this->shlinkClient->listNonOrphanVisits();
    }

    #[Test]
    public function listNonOrphanVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $this->visitsClient->expects($this->once())->method('listNonOrphanVisitsWithFilter')->with($filter)->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listNonOrphanVisitsWithFilter($filter);
    }

    #[Test]
    public function deleteOrphanVisitsDelegatesCallToProperClient(): void
    {
        $this->visitsClient->expects($this->once())->method('deleteOrphanVisits')->willReturn(
            VisitsDeletion::fromArray([]),
        );
        $this->shlinkClient->deleteOrphanVisits();
    }

    #[Test]
    public function deleteShortUrlVisitsDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $this->visitsClient->expects($this->once())->method('deleteShortUrlVisits')->with($identifier)->willReturn(
            VisitsDeletion::fromArray([]),
        );
        $this->shlinkClient->deleteShortUrlVisits($identifier);
    }

    #[Test]
    public function getShortUrlRedirectRulesDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $this->redirectRulesClient->expects($this->once())->method('getShortUrlRedirectRules')->with(
            $identifier,
        )->willReturn(RedirectRulesList::fromArray([]));
        $this->shlinkClient->getShortUrlRedirectRules($identifier);
    }

    #[Test]
    public function setShortUrlRedirectRulesDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $rules = SetRedirectRules::fromScratch();
        $this->redirectRulesClient->expects($this->once())->method('setShortUrlRedirectRules')->with(
            $identifier,
            $rules,
        )->willReturn(RedirectRulesList::fromArray([]));
        $this->shlinkClient->setShortUrlRedirectRules($identifier, $rules);
    }
}
