<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
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

use function sprintf;

class ShortUrlsClient implements ShortUrlsClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @return ShortUrlsList|ShortUrl[]
     */
    public function listShortUrls(): ShortUrlsList
    {
        return $this->listShortUrlsWithFilter(ShortUrlsFilter::create());
    }

    /**
     * @return ShortUrlsList|ShortUrl[]
     */
    public function listShortUrlsWithFilter(ShortUrlsFilter $filter): ShortUrlsList
    {
        $query = $filter->toArray();
        $buildQueryWithPage = static function (int $page, int $itemsPerPage) use ($query): array {
            $query['itemsPerPage'] = $itemsPerPage;
            $query['page'] = $page;

            return $query;
        };

        return ShortUrlsList::forTupleLoader(function (int $page, int $itemsPerPage) use ($buildQueryWithPage): array {
            $payload = $this->httpClient->getFromShlink('/short-urls', $buildQueryWithPage($page, $itemsPerPage));
            return [$payload['shortUrls']['data'] ?? [], $payload['shortUrls']['pagination'] ?? []];
        });
    }

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     */
    public function getShortUrl(ShortUrlIdentifier $identifier): ShortUrl
    {
        try {
            return ShortUrl::fromArray(
                $this->httpClient->getFromShlink(...$this->identifierToUrlAndQuery($identifier)),
            );
        } catch (HttpException $e) {
            throw match ($e->type()) {
                'INVALID_SHORTCODE' => ShortUrlNotFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     * @throws DeleteShortUrlThresholdException
     */
    public function deleteShortUrl(ShortUrlIdentifier $identifier): void
    {
        [$url, $query] = $this->identifierToUrlAndQuery($identifier);

        try {
            $this->httpClient->callShlinkWithBody($url, 'DELETE', [], $query);
        } catch (HttpException $e) {
            throw match ($e->type()) {
                'INVALID_SHORTCODE' => ShortUrlNotFoundException::fromHttpException($e),
                'INVALID_SHORTCODE_DELETION' => DeleteShortUrlThresholdException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @throws HttpException
     * @throws NonUniqueSlugException
     * @throws InvalidLongUrlException
     * @throws InvalidDataException
     */
    public function createShortUrl(ShortUrlCreation $creation): ShortUrl
    {
        try {
            return ShortUrl::fromArray($this->httpClient->callShlinkWithBody('/short-urls', 'POST', $creation));
        } catch (HttpException $e) {
            throw match ($e->type()) {
                'INVALID_ARGUMENT' => InvalidDataException::fromHttpException($e),
                'INVALID_URL' => InvalidLongUrlException::fromHttpException($e),
                'INVALID_SLUG' => NonUniqueSlugException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @throws HttpException
     * @throws ShortUrlNotFoundException
     * @throws InvalidDataException
     */
    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): ShortUrl
    {
        [$url, $query] = $this->identifierToUrlAndQuery($identifier);

        try {
            return ShortUrl::fromArray($this->httpClient->callShlinkWithBody($url, 'PATCH', $edition, $query));
        } catch (HttpException $e) {
            throw match ($e->type()) {
                'INVALID_SHORTCODE' => ShortUrlNotFoundException::fromHttpException($e),
                'INVALID_ARGUMENT' => InvalidDataException::fromHttpException($e),
                default => $e,
            };
        }
    }

    /**
     * @return array{string, array}
     */
    private function identifierToUrlAndQuery(ShortUrlIdentifier $identifier): array
    {
        return [
            sprintf('/short-urls/%s', $identifier->shortCode()),
            ['domain' => $identifier->domain()],
        ];
    }
}
