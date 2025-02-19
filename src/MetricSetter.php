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
namespace Hyperf\Metric;

use Hyperf\Metric\Contract\GaugeInterface;
use Hyperf\Retry\Retry;
use Hyperf\Utils\Coroutine;

use function array_values;
use function str_replace;

/**
 * A Helper trait to set stats from swoole and kernal.
 */
trait MetricSetter
{
    /**
     * Try to set every stats available to the gauge.
     * Some stats might be missing depending
     * on the platform.
     */
    private function trySet(string $prefix, array $metrics, array $stats): void
    {
        foreach (array_keys($stats) as $key) {
            $metricsKey = str_replace('.', '_', $prefix . $key);
            if (array_key_exists($metricsKey, $metrics)) {
                $metrics[$metricsKey]->set($stats[$key]);
            }
        }
    }

    /**
     * Create an array of gauges.
     * @param array<string, string> $labels
     * @return GaugeInterface[]
     */
    private function factoryMetrics(array $labels, string ...$names): array
    {
        $out = [];
        foreach ($names as $name) {
            $out[$name] = $this
                ->factory
                ->makeGauge($name, \array_keys($labels))
                ->with(...array_values($labels));
        }
        return $out;
    }

    /**
     * Spawn a new coroutine to handle metrics.
     */
    private function spawnHandle()
    {
        Coroutine::create(function () {
            if (class_exists(Retry::class)) {
                Retry::whenThrows()->backoff(100)->call(function () {
                    $this->factory->handle();
                });
            } else {
                retry(PHP_INT_MAX, function () {
                    $this->factory->handle();
                }, 100);
            }
        });
    }
}
