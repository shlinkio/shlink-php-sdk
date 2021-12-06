<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Builder;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;

class ShlinkClientBuilderTest extends TestCase
{
    use ClientBuilderMethodsProviderTrait;
    use ProphecyTrait;

    private ShlinkClientBuilder $builder;
    private ObjectProphecy $client;
    private ObjectProphecy $requestFactory;
    private ObjectProphecy $streamFactory;
    private ShlinkConfigInterface $config;

    public function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $this->streamFactory = $this->prophesize(StreamFactoryInterface::class);

        $this->builder = new ShlinkClientBuilder(
            $this->client->reveal(),
            $this->requestFactory->reveal(),
            $this->streamFactory->reveal(),
        );
        $this->config = ShlinkConfig::fromBaseUrlAndApiKey('foo', 'bar');
    }

    /**
     * @test
     * @dataProvider provideMethods
     */
    public function buildClientReturnsAlwaysNewInstances(string $method): void
    {
        $instance1 = $this->builder->{$method}($this->config);
        $instance2 = $this->builder->{$method}($this->config);

        self::assertEquals($instance1, $instance2);
        self::assertNotSame($instance1, $instance2);
    }
}
