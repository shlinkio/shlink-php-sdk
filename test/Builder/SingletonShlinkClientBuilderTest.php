<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Builder;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilderInterface;
use Shlinkio\Shlink\SDK\Builder\SingletonShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\ShlinkClient;

class SingletonShlinkClientBuilderTest extends TestCase
{
    use ClientBuilderMethodsProviderTrait;
    use ProphecyTrait;

    private SingletonShlinkClientBuilder $builder;
    private ObjectProphecy $wrapped;

    public function setUp(): void
    {
        $this->wrapped = $this->prophesize(ShlinkClientBuilderInterface::class);
        $this->builder = new SingletonShlinkClientBuilder($this->wrapped->reveal());
        $this->config = ShlinkConfig::fromBaseUrlAndApiKey('foo', 'bar');
    }

    /**
     * @test
     * @dataProvider provideMethods
     */
    public function buildClientReturnsAlwaysNewInstances(string $method): void
    {
        $call = $this->wrapped->__call($method, [Argument::type(ShlinkConfigInterface::class)])->willReturn(
            $this->prophesize(ShlinkClient::class)->reveal(),
        );

        $configOne = ShlinkConfig::fromBaseUrlAndApiKey('foo', 'bar');
        $instance1 = $this->builder->{$method}($configOne);
        $instance2 = $this->builder->{$method}($configOne);
        self::assertSame($instance1, $instance2);

        $configTwo = ShlinkConfig::fromBaseUrlAndApiKey('bar', 'foo');
        $instance1 = $this->builder->{$method}($configTwo);
        $instance2 = $this->builder->{$method}($configTwo);
        $instance3 = $this->builder->{$method}($configTwo);
        self::assertSame($instance1, $instance2);
        self::assertSame($instance1, $instance3);
        self::assertSame($instance2, $instance3);

        $call->shouldHaveBeenCalledTimes(2);
    }
}
