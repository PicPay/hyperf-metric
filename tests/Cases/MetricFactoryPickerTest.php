<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf + PicPay.
 *
 * @link     https://github.com/PicPay/hyperf-metric
 * @document https://github.com/PicPay/hyperf-metric/wiki
 * @contact  @PicPay
 * @license  https://github.com/PicPay/hyperf-metric/blob/main/LICENSE
 */
namespace HyperfTest\Metric\Cases;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Container;
use Hyperf\Metric\Adapter\NoOp\MetricFactory as NoOpFactory;
use Hyperf\Metric\Adapter\Prometheus\MetricFactory as PrometheusFactory;
use Hyperf\Metric\Adapter\RemoteProxy\MetricFactory as RemoteFactory;
use Hyperf\Metric\Adapter\StatsD\MetricFactory as StatsDFactory;
use Hyperf\Metric\MetricFactoryPicker;
use Hyperf\Process\ProcessCollector;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Swoole\Process;

/**
 * @internal
 * @coversNothing
 */
final class MetricFactoryPickerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testPrometheus(): void
    {
        $config = new Config([
            'metric' => [
                'default' => 'prometheus',
                'use_standalone_process' => false,
                'enable_default_metrics' => true,
            ],
        ]);
        $container = Mockery::mock(Container::class);
        $container->expects('get')->with(ConfigInterface::class)->andReturns($config);
        $container->expects('get')->with(PrometheusFactory::class)->andReturns(Mockery::mock(PrometheusFactory::class));

        $picker = new MetricFactoryPicker();

        $this->assertInstanceOf(PrometheusFactory::class, $picker($container));
    }

    public function testStatsD(): void
    {
        $config = new Config([
            'metric' => [
                'default' => 'statsD',
                'use_standalone_process' => false,
                'enable_default_metrics' => true,
                'metric' => [
                    'prometheus' => [
                        'driver' => PrometheusFactory::class,
                    ],
                    'statsD' => [
                        'driver' => StatsDFactory::class,
                    ],
                ],
            ],
        ]);
        $container = Mockery::mock(Container::class);
        $container->expects('get')->with(ConfigInterface::class)->andReturn($config);
        $container->expects('get')->with(StatsDFactory::class)->andReturn(Mockery::mock(StatsDFactory::class));

        $picker = new MetricFactoryPicker();

        $this->assertInstanceOf(StatsDFactory::class, $picker($container));
    }

    public function testProxy(): void
    {
        $config = new Config([
            'metric' => [
                'default' => 'statsD',
                'use_standalone_process' => true,
                'enable_default_metrics' => true,
                'metric' => [
                    'prometheus' => [
                        'driver' => PrometheusFactory::class,
                    ],
                    'statsD' => [
                        'driver' => StatsDFactory::class,
                    ],
                ],
            ],
        ]);
        ProcessCollector::add('dummy', Mockery::mock(Process::class));
        $container = Mockery::mock(Container::class);
        $container->expects('get')->with(ConfigInterface::class)->andReturn($config);
        $container->expects('get')->with(RemoteFactory::class)->andReturn(Mockery::mock(RemoteFactory::class));

        $picker = new MetricFactoryPicker();

        $this->assertInstanceOf(RemoteFactory::class, $picker($container));
    }

    public function testMetricProcess(): void
    {
        $config = new Config([
            'metric' => [
                'default' => 'prometheus',
                'use_standalone_process' => true,
                'enable_default_metrics' => false,
                'metric' => [
                    'prometheus' => [
                        'driver' => PrometheusFactory::class,
                    ],
                    'statsD' => [
                        'driver' => StatsDFactory::class,
                    ],
                ],
            ],
        ]);
        ProcessCollector::add('dummy', Mockery::mock(Process::class));
        $container = Mockery::mock(Container::class);
        $container->expects('get')->with(ConfigInterface::class)->andReturn($config);
        $container->expects('get')->with(PrometheusFactory::class)->andReturn(Mockery::mock(PrometheusFactory::class));

        MetricFactoryPicker::$inMetricProcess = true;
        $picker = new MetricFactoryPicker();

        $this->assertInstanceOf(PrometheusFactory::class, $picker($container));
    }

    public function testNoOpDriver(): void
    {
        $config = new Config([
            'metric' => [
                'default' => 'noop',
                'metric' => [
                    'noop' => [
                        'driver' => NoOpFactory::class,
                    ],
                    'statsD' => [
                        'driver' => StatsDFactory::class,
                    ],
                ],
            ],
        ]);
        $container = Mockery::mock(Container::class);
        $container->expects('get')->with(ConfigInterface::class)->andReturn($config);
        $container->expects('get')->with(NoOpFactory::class)->andReturn(Mockery::mock(NoOpFactory::class));

        MetricFactoryPicker::$inMetricProcess = true;
        $picker = new MetricFactoryPicker();

        $this->assertInstanceOf(NoOpFactory::class, $picker($container));
    }
}
