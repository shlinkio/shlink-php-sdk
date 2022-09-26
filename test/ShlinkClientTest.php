<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirects;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
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
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

class ShlinkClientTest extends TestCase
{
    use ProphecyTrait;

    private ShlinkClient $shlinkClient;
    private ObjectProphecy $shortUrlsClient;
    private ObjectProphecy $visitsClient;
    private ObjectProphecy $tagsClient;
    private ObjectProphecy $domainsClient;

    public function setUp(): void
    {
        $this->shortUrlsClient = $this->prophesize(ShortUrlsClientInterface::class);
        $this->visitsClient = $this->prophesize(VisitsClientInterface::class);
        $this->tagsClient = $this->prophesize(TagsClientInterface::class);
        $this->domainsClient = $this->prophesize(DomainsClientInterface::class);

        $this->shlinkClient = new ShlinkClient(
            $this->shortUrlsClient->reveal(),
            $this->visitsClient->reveal(),
            $this->tagsClient->reveal(),
            $this->domainsClient->reveal(),
        );
    }

    /** @test */
    public function listDomainsDelegatesCallToProperClient(): void
    {
        $listDomains = $this->domainsClient->listDomains()->willReturn([]);

        $this->shlinkClient->listDomains();

        $listDomains->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function configureDomainRedirectsDelegatesCallToProperClient(): void
    {
        $configureDomains = $this->domainsClient->configureDomainRedirects(Argument::cetera())->willReturn(
            DomainRedirects::fromArray([]),
        );

        $this->shlinkClient->configureDomainRedirects(DomainRedirectsConfig::forDomain('foo.com'));

        $configureDomains->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listShortUrlsDelegatesCallToProperClient(): void
    {
        $listUrls = $this->shortUrlsClient->listShortUrls()->willReturn(
            ShortUrlsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listShortUrls();

        $listUrls->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listShortUrlsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = ShortUrlsFilter::create();
        $listUrls = $this->shortUrlsClient->listShortUrlsWithFilter($filter)->willReturn(
            ShortUrlsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listShortUrlsWithFilter($filter);

        $listUrls->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function getShortUrlDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $getShortUrl = $this->shortUrlsClient->getShortUrl($identifier)->willReturn(
            ShortUrl::fromArray(['dateCreated' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM)]),
        );

        $this->shlinkClient->getShortUrl($identifier);

        $getShortUrl->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function deleteShortUrlDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $deleteShortUrl = $this->shortUrlsClient->deleteShortUrl($identifier);

        $this->shlinkClient->deleteShortUrl($identifier);

        $deleteShortUrl->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function createShortUrlDelegatesCallToProperClient(): void
    {
        $data = ShortUrlCreation::forLongUrl('https://foo.com');
        $createShortUrl = $this->shortUrlsClient->createShortUrl($data)->willReturn(
            ShortUrl::fromArray(['dateCreated' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM)]),
        );

        $this->shlinkClient->createShortUrl($data);

        $createShortUrl->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function editShortUrlDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $data = ShortUrlEdition::create();
        $editShortUrl = $this->shortUrlsClient->editShortUrl($identifier, $data)->willReturn(
            ShortUrl::fromArray(['dateCreated' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM)]),
        );

        $this->shlinkClient->editShortUrl($identifier, $data);

        $editShortUrl->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listTagsDelegatesCallToProperClient(): void
    {
        $listTags = $this->tagsClient->listTags()->willReturn([]);

        $this->shlinkClient->listTags();

        $listTags->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listTagsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = TagsFilter::create();
        $listTags = $this->tagsClient->listTagsWithFilter($filter)->willReturn([]);

        $this->shlinkClient->listTagsWithFilter($filter);

        $listTags->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listTagsWithStatsDelegatesCallToProperClient(): void
    {
        $listTags = $this->tagsClient->listTagsWithStats()->willReturn(
            TagsWithStatsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listTagsWithStats();

        $listTags->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listTagsWithStatsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = TagsFilter::create();
        $listTags = $this->tagsClient->listTagsWithStatsWithFilter($filter)->willReturn(
            TagsWithStatsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listTagsWithStatsWithFilter($filter);

        $listTags->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function renameTagDelegatesCallToProperClient(): void
    {
        $tagRenaming = TagRenaming::fromOldNameAndNewName('foo', 'bar');
        $rename = $this->tagsClient->renameTag($tagRenaming);

        $this->shlinkClient->renameTag($tagRenaming);

        $rename->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function deleteTagsDelegatesCallToProperClient(): void
    {
        $delete = $this->tagsClient->deleteTags('foo', 'bar');

        $this->shlinkClient->deleteTags('foo', 'bar');

        $delete->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function getVisitsSummaryDelegatesCallToProperClient(): void
    {
        $getSummary = $this->visitsClient->getVisitsSummary()->willReturn(VisitsSummary::fromArray([]));

        $this->shlinkClient->getVisitsSummary();

        $getSummary->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listShortUrlVisitsDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $listVisits = $this->visitsClient->listShortUrlVisits($identifier)->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listShortUrlVisits($identifier);

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listShortUrlVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $identifier = ShortUrlIdentifier::fromShortCode('foo');
        $filter = VisitsFilter::create();
        $listVisits = $this->visitsClient->listShortUrlVisitsWithFilter($identifier, $filter)->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listShortUrlVisitsWithFilter($identifier, $filter);

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listTagVisitsDelegatesCallToProperClient(): void
    {
        $listVisits = $this->visitsClient->listTagVisits('foo')->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listTagVisits('foo');

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listTagVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $listVisits = $this->visitsClient->listTagVisitsWithFilter('foo', $filter)->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listTagVisitsWithFilter('foo', $filter);

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listDefaultDomainVisitsDelegatesCallToProperClient(): void
    {
        $listVisits = $this->visitsClient->listDefaultDOmainVisits()->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listDefaultDOmainVisits();

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listDefaultDomainVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $listVisits = $this->visitsClient->listDefaultDOmainVisitsWithFilter($filter)->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listDefaultDOmainVisitsWithFilter($filter);

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listDomainVisitsDelegatesCallToProperClient(): void
    {
        $listVisits = $this->visitsClient->listDomainVisits('foo.com')->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listDomainVisits('foo.com');

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listDomainVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $listVisits = $this->visitsClient->listDomainVisitsWithFilter('foo.com', $filter)->willReturn(
            VisitsList::forTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listDomainVisitsWithFilter('foo.com', $filter);

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listOrphanVisitsDelegatesCallToProperClient(): void
    {
        $listVisits = $this->visitsClient->listOrphanVisits()->willReturn(
            VisitsList::forOrphanVisitsTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listOrphanVisits();

        $listVisits->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function listOrphanVisitsWithFilterDelegatesCallToProperClient(): void
    {
        $filter = VisitsFilter::create();
        $listVisits = $this->visitsClient->listOrphanVisitsWithFilter($filter)->willReturn(
            VisitsList::forOrphanVisitsTupleLoader(static fn () => [[], []]),
        );

        $this->shlinkClient->listOrphanVisitsWithFilter($filter);

        $listVisits->shouldHaveBeenCalledOnce();
    }
}
