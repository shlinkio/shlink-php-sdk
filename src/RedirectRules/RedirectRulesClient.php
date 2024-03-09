<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Http\ErrorType;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRulesList;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
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

    public function setShortUrlRedirectRules(
        ShortUrlIdentifier $identifier,
        SetRedirectRules $rules,
    ): RedirectRulesList {
        [$shortCode, $query] = $identifier->toShortCodeAndQuery();

        try {
            return RedirectRulesList::fromArray($this->httpClient->callShlinkWithBody(
                sprintf('/short-urls/%s/redirect-rules', $shortCode),
                'POST',
                $rules,
                $query,
            ));
        } catch (HttpException $e) {
            throw match ($e->type) {
                ErrorType::SHORT_URL_NOT_FOUND => ShortUrlNotFoundException::fromHttpException($e),
                ErrorType::INVALID_DATA => InvalidDataException::fromHttpException($e),
                default => $e,
            };
        }
    }
}
