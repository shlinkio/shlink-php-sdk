<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\Builder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilderInterface;
use Shlinkio\Shlink\SDK\Builder\SingletonShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\ShlinkClient;

class SingletonShlinkClientBuilderTest extends TestCase
{
    use ClientBuilderMethodsProviderTrait;

    private SingletonShlinkClientBuilder $builder;
    private MockObject & ShlinkClientBuilderInterface $wrapped;

    public function setUp(): void
    {
        $this->wrapped = $this->createMock(ShlinkClientBuilderInterface::class);
        $this->builder = new SingletonShlinkClientBuilder($this->wrapped);
    }

    /**
     * @test
     * @dataProvider provideMethods
     */
    public function buildClientReturnsAlwaysNewInstances(string $method): void
    {
        $this->wrapped->expects($this->exactly(2))->method($method)->with(
            $this->isInstanceOf(ShlinkConfigInterface::class),
        )->willReturn($this->createMock(ShlinkClient::class));

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
    }
}
