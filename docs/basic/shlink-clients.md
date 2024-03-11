# Shlink "clients"

In order to make sure you always have access just to the minimum amount of resources needed, this SDK provides different services to consume every context of the API:

* `ShortUrlsClient`
* `VisitsClient`
* `TagsClient`
* `DomainsClient`
* `RedirectRulesClient`

They all expect an `HttpClient` to be injected, and implement their corresponding interfaces.

For convenience, a `ShlinkClient` service is also provided, which wraps an instance of each of the clients above, and implements all their interfaces.

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\RedirectRules\RedirectRulesClient;
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
$redirectRulesClient = new RedirectRulesClient($httpClient);

$client = new ShlinkClient($shortUrlsClient, $visitsClient, $tagsClient, $domainsClient, $redirectRulesClient);
```

### Client Builder

Sometimes you may not know the Shlink config params before runtime, for example, if they are going to be dynamically provided.

When this happens, it's not possible to predefine the clients creation as in the examples above.

For those cases, the `ShlinkClientBuilder` is provided. It depends on PSR-17 and 18 adapters, and exposes methods to build client instances from a `ShlinkConfig` object.

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

$builder = new ShlinkClientBuilder(
    new Client(), // Any object implementing PSR-18's Psr\Http\Client\ClientInterface
    new HttpFactory(), // Any object implementing PSR-17's Psr\Http\Message\RequestFactoryInterface
    new HttpFactory(), // Any object implementing PSR-17's Psr\Http\Message\StreamFactoryInterface
);

// Later at runtime...
$config = ShlinkConfig::fromBaseUrlAndApiKey(
    // Get base URL and API Key from somewhere...
);

$visitsClient = $builder->buildVisitsClient($config);
$visitsClient->listTagVisits('foo');

$shortUrlsClient = $builder->buildShortUrlsClient($config);
$shortUrlsClient->deleteShortUrl(ShortUrlIdentifier::fromShortCode('bar'));
```

### Singleton instances

In the example above, the `ShlinkClientBuilder` will return a new client instance every time any of the `build...()` methods is invoked.

If you want to make sure the same instance is always returned for a set of base URL + API key, you can wrap it into a `SingletonShlinkClientBuilder` instance, which also implements `ShlinkClientBuilderInterface` and thus, it can be safely replace the regular `ShlinkClientBuilder`.

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Builder\SingletonShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

$builder = new SingletonShlinkClientBuilder(
    new ShlinkClientBuilder(new Client(), new HttpFactory(), new HttpFactory()),
);
$config = ShlinkConfig::fromBaseUrlAndApiKey(...);

$client1 = $builder->buildTagsClient($config);
$client2 = $builder->buildTagsClient($config);

var_dump($client1 === $client2); // This is true
```
