# Short URLs

In order to consume the [Short URLs](https://api-spec.shlink.io/#/Short%20URLs) context of the API, you need to create a `ShortUrlsClient`, as explained in [Shlink "clients"](/shlink-clients).

```php
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;

$shortUrlsClient = new ShortUrlsClient(new HttpClient(...));
```

Once you have it, these are all the operations you can perform:

### List short URLs

```php
$allShortUrls = $shortUrlsClient->listShortUrls();

foreach ($allShortUrls as $shortUrl) {
    echo $shortUrl->shortUrl;
    echo $shortUrl->longUrl;
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlListOrderField;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;

$filter = ShortUrlsFilter::create()->searchingBy('foobar')
                                   ->containingSomeTags('videogames', 'development')
                                   ->orderingDescBy(ShortUrlListOrderField::TITLE);
$filteredShortUrls = $shortUrlsClient->listShortUrlsWithFilter($filter);

foreach ($filteredShortUrls as $shortUrl) {
    echo $shortUrl->shortUrl;
    echo $shortUrl->longUrl;
}
```

### Get individual short URLs

```php
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

try {
    $shortUrl = $shortUrlsClient->getShortUrl(ShortUrlIdentifier::fromShortCode('abc123'));

    echo $shortUrl->shortUrl;
    echo $shortUrl->longUrl;
} catch (ShortUrlNotFoundException) {
    echo 'Short URL not found';
}
```

### Create new short URL

```php
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\InvalidLongUrlException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\NonUniqueSlugException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;

try {
    $creation = ShortUrlCreation::forLongUrl('https://shlink.io')->withCustomSlug('shlink')
                                                                 ->withMaxVisits(1000)
                                                                 ->validSince(new DateTimeImmutable('2022-05-01'));
    $shortUrl = $shortUrlsClient->createShortUrl($creation);

    echo $shortUrl->shortUrl;
    echo $shortUrl->longUrl;
} catch (NonUniqueSlugException) {
    echo 'There is already a short URL using this custom slug';
} catch (InvalidLongUrlException) {
    echo 'The long URL is not reachable';
} catch (InvalidDataException) {
    echo 'Provided data is invalid';
}
```

### Delete short URL

```php
use Shlinkio\Shlink\SDK\ShortUrls\Exception\DeleteShortUrlThresholdException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

try {
    $shortUrl = $shortUrlsClient->deleteShortUrl(ShortUrlIdentifier::fromShortCode('abc123'));

    echo $shortUrl->shortUrl;
    echo $shortUrl->longUrl;
} catch (ShortUrlNotFoundException) {
    echo 'Short URL not found';
} catch (DeleteShortUrlThresholdException) {
    echo 'The short URL already received too many visits';
}
```

### Edit short URL

```php
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\ShortUrls\Exception\ShortUrlNotFoundException;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

try {
    $edition = ShortUrlEdition::create()->crawlable()
                                        ->withMaxVisits(1000)
                                        ->removingTitle()
                                        ->withTags('videogames', 'ai');
    $shortUrl = $shortUrlsClient->editShortUrl(ShortUrlIdentifier::fromShortCode('abc123'), $edition);

    echo $shortUrl->shortUrl;
    echo $shortUrl->longUrl;
} catch (ShortUrlNotFoundException) {
    echo 'Short URL not found';
} catch (InvalidDataException) {
    echo 'Provided data is invalid';
}
```
