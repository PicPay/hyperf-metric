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
 * Counter describes a metric that accumulates values monotonically.
 * An example of a counter is the number of received HTTP requests.
 */
interface CounterInterface
{
    public function with(string ...$labelValues): static;

    public function add(int $delta): void;
}
