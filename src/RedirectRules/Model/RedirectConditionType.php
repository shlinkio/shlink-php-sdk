<?php

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

enum RedirectConditionType: string
{
    case DEVICE = 'device';
    case LANGUAGE = 'language';
    case QUERY_PARAM = 'query-param';
    case UNKNOWN = 'unknown';
}
