# Domains

In order to consume the [Domains](https://api-spec.shlink.io/#/Domains) context of the API, you need to create a `DomainsClient`, as explained in [Shlink "clients"](/shlink-clients).

```php
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;

$shortUrlsClient = new DomainsClient(new HttpClient(...));
```

Once you have it, these are all the operations you can perform:

### List domains

### Configure redirects
