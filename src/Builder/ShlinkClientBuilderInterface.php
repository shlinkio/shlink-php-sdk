<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Builder;

use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

interface ShlinkClientBuilderInterface
{
    public function buildShortUrlsClient(ShlinkConfigInterface $config): ShortUrlsClientInterface;

    public function buildVisitsClient(ShlinkConfigInterface $config): VisitsClientInterface;

    public function buildTagsClient(ShlinkConfigInterface $config): TagsClientInterface;

    public function buildDomainsClient(ShlinkConfigInterface $config): DomainsClientInterface;
}
