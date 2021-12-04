<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeInterface;

interface VisitInterface
{
    public function referer(): string;
    public function dateTime(): DateTimeInterface;
    public function userAgent(): string;
    public function potentialBot(): bool;
    public function location(): ?VisitLocation;
}
