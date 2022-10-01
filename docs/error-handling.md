# Error handling

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
* If Shlink returns any unknown error that this SDK does not explicitly handle, it will be thrown as a `Shlinkio\Shlink\SDK\Exception\HttpException` object.
