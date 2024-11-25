<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeInterface;

interface VisitInterface
{
    public function referer(): string;
    public function date(): DateTimeInterface;
    public function userAgent(): string;
    public function potentialBot(): bool;
    public function location(): VisitLocation|null;
    public function visitedUrl(): string;
    public function redirectUrl(): string|null;
}
