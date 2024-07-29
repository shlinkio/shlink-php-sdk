<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\DeleteShortUrlThresholdException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\InvalidLongUrlException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\NonUniqueSlugException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsList;

interface ShortUrlsClientInterface
{
    public function listShortUrls(): ShortUrlsList;

    public function listShortUrlsWithFilter(ShortUrlsFilter $filter): ShortUrlsList;

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl;

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     * @throws DeleteShortUrlThresholdException
     */
    public function deleteShortUrl(ShortUrlIdentifier $identifier): void;

    /**
     * @throws HttpException
     * @throws NonUniqueSlugException
     * @throws InvalidLongUrlException
     * @throws InvalidDataException
     */
    public function createShortUrl(ShortUrlCreation $creation): ShortUrl;

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     * @throws InvalidDataException
     */
    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl;
}
