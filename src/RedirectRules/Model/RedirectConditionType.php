<?php

namespace Shlinkio\Shlink\SDK\RedirectRules\Model;

enum RedirectConditionType: string
{
    case DEVICE = 'device';
    case LANGUAGE = 'language';
    case QUERY_PARAM = 'query-param';
    case ANY_VALUE_QUERY_PARAM = 'any-value-query-param';
    case VALUELESS_QUERY_PARAM = 'valueless-query-param';
    case IP_ADDRESS = 'ip-address';
    case GEOLOCATION_COUNTRY_CODE = 'geolocation-country-code';
    case GEOLOCATION_CITY_NAME = 'geolocation-city-name';
    case UNKNOWN = 'unknown';
}
