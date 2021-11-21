<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits;

use Shlinkio\Shlink\SDK\Visits\Model\VisitsList;

interface VisitsClientInterface
{
    public function listShortUrlVisits(string $shortCode, ?string $domain = null): VisitsList;

    public function listTagVisits(string $tag): VisitsList;

    public function listOrphanVisits(): VisitsList;
}
