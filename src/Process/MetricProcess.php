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
namespace Hyperf\Metric\Process;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Metric\Contract\MetricFactoryInterface;
use Hyperf\Metric\Event\MetricFactoryReady;
use Hyperf\Metric\MetricFactoryPicker;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Psr\EventDispatcher\EventDispatcherInterface;
use Swoole\Server;

/**
 * Metric Process.
 */
class MetricProcess extends AbstractProcess
{
    public $name = 'metric';

    public $nums = 1;

    protected MetricFactoryInterface $factory;

    public function isEnable($server): bool
    {
        $config = $this->container->get(ConfigInterface::class);
        return $server instanceof Server && $config->get('metric.use_standalone_process', true);
    }

    public function handle(): void
    {
        MetricFactoryPicker::$inMetricProcess = true;
        $this->factory = make(MetricFactoryInterface::class);
        $this
            ->container
            ->get(EventDispatcherInterface::class)
            ->dispatch(new MetricFactoryReady($this->factory));
        $this
            ->factory
            ->handle();
    }
}
