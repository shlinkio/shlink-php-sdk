<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules;

use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRulesList;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

interface RedirectRulesClientInterface
{
    /**
     * @throws ShortUrlNotFoundException
     */
    public function getShortUrlRedirectRules(ShortUrlIdentifier $identifier): RedirectRulesList;

    /**
     * @throws ShortUrlNotFoundException
     * @throws InvalidDataException
     */
    public function setShortUrlRedirectRules(
        ShortUrlIdentifier $identifier,
        SetRedirectRules $rules,
    ): RedirectRulesList;
}
