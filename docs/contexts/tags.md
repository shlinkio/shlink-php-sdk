# Tags

In order to consume the [Tags](https://api-spec.shlink.io/#/Tags) context of the API, you need to create a `TagsClient`, as explained in [Shlink "clients"](/shlink-clients).

```php
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Tags\TagsClient;

$tagsClient = new TagsClient(new HttpClient(...));
```

Once you have it, these are all the operations you can perform:

### List tags


```php
$allTags = $tagsClient->listTags();

foreach ($allTags as $tag) {
    echo $tag;
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\Tags\Model\TagsListOrderField;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;

$filter = TagsFilter::create()->searchingBy('videogames')
                              ->orderingAscBy(TagsListOrderField::TAG);
$filteredTags = $tagsClient->listTagsWithFilter($filter);

foreach ($filteredTags as $tag) {
    echo $tag;
}
```

### List tags with stats


```php
$allTagsWithSTats = $tagsClient->listTagsWithStats();

foreach ($allTagsWithSTats as $tag) {
    echo 'Tag: ' . $tag->tag;
    echo 'Short URLs could: ' . $tag->shortUrlsCount;
    echo 'Visits could: ' . $tag->visitsCount;
}
```

You can also specify some filters for the list:

```php
use Shlinkio\Shlink\SDK\Tags\Model\TagsListOrderField;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;

$filter = TagsFilter::create()->searchingBy('videogames')
                              ->orderingAscBy(TagsListOrderField::SHORT_URLS_COUNT);
$filteredTagsWithSTats = $tagsClient->listTagsWithStatsWithFilter($filter);

foreach ($filteredTagsWithSTats as $tag) {
    echo 'Tag: ' . $tag->tag;
    echo 'Short URLs could: ' . $tag->shortUrlsCount;
    echo 'Visits could: ' . $tag->visitsCount;
}
```

### Rename tag

```php
use Shlinkio\Shlink\SDK\Exception\InvalidDataException;
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagConflictException;
use Shlinkio\Shlink\SDK\Tags\Exception\TagNotFoundException;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;

try {
    $renaming = TagRenaming::fromOldNameAndNewName('games', 'videogames');
    $tagsClient->renameTag($renaming);
} catch (InvalidDataException) {
    echo 'Provided data is invalid';
} catch (ForbiddenTagOperationException) {
    echo 'Used API key does not have permission to rename tags';
} catch (TagConflictException) {
    echo 'Another tag with the new name already exists';
} catch (TagNotFoundException) {
    echo 'A tag with the old name was not found';
}
```

### Delete tag

```php
use Shlinkio\Shlink\SDK\Tags\Exception\ForbiddenTagOperationException;

try {
    $tagsClient->deleteTags('videogames', 'marketing', 'blog');
} catch (ForbiddenTagOperationException) {
    echo 'Used API key does not have permission to delete tags';
}
```
