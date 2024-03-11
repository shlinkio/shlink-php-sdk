# Getting started

This library provides different client services to consume every context of the Shlink API. Each of those services depends on a helper `HttpClient` service, which wraps the logic to perform HTTP calls to a Shlink instance's REST API.

For example, if you want to consume `short-urls` endpoints, you would do something like this:

```php
use Cake\Chronos\Chronos;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlListOrderField;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;

use function count;

$httpClient = new HttpClient(
    new Client(), // Any object implementing PSR-18's Psr\Http\Client\ClientInterface
    new HttpFactory(), // Any object implementing PSR-17's Psr\Http\Message\RequestFactoryInterface
    new HttpFactory(), // Any object implementing PSR-17's Psr\Http\Message\StreamFactoryInterface
    ShlinkConfig::fromEnv()
)
$client = new ShortUrlsClient($httpClient)

$filter = ShortUrlsFilter::create()
    ->containingSomeTags('foo', 'bar')
    ->since(Chronos::now()->subDays(10)) // Any object implementing DateTimeInterface
    ->orderingAscBy(ShortUrlListOrderField::VISITS)
$shortUrls = $client->listShortUrlsWithFilter($filter)

echo 'The total amount of short URLs is ' . count($shortUrls);

foreach ($shortUrls as $shortUrl) {
    echo 'Short URL: ' . $shortUrl->shortUrl;
    echo 'Amount of visits: ' . $shortUrl->visitsSummary->total;
}
```
