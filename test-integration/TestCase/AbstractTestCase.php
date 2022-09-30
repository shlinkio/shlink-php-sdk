<?php

declare(strict_types=1);

namespace ShlinkioIntegrationTest\Shlink\SDK\TestCase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\EnvShlinkConfig;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;

class AbstractTestCase extends TestCase
{
    protected static function httpClient(): HttpClientInterface
    {
        static $client;
        if ($client === null) {
            $httpFactory = new HttpFactory();
            $client = new HttpClient(
                new Client(['http_errors' => false]),
                $httpFactory,
                $httpFactory,
                EnvShlinkConfig::fromEnv(),
            );
        }

        return $client;
    }
}
