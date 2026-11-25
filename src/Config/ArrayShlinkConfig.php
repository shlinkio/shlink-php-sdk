<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Config;

use Override;
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

                #[Override]
                public function baseUrl(): string
                {
                    return trim($this->config[ArrayShlinkConfig::BASE_URL_PROP] ?? '');
                }

                #[Override]
                public function apiKey(): string
                {
                    return trim($this->config[ArrayShlinkConfig::API_KEY_PROP] ?? '');
                }

                #[Override]
                public function version(): string
                {
                    return trim($this->config[ArrayShlinkConfig::VERSION_PROP] ?? '');
                }

                #[Override]
                public function missingConfigException(): InvalidConfigException
                {
                    return InvalidConfigException::forInvalidConfig();
                }
            },
        ));
    }

    #[Override]
    public function baseUrl(): string
    {
        return $this->wrapped->baseUrl();
    }

    #[Override]
    public function apiKey(): string
    {
        return $this->wrapped->apiKey();
    }

    #[Override]
    public function version(): ApiVersion
    {
        return $this->wrapped->version();
    }
}
