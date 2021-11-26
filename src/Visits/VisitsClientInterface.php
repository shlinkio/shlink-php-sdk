<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits;

use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsSummary;

interface VisitsClientInterface
{
    public function getVisitsSummary(): VisitsSummary;

    public function listShortUrlVisits(ShortUrlIdentifier $shortUrlIdentifier): VisitsList;

    public function listShortUrlVisitsWithFilter(
        ShortUrlIdentifier $shortUrlIdentifier,
        VisitsFilter $filter,
    ): VisitsList;

    public function listTagVisits(string $tag): VisitsList;

    public function listTagVisitsWithFilter(string $tag, VisitsFilter $filter): VisitsList;

    public function listOrphanVisits(): VisitsList;

    public function listOrphanVisitsWithFilter(VisitsFilter $filter): VisitsList;
}
