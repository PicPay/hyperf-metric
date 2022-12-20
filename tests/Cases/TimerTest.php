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

use Hyperf\Di\Container;
use Hyperf\Metric\Contract\HistogramInterface;
use Hyperf\Metric\Contract\MetricFactoryInterface;
use Hyperf\Metric\Timer;
use Hyperf\Utils\ApplicationContext;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class TimerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testEnd()
    {
        $this->mockContainer();
        $timer = new Timer('test');
        $timer->end();
        $this->assertTrue(true);
    }

    public function testEndCalledTwice()
    {
        $this->mockContainer();
        $timer2 = new Timer('test');
        $timer2->end();
        $timer2->end();
        $this->assertTrue(true);
    }

    public function testEndNotCalled()
    {
        $this->mockContainer();
        $timer3 = new Timer('test');
        unset($timer3);
        $this->assertTrue(true);
    }

    private function mockContainer()
    {
        $container = Mockery::mock(Container::class);
        $container->shouldReceive('make')->with(MetricFactoryInterface::class, [])->andReturn(new class() {
            public function makeHistogram($name, $labels)
            {
                $histogram = Mockery::mock(HistogramInterface::class);
                $histogram->shouldReceive('with')->andReturn($histogram);
                $histogram->shouldReceive('put')->once();
                return $histogram;
            }
        });
        ApplicationContext::setContainer($container);
    }
}
