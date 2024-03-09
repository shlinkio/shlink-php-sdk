<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\RedirectRules;

use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRulesList;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

interface RedirectRulesClientInterface
{
    public function getShortUrlRedirectRules(ShortUrlIdentifier $identifier): RedirectRulesList;

//    public function setShortUrlRedirectRules(ShortUrlIdentifier $identifier): RedirectRulesList;
}
