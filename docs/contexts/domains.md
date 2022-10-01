# Domains

In order to consume the [Domains](https://api-spec.shlink.io/#/Domains) context of the API, you need to create a `DomainsClient`, as explained in [Shlink "clients"](/shlink-clients).

```php
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;

$domainsClient = new DomainsClient(new HttpClient(...));
```

Once you have it, these are all the operations you can perform:

### List domains

```php
$domains = $domainsClient->listDomains();

foreach ($domains as $domain) {
    echo 'Domain: ' . $domain->domain;
    echo 'Is default: ' . ($domain->isDefault ? 'YES' : 'NO');
}
```

### Configure redirects

```php
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;

$redirects = DomainRedirectsConfig::forDomain('slnk.to')->withBaseUrlRedirect('https://shlink.io')
                                                        ->removingRegularNotFoundRedirect();
$result = $domainsClient->configureDomainRedirects($redirects);

echo 'Base URL: ' . ($result->baseUrlRedirect ?? 'N/A');
echo 'Regular 404: ' . ($result->regularNotFoundRedirect ?? 'N/A');
echo 'Invalid short URL: ' . ($result->invalidShortUrlRedirect ?? 'N/A');
```
