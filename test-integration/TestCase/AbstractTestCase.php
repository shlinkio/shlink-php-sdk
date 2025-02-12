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

    #[After]
    public function cleanupTestArtifacts(): void
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

        // Delete all short URLs (and their visits), all tags and all orphan visits after every test
        // This ensures side effects in one test do not affect subsequent ones
        $tags = $shlinkClient->listTags();
        $shlinkClient->deleteTags(...$tags);

        $shortUrls = $shlinkClient->listShortUrls();
        foreach ($shortUrls as $shortUrl) {
            $shlinkClient->deleteShortUrl($shortUrl->identifier());
        }

        $shlinkClient->deleteOrphanVisits();
    }
}
