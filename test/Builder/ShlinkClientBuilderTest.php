<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Builder;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;

class ShlinkClientBuilderTest extends TestCase
{
    use ClientBuilderMethodsProviderTrait;

    private ShlinkClientBuilder $builder;
    private ShlinkConfigInterface $config;

    public function setUp(): void
    {
        $this->builder = new ShlinkClientBuilder(
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
        );
        $this->config = ShlinkConfig::fromBaseUrlAndApiKey('foo', 'bar');
    }

    #[Test, DataProvider('provideMethods')]
    public function buildClientReturnsAlwaysNewInstances(string $method): void
    {
        $instance1 = $this->builder->{$method}($this->config);
        $instance2 = $this->builder->{$method}($this->config);

        self::assertEquals($instance1, $instance2);
        self::assertNotSame($instance1, $instance2);
    }
}
