<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules;

use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRulesList;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

use function sprintf;

readonly class RedirectRulesClient implements RedirectRulesClientInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function getShortUrlRedirectRules(ShortUrlIdentifier $identifier): RedirectRulesList
    {
        [$shortCode, $query] = $identifier->toShortCodeAndQuery();

        try {
            return RedirectRulesList::fromArray(
                $this->httpClient->getFromShlink(sprintf('/short-urls/%s/redirect-rules', $shortCode), $query),
            );
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::SHORT_URL_NOT_FOUND => ShortUrlNotFoundException::fromHttpException($e),
                default => $e,
            };
        }
    }
}
