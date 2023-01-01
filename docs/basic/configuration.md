# Configuration

This SDK provides a couple of ways to provide Shlink's configuration (the base URL and API key).

This is done via configuration objects that are later passed to Shlink's HTTP Client.

Currently, this library supports the next ways to create config objects:

### Environment variables

If you want to provide the base URL and API key as env vars, create the config with `ShlinkConfig::fromEnv()`.

This approach will try to read the `SHLINK_BASE_URL` and `SHLINK_API_KEY` env vars, and throw an exception if any of them is missing.

It will also try to read `SHLINK_API_VERSION` to determine the API version to use, expecting values "2" or "3". It will fall back to "2" if not provided.

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

try {
    $config = ShlinkConfig::fromEnv();
} catch (InvalidConfigException $e) {
    // Either 'SHLINK_BASE_URL' or 'SHLINK_API_KEY' env vars were not found,
    // or the value provided for 'SHLINK_API_VERSION' is different from "2" or "3".
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
        'version' => '3',
    ]);
} catch (InvalidConfigException $e) {
    // Either 'baseUrl' or 'apiKey' props were missing in the array,
    // or 'version' prop has a value different from "2" or "3".
}
```

### On the fly values

If you want to provide the base URL and API key on the fly, use `ShlinkConfig::fromV2BaseUrlAndApiKey(...)` or `ShlinkConfig::fromV3BaseUrlAndApiKey(...)` (depending on which API version you want to use):

```php
use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;

$config = ShlinkConfig::fromV2BaseUrlAndApiKey('https://my-domain.com', 'cec2f62c-b119-452a-b351-a416a2f5f45a');
$config = ShlinkConfig::fromV3BaseUrlAndApiKey('https://my-domain.com', 'cec2f62c-b119-452a-b351-a416a2f5f45a');
```
