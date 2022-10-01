# Tags

In order to consume the [Tags](https://api-spec.shlink.io/#/Tags) context of the API, you need to create a `TagsClient`, as explained in [Shlink "clients"](/shlink-clients).

```php
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Tags\TagsClient;

$shortUrlsClient = new TagsClient(new HttpClient(...));
```

Once you have it, these are all the operations you can perform:

### List tags

### List tags with stats

### Rename tag

### Delete tag
