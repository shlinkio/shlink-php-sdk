# Visits

In order to consume the [Visits](https://api-spec.shlink.io/#/Visits) context of the API, you need to create a `VisitsClient`, as explained in [Shlink "clients"](/shlink-clients).

```php
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Visits\VisitsClient;

$visitsClient = new VisitsClient(new HttpClient(...));
```

Once you have it, these are all the operations you can perform:

### Visits summary

```php
use function count;

$summary = $visitsClient->getVisitsSummary();

echo 'Non-orphan visits: ' . $summary->visitsCount;
echo 'Orphan visits: ' . $summary->orphanVisitsCount;
echo 'Total visits: ' . count($summary);
```

### Visits by short URL

### Visits by Tag

### Visits by Domain

### Orphan visits

### Non-orphan visits
