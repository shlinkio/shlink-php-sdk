<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Builder;

use Override;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\Domains\DomainsClientInterface;
use Shlinkio\Shlink\SDK\RedirectRules\RedirectRulesClientInterface;
use Shlinkio\Shlink\SDK\ShortUrls\ShortUrlsClientInterface;
use Shlinkio\Shlink\SDK\Tags\TagsClientInterface;
use Shlinkio\Shlink\SDK\Visits\VisitsClientInterface;

use function sprintf;

class SingletonShlinkClientBuilder implements ShlinkClientBuilderInterface
{
    private array $instances = [];

    public function __construct(private readonly ShlinkClientBuilderInterface $wrapped) {}

    #[Override]
    public function buildShortUrlsClient(ShlinkConfigInterface $config): ShortUrlsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[ShortUrlsClientInterface::class][$key] ??=
            $this->wrapped->buildShortUrlsClient($config);
    }

    #[Override]
    public function buildVisitsClient(ShlinkConfigInterface $config): VisitsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[VisitsClientInterface::class][$key] ??= $this->wrapped->buildVisitsClient($config);
    }

    #[Override]
    public function buildTagsClient(ShlinkConfigInterface $config): TagsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[TagsClientInterface::class][$key] ??= $this->wrapped->buildTagsClient($config);
    }

    #[Override]
    public function buildDomainsClient(ShlinkConfigInterface $config): DomainsClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[DomainsClientInterface::class][$key] ??= $this->wrapped->buildDomainsClient($config);
    }

    #[Override]
    public function buildRedirectRulesClient(ShlinkConfigInterface $config): RedirectRulesClientInterface
    {
        $key = $this->configToKey($config);
        return $this->instances[RedirectRulesClientInterface::class][$key] ??=
            $this->wrapped->buildRedirectRulesClient(
                $config,
            );
    }

    private function configToKey(ShlinkConfigInterface $config): string
    {
        return sprintf('%s_%s', $config->baseUrl(), $config->apiKey());
    }
}
