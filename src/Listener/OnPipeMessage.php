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
namespace Hyperf\Metric\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Metric\Adapter\RemoteProxy\Counter;
use Hyperf\Metric\Adapter\RemoteProxy\Gauge;
use Hyperf\Metric\Adapter\RemoteProxy\Histogram;
use Hyperf\Metric\Contract\MetricFactoryInterface;
use Hyperf\Process\Event\PipeMessage;

/**
 * Receives messages in metric process.
 */
class OnPipeMessage implements ListenerInterface
{
    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            PipeMessage::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event): void
    {
        $factory = make(MetricFactoryInterface::class);
        if (property_exists($event, 'data') && $event instanceof PipeMessage) {
            $inner = $event->data;
            switch (true) {
                case $inner instanceof Counter:
                    $counter = $factory->makeCounter($inner->name, $inner->labelNames);
                    $counter->with(...$inner->labelValues)->add($inner->delta);
                    break;
                case $inner instanceof Gauge:
                    $gauge = $factory->makeGauge($inner->name, $inner->labelNames);
                    if (isset($inner->value)) {
                        $gauge->with(...$inner->labelValues)->set($inner->value);
                    } else {
                        $gauge->with(...$inner->labelValues)->add($inner->delta);
                    }
                    break;
                case $inner instanceof Histogram:
                    $histogram = $factory->makeHistogram($inner->name, $inner->labelNames);
                    $histogram->with(...$inner->labelValues)->put($inner->sample);
                    break;
                default:
                    // Nothing to do
                    break;
            }
        }
    }
}
