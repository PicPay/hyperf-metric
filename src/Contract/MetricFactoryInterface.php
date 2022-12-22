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
namespace Hyperf\Metric\Contract;

interface MetricFactoryInterface
{
    /**
     * Create a Counter.
     * @param string $name name of the metric
     * @param string[] $labelNames key of your label kvs
     */
    public function makeCounter(string $name, ?array $labelNames = []): CounterInterface;

    /**
     * Create a Gauge.
     * @param string $name name of the metric
     * @param string[] $labelNames key of your label kvs
     */
    public function makeGauge(string $name, ?array $labelNames = []): GaugeInterface;

    /**
     * Create a HistogramInterface.
     * @param string $name name of the metric
     * @param string[] $labelNames key of your label kvs
     */
    public function makeHistogram(string $name, ?array $labelNames = []): HistogramInterface;

    /**
     * Handle the metric collecting/reporting/serving tasks.
     */
    public function handle(): void;
}
