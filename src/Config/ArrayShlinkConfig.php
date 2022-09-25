<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;

use function trim;

final class ArrayShlinkConfig implements ShlinkConfigInterface
{
    public const BASE_URL_PROP = 'baseUrl';
    public const API_KEY_PROP = 'apiKey';

    private function __construct(private readonly ShlinkConfigInterface $wrapped)
    {
    }

    /**
     * @throws InvalidConfigException
     */
    public static function fromArray(array $config): self
    {
        if (! isset($config[self::BASE_URL_PROP], $config[self::API_KEY_PROP])) {
            throw InvalidConfigException::forInvalidConfig();
        }

        $baseUrl = trim($config[self::BASE_URL_PROP]);
        $apiKey = trim($config[self::API_KEY_PROP]);

        return new self(ShlinkConfig::fromBaseUrlAndApiKey($baseUrl, $apiKey));
    }

    public function baseUrl(): string
    {
        return $this->wrapped->baseUrl();
    }

    public function apiKey(): string
    {
        return $this->wrapped->apiKey();
    }
}
