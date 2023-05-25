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

echo 'Non-orphan visits: ' . $summary->nonOrphanVisits->total;
echo 'Orphan visits: ' . $summary->orphanVisits->total;
echo 'Total visits: ' . count($summary);
```

### Visits by short URL

```php
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

try {
    $shortUrlId = ShortUrlIdentifier::fromShortCode('abc123');
    $visits = $visitsClient->listShortUrlVisits($shortUrlId);
    
    foreach ($visits as $visit) {
        echo $visit->referer();
        echo $visit->userAgent();
        echo $visit->date()->format(DateTimeInterface::ATOM);
    }
} catch (ShortUrlNotFoundException) {
    echo 'Requested short URL could not be found'
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

try {
    $shortUrlId = ShortUrlIdentifier::fromShortCode('abc123');
    $filter = VisitsFilter::create()->excludingBots()
                                    ->since(new DateTimeImmutable('2020-01-01'))
    $filteredVisits = $visitsClient->listShortUrlVisitsWithFilter($shortUrlId, $filter);
    
    foreach ($filteredVisits as $visit) {
        echo $visit->referer();
        echo $visit->userAgent();
        echo $visit->date()->format(DateTimeInterface::ATOM);
    }
} catch (ShortUrlNotFoundException) {
    echo 'Requested short URL could not be found'
}
```

### Visits by Tag

```php
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;

try {
    $visits = $visitsClient->listTagVisits('videogames');
    
    foreach ($visits as $visit) {
        echo $visit->referer();
        echo $visit->userAgent();
        echo $visit->date()->format(DateTimeInterface::ATOM);
    }
} catch (TagNotFoundException) {
    echo 'Requested tag could not be found'
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

try {
    $filter = VisitsFilter::create()->excludingBots()
                                    ->since(new DateTimeImmutable('2020-01-01'))
    $filteredVisits = $visitsClient->listTagVisitsWithFilter('videogames', $filter);
    
    foreach ($filteredVisits as $visit) {
        echo $visit->referer();
        echo $visit->userAgent();
        echo $visit->date()->format(DateTimeInterface::ATOM);
    }
} catch (TagNotFoundException) {
    echo 'Requested tag could not be found'
}
```

### Visits by Domain

> Requires Shlink 3.1.0 or higher.

```php
use Shlinkio\Shlink\SDK\Domains\Exception\DomainNotFoundException;

try {
    $visits = $visitsClient->listDomainVisits('examp.le');
    
    foreach ($visits as $visit) {
        echo $visit->referer();
        echo $visit->userAgent();
        echo $visit->date()->format(DateTimeInterface::ATOM);
    }
} catch (DomainNotFoundException) {
    echo 'Requested domain could not be found'
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\Domains\Exception\DomainNotFoundException;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

try {
    $filter = VisitsFilter::create()->excludingBots()
                                    ->since(new DateTimeImmutable('2020-01-01'))
    $filteredVisits = $visitsClient->listDomainVisitsWithFilter('examp.le', $filter);
    
    foreach ($filteredVisits as $visit) {
        echo $visit->referer();
        echo $visit->userAgent();
        echo $visit->date()->format(DateTimeInterface::ATOM);
    }
} catch (DomainNotFoundException) {
    echo 'Requested domain could not be found'
}
```

> If you want to get visits for default domain, use `listDefaultDomainVisits()` and `listDefaultDomainVisitsWithFilter(...)` respectively.

### Orphan visits

```php
$visits = $visitsClient->listOrphanVisits();

foreach ($visits as $visit) {
    echo $visit->referer();
    echo $visit->userAgent();
    echo $visit->date()->format(DateTimeInterface::ATOM);
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

$filter = VisitsFilter::create()->excludingBots()
                                ->since(new DateTimeImmutable('2020-01-01'))
$filteredVisits = $visitsClient->listOrphanVisitsWithFilter($filter);

foreach ($filteredVisits as $visit) {
    echo $visit->referer();
    echo $visit->userAgent();
    echo $visit->date()->format(DateTimeInterface::ATOM);
}
```

### Non-orphan visits

```php
$visits = $visitsClient->listNonOrphanVisits();

foreach ($visits as $visit) {
    echo $visit->referer();
    echo $visit->userAgent();
    echo $visit->date()->format(DateTimeInterface::ATOM);
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

$filter = VisitsFilter::create()->excludingBots()
                                ->since(new DateTimeImmutable('2020-01-01'))
$filteredVisits = $visitsClient->listNonOrphanVisitsWithFilter($filter);

foreach ($filteredVisits as $visit) {
    echo $visit->referer();
    echo $visit->userAgent();
    echo $visit->date()->format(DateTimeInterface::ATOM);
}
```

### Delete orphan visits

```php
use function count;

$result = $visitsClient->deleteOrphanVisits();

echo $result->deletedVisits;
echo count($result);
```

### Delete short URL visits

```php
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

try {
    $shortUrlId = ShortUrlIdentifier::fromShortCode('abc123');
    $result = $visitsClient->deleteShortUrlVisits($shortUrlId);

    echo $result->deletedVisits;
    echo count($result);
} catch (ShortUrlNotFoundException) {
    echo 'Requested short URL could not be found'
}
```
