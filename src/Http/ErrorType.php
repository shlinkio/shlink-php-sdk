<?php

namespace Shlinkio\Shlink\SDK\Http;

enum ErrorType: string
{
    case INVALID_DATA = 'https://shlink.io/api/error/invalid-data';
    case INVALID_SHORT_URL_DELETION = 'https://shlink.io/api/error/invalid-short-url-deletion';
    case DOMAIN_NOT_FOUND = 'https://shlink.io/api/error/domain-not-found';
    case FORBIDDEN_TAG_OPERATION = 'https://shlink.io/api/error/forbidden-tag-operation';
    case NON_UNIQUE_SLUG = 'https://shlink.io/api/error/non-unique-slug';
    case SHORT_URL_NOT_FOUND = 'https://shlink.io/api/error/short-url-not-found';
    case TAG_CONFLICT = 'https://shlink.io/api/error/tag-conflict';
    case TAG_NOT_FOUND = 'https://shlink.io/api/error/tag-not-found';
    case MERCURE_NOT_CONFIGURED = 'https://shlink.io/api/error/mercure-not-configured';
    case MISSING_AUTHORIZATION = 'https://shlink.io/api/error/missing-authentication';
    case INVALID_API_KEY = 'https://shlink.io/api/error/invalid-api-key';

    // Not really a Shlink API error. Used only as fallback value
    case UNKNOWN = 'https://shlink.io/api/error/unknown';
}
