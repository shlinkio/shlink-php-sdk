# Shlink PHP SDK

[![Build Status](https://img.shields.io/github/workflow/status/shlinkio/shlink-php-sdk/Continuous%20integration/main?logo=github&style=flat-square)](https://github.com/shlinkio/shlink-php-sdk/actions?query=workflow%3A%22Continuous+integration%22)
[![Code Coverage](https://img.shields.io/codecov/c/gh/shlinkio/shlink-php-sdk/main?style=flat-square)](https://app.codecov.io/gh/shlinkio/shlink-php-sdk)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-php-sdk.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-php-sdk)
[![License](https://img.shields.io/github/license/shlinkio/shlink-php-sdk.svg?style=flat-square)](https://github.com/shlinkio/shlink-php-sdk/blob/main/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://slnk.to/donate)

A PHP SDK to consume Shlink's REST API in a very convenient and robust way.

* Very expressive API.
* Decoupled from implementations. Depending only on PSR-11, PSR-17 and PSR-18 interfaces.
* Dependency injection. Every service can be composed out of a set of pieces.
* Statically typed and immutable DTOs, with meaningful named constructors.
* Generator-based iterable collections, to abstract pagination and reduce resource consumption.
* Error handling via contextual exceptions.
* Extensively tested. Unit tests, integration tests and mutation testing.

## Installation

Install the SDK with composer.

    composer install shlinkio/shlink-php-sdk

## Usage

This library provides different services to consume every context of the Shlink API.

For example, if you want to consume `short-urls` endpoints, you would do something like this:

```php
use Cake\Chronos\Chronos;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlListOrderFields;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;

use function count;

$httpClient = new HttpClient(
    new Client(), // Any object implementing Psr\Http\Client\ClientInterface from PSR-18
    new HttpFactory(), // Any object implementing Psr\Http\Message\RequestFactoryInterface from PSR-17
    new HttpFactory(), // Any object implementing Psr\Http\Message\StreamFactoryInterface from PSR-17
    ShlinkConfig::fromEnv()
)
$client = new ShortUrlsClient($httpClient)

$filter = ShortUrlsFilter::create()
    ->containingTags('foo', 'bar')
    ->since(Chronos::now()->subDays(10)) // Any object implementing DateTimeInterface
    ->orderingAscBy(ShortUrlListOrderFields::VISITS)
$shortUrls = $client->listShortUrlsWithFilter($filter)

echo 'The total amount of short URLs is ' . count($shortUrls);

foreach ($shortUrls as $shortUrl) {
    echo 'Short URL: ' . $shortUrl->shortUrl();
    echo 'Amount of visits: ' . $shortUrl->visitsCount();
}
```

### Shlink configuration

This SDK provides a couple of ways to provide Shlink's config (mainly base URL and API key).

This is done via configuration objects that are later passed to Shlink's HTTP Client.

Currently, this library supports the next ways to create config objects:

#### Environment variables

If you want to provide the base URL and API key as env vars, do this:

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

try {
    $config = ShlinkConfig::fromEnv();
} catch (InvalidConfigException $e) {
    // Either 'SHLINK_BASE_URL' or 'SHLINK_API_KEY' env vars were not found.
}
```

#### Raw array

If you want to provide the base URL and API key from a config array, do this:

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

try {
    $config = ShlinkConfig::fromArray([
        'baseUrl' => 'https://my-domain.com'
        'apiKey' => 'cec2f62c-b119-452a-b351-a416a2f5f45a',
    ]);
} catch (InvalidConfigException $e) {
    // Either 'baseUrl' or 'apiKey' props were missing in the array.
}
```

#### On the fly values

If you want to provide the base URL and API key on the fly, do this:

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

$config = ShlinkConfig::fromBaseUrlAndApiKey('https://my-domain.com', 'cec2f62c-b119-452a-b351-a416a2f5f45a');
```

### Shlink "Clients"

As mentioned above, the SDK provides different services to consume every context of the API, `ShortUrlsClient`, `VisitsClient`, `TagsClient` and `DomainsClient`.

They all expect an `HttpClient` to be injected, and implement their corresponding interfaces.

For convenience, a `ShlinkClient` service is also provided, which wraps an instance of each of the clients above, and implements all their interfaces.

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\ShlinkClient;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;
use Shlinkio\Shlink\SDK\Tags\TagsClient;
use Shlinkio\Shlink\SDK\Visits\VisitsClient;

$httpFactory = new HttpFactory();
$httpClient = new HttpClient(new Client(), $httpFactory, $httpFactory, ShlinkConfig::fromEnv());
$shortUrlsClient = new ShortUrlsClient($httpClient);
$visitsClient = new VisitsClient($httpClient);
$tagsClient = new TagsClient($httpClient);
$domainsClient = new DomainsClient($httpClient);

$client = new ShlinkClient($shortUrlsClient, $visitsClient, $tagsClient, $domainsClient);
```

## PSR-11 container integration

In the examples above you have seen the dependency injection graph is slightly complex, with a couple of dependency levels until you get a usable API client ready.

In order to simplify creating objects, this SDK provides some basic factories for PSR-11 containers.

[TODO]

## Error handling

This SDK will wrap all the known errors that Shlink's API can return into meaningful exceptions.

Some examples. Deleting a short URL:

```php
use Shlinkio\Shlink\SDK\ShortUrls\Exception\DeleteShortUrlThresholdException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;

$shortUrlsClient = new ShortUrlsClient(...);

try {
    $shortUrlsClient->deleteShortUrl(ShortUrlIdentifier::fromShortCode('abc123'));
} catch (ShortUrlNotFoundException $e) {
    // A short URL with short code 'abc123' was not found.
} catch (DeleteShortUrlThresholdException $e) {
    // The short URL has reached a threshold of visits which prevents it from being deleted
}
```

Renaming a tag:

```php
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\TagsClient;

$tagsClient = new TagsClient(...);

try {
    $tagsClient->renameTag(TagRenaming::fromOldNameAndNewName('oldName', 'newName'));
} catch (TagNotFoundException $e) {
    // The tag `oldName` was not found
} catch (TagConflictException $e) {
    // There's already another tag with name `newName`
} catch (ForbiddenTagOperationException $e) {
    // Provided API key does not have permissions to rename tags
}
```

Considerations:

* All methods annotate the known exceptions they can throw, so your IDE of choice will most probably notify of any missing `catch`.
* All exceptions thrown by the library implement the marker interface `Shlinkio\Shlink\SDK\Exception\ExceptionInterface`, in case you want to capture all of them in a single `catch` block.

## Debugging

If you need to debug what HTTP requests the SDK is doing, the `HttpClient` can be provided with an optional fifth argument, which is an object implementing `Shlinkio\Shlink\SDK\Http\Debug\HttpDebuggerInterface`.

This interface exposes a method that will get invoked with the request object sent for every request.

This way, you will be able to inspect or log the headers, URL, body, or anything you need to know.

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Http\Debug\HttpDebuggerInterface;
use Shlinkio\Shlink\SDK\Http\HttpClient;

$httpClient = new HttpClient(
    new Client(),
    new HttpFactory(),
    new HttpFactory(),
    ShlinkConfig::fromEnv(),
    new class implements HttpDebuggerInterface {
        public function debugRequest(RequestInterface $req): void
        {
            var_dump($req->getUri()->__toString());
            var_dump($req->getBody()->__toString());
            var_dump($req->getHeaders());
        }
    }
)
```
