<?php

declare(strict_types=1);

namespace ShlinkioIntegrationTest\Shlink\SDK\TestCase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Config\EnvShlinkConfig;
use Shlinkio\Shlink\SDK\Domains\DomainsClient;
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\Http\HttpClientInterface;
use Shlinkio\Shlink\SDK\RedirectRules\RedirectRulesClient;
use Shlinkio\Shlink\SDK\ShlinkClient;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClient;
use Shlinkio\Shlink\SDK\Tags\TagsClient;
use Shlinkio\Shlink\SDK\Visits\VisitsClient;

use function array_pad;
use function explode;
use function getenv;
use function implode;
use function version_compare;

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

    protected static function shlinkClient(): ShlinkClient
    {
        static $shlinkClient;
        if ($shlinkClient === null) {
            $httpClient = self::httpClient();
            $shlinkClient = new ShlinkClient(
                new ShortUrlsClient($httpClient),
                new VisitsClient($httpClient),
                new TagsClient($httpClient),
                new DomainsClient($httpClient),
                new RedirectRulesClient($httpClient),
            );
        }

        return $shlinkClient;
    }

    #[After]
    public function cleanupTestArtifacts(): void
    {
        $shlinkClient = self::shlinkClient();

        // Delete all short URLs (and their visits), all tags and all orphan visits after every test
        // This ensures side effects in one test do not affect subsequent ones
        $tags = $shlinkClient->listTags();
        $shlinkClient->deleteTags(...$tags);

        $shortUrls = $shlinkClient->listShortUrls();
        foreach ($shortUrls as $shortUrl) {
            $shlinkClient->deleteShortUrl($shortUrl->identifier());
        }

        // Do not try to delete orphan visits for versions older than 3.6.0, as it's not supported there
        $version = getenv('SHLINK_VERSION') ?: '';
        $normalizedVersion = implode('.', array_pad(explode('.', $version), length: 3, value: '0'));
        if (version_compare($normalizedVersion, '3.6.0') >= 0) {
            $shlinkClient->deleteOrphanVisits();
        }
    }
}
