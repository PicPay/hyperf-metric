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
 * Gauge describes a metric that takes specific values over time.
 * An example of a gauge is the current depth of a job queue.
 */
interface GaugeInterface
{
    public function with(string ...$labelValues): static;

    public function set(float $value): void;

    public function add(float $delta): void;
}
