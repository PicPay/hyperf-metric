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
namespace Hyperf\Metric\Event;

use Hyperf\Metric\Contract\MetricFactoryInterface;

class MetricFactoryReady
{
    /**
     * @param MetricFactoryInterface $factory a ready to use factory
     */
    public function __construct(public MetricFactoryInterface $factory)
    {
    }
}
