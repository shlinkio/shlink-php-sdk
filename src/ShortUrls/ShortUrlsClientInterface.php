<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;

interface ShortUrlsClientInterface
{
    public function listShortUrls(): ShortUrlsList;

    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl;

    public function deleteShortUrl(ShortUrlIdentifier $identifier): void;

    public function createShortUrl(ShortUrlCreation $creation): ShortUrl;

    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl;
}
