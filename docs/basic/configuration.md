# Configuration

This SDK provides a couple of ways to provide Shlink's configuration (the base URL and API key).

This is done via configuration objects that are later passed to Shlink's HTTP Client.

Currently, this library supports the next ways to create config objects:

### Environment variables

If you want to provide the base URL and API key as env vars, create the config with `ShlinkConfig::fromEnv()`.

This approach will try to read the `SHLINK_BASE_URL` and `SHLINK_API_KEY` env vars, and throw an exception if any of them is missing.

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

try {
    $config = ShlinkConfig::fromEnv();
} catch (InvalidConfigException $e) {
    // Either 'SHLINK_BASE_URL' or 'SHLINK_API_KEY' env vars were not found.
}
```

### Raw array

If you want to provide the base URL and API key from a config array, do the following:

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

try {
    $config = ShlinkConfig::fromArray([
        'baseUrl' => 'https://my-domain.com'
        'apiKey' => 'cec2f62c-b119-452a-b351-a416a2f5f45a',
    ]);
} catch (InvalidConfigException $e) {
    // Either 'baseUrl' or 'apiKey' props were missing in the array.
}
```

### On the fly values

If you want to provide the base URL and API key on the fly, use `ShlinkConfig::fromBaseUrlAndApiKey(...)`:

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

$config = ShlinkConfig::fromBaseUrlAndApiKey('https://my-domain.com', 'cec2f62c-b119-452a-b351-a416a2f5f45a');
```
