# Debugging

If you need to debug what HTTP requests the SDK is doing, the `HttpClient` and `ShlinkClientBuilder` can be provided with an optional last argument, which is an object implementing `Shlinkio\Shlink\SDK\Http\Debug\HttpDebuggerInterface`.

This interface exposes a method that will get invoked with the object sent for every request.

This way, you will be able to inspect or log the headers, URL, body, or anything you need.

```php
use Psr\Http\Message\RequestInterface;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Http\Debug\HttpDebuggerInterface;
use Shlinkio\Shlink\SDK\Http\HttpClient;

$debugger = new class implements HttpDebuggerInterface {
    public function debugRequest(RequestInterface $req): void
    {
        var_dump($req->getUri()->__toString());
        var_dump($req->getBody()->__toString());
        var_dump($req->getHeaders());
    }
};

$httpClient = new HttpClient(
    /* Client */,
    /* RequestBuilder */,
    /* StreamBuilder */,
    ShlinkConfig::fromEnv(),
    $debugger,
)
$builder = new ShlinkClientBuilder(
    /* Client */,
    /* RequestBuilder */,
    /* StreamBuilder */,
    $debugger,
)
```

> Notice that this debugger cannot be used to manipulate the request object before the request itself is performed, since PSR-7 request objects are immutable.
