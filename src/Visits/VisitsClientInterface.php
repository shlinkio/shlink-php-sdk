<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits;

use Shlinkio\Shlink\SDK\Domains\Exception\DomainNotFoundException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisit;
use Shlinkio\Shlink\SDK\Visits\Model\OrphanVisitType;
use Shlinkio\Shlink\SDK\Visits\Model\Visit;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsDeletion;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsOverview;

interface VisitsClientInterface
{
    public function getVisitsOverview(): VisitsOverview;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function listShortUrlVisitsWithFilter(
        ShortUrlIdentifier $shortUrlIdentifier,
        VisitsFilter $filter,
    ): VisitsList;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws TagNotFoundException
     */
    public function listTagVisits(string $tag): VisitsList;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws TagNotFoundException
     */
    public function listTagVisitsWithFilter(string $tag, VisitsFilter $filter): VisitsList;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDefaultDomainVisits(): VisitsList;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDefaultDomainVisitsWithFilter(VisitsFilter $filter): VisitsList;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDomainVisits(string $domain): VisitsList;

    /**
     * @return VisitsList|Visit[]
     * @throws HttpException
     * @throws DomainNotFoundException
     */
    public function listDomainVisitsWithFilter(string $domain, VisitsFilter $filter): VisitsList;

    /**
     * @return VisitsList|OrphanVisit[]
     */
    public function listOrphanVisits(): VisitsList;

    /**
     * @return VisitsList|OrphanVisit[]
     */
    public function listOrphanVisitsWithFilter(VisitsFilter $filter, ?OrphanVisitType $type = null): VisitsList;

    /**
     * @return VisitsList|Visit[]
     */
    public function listNonOrphanVisits(): VisitsList;

    /**
     * @return VisitsList|Visit[]
     */
    public function listNonOrphanVisitsWithFilter(VisitsFilter $filter): VisitsList;

    public function deleteOrphanVisits(): VisitsDeletion;

    public function deleteShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsDeletion;
}
