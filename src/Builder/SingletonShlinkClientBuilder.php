<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Builder;

use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

use function sprintf;

class SingletonShlinkClientBuilder implements ShlinkClientBuilderInterface
{
    private array $instances = [];

    public function __construct(private readonly ShlinkClientBuilderInterface $wrapped)
    {
    }

    public function buildShortUrlsClient(ShlinkConfigInterface $config): ShortUrlsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[ShortUrlsClientInterface::class][$key] ?? (
            $this->instances[ShortUrlsClientInterface::class][$key] = $this->wrapped->buildShortUrlsClient($config)
        );
    }

    public function buildVisitsClient(ShlinkConfigInterface $config): VisitsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[VisitsClientInterface::class][$key] ?? (
            $this->instances[VisitsClientInterface::class][$key] = $this->wrapped->buildVisitsClient($config)
        );
    }

    public function buildTagsClient(ShlinkConfigInterface $config): TagsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[TagsClientInterface::class][$key] ?? (
            $this->instances[TagsClientInterface::class][$key] = $this->wrapped->buildTagsClient($config)
        );
    }

    public function buildDomainsClient(ShlinkConfigInterface $config): DomainsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[DomainsClientInterface::class][$key] ?? (
            $this->instances[DomainsClientInterface::class][$key] = $this->wrapped->buildDomainsClient($config)
        );
    }

    private function configToKey(ShlinkConfigInterface $config): string
    {
        return sprintf('%s_%s', $config->baseUrl(), $config->apiKey());
    }
}
