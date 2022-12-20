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
namespace Hyperf\Metric\Adapter\NoOp;

use Hyperf\Metric\Contract\HistogramInterface;

class Histogram implements HistogramInterface
{
    public function with(string ...$labelValues): static
    {
        return $this;
    }

    public function put(float $sample): void
    {
    }
}
