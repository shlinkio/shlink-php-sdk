<?php

declare(strict_types=1);

namespace ShlinkioIntegrationTest\Shlink\SDK\ShortUrls;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;

use function json_decode;

class ShortUrlsClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'https://acel.me',
            'http_errors' => false,
            'headers' => ['x-api-key' => 'redacted'],
        ]);
    }

    /** @test */
    public function shortUrlCanBeCreated(): void
    {
        $resp = $this->client->post('/rest/v2/short-urls', [
            RequestOptions::JSON => ['longUrl' => 'https://shlink.io'],
        ]);
        $body = json_decode($resp->getBody()->__toString(), true);

        self::assertEquals('https://shlink.io', $body['longUrl']);

        $resp = $this->client->delete('/rest/v2/short-urls/' . $body['shortCode']);

        self::assertEquals(204, $resp->getStatusCode());
    }
}
