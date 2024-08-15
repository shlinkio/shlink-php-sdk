<?php

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

enum RedirectConditionType: string
{
    case DEVICE = 'device';
    case LANGUAGE = 'language';
    case QUERY_PARAM = 'query-param';
    case IP_ADDRESS = 'ip-address';
    case UNKNOWN = 'unknown';
}
