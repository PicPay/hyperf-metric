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

/**
 * Histogram describes a metric that takes repeated observations of the same
 * kind of thing, and produces a statistical summary of those observations,
 * typically expressed as quantiles or buckets. An example of a histogram is
 * HTTP request latencies.
 */
interface HistogramInterface
{
    public function with(string ...$labelValues): static;

    public function put(float $sample): void;
}
