<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Shlinkio\Shlink\SDK\Config\Exception\InvalidConfigException;
use Shlinkio\Shlink\SDK\Http\ApiVersion;

use function trim;

final readonly class ArrayShlinkConfig implements ShlinkConfigInterface
{
    public const string BASE_URL_PROP = 'baseUrl';
    public const string API_KEY_PROP = 'apiKey';
    public const string VERSION_PROP = 'version';

    private function __construct(private ShlinkConfigInterface $wrapped) {}

    /**
     * @param array{
     *      ArrayShlinkConfig::BASE_URL_PROP?: string,
     *      ArrayShlinkConfig::API_KEY_PROP?: string,
     *      ArrayShlinkConfig::VERSION_PROP?: string,
     *  } $config
     * @throws InvalidConfigException
     */
    public static function fromArray(array $config): self
    {
        return new self(ShlinkConfig::fromRawConfig(
            new readonly class($config) implements RawConfigInterface {
                /**
                 * @param array{
                 *     ArrayShlinkConfig::BASE_URL_PROP?: string,
                 *     ArrayShlinkConfig::API_KEY_PROP?: string,
                 *     ArrayShlinkConfig::VERSION_PROP?: string,
                 * } $config
                 */
                public function __construct(private array $config) {}

                public function baseUrl(): string
                {
                    return trim($this->config[ArrayShlinkConfig::BASE_URL_PROP] ?? '');
                }

                public function apiKey(): string
                {
                    return trim($this->config[ArrayShlinkConfig::API_KEY_PROP] ?? '');
                }

                public function version(): string
                {
                    return trim($this->config[ArrayShlinkConfig::VERSION_PROP] ?? '');
                }

                public function missingConfigException(): InvalidConfigException
                {
                    return InvalidConfigException::forInvalidConfig();
                }
            },
        ));
    }

    public function baseUrl(): string
    {
        return $this->wrapped->baseUrl();
    }

    public function apiKey(): string
    {
        return $this->wrapped->apiKey();
    }

    public function version(): ApiVersion
    {
        return $this->wrapped->version();
    }
}
