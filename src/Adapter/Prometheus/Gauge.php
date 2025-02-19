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
namespace Hyperf\Metric\Adapter\Prometheus;

use Hyperf\Metric\Contract\GaugeInterface;

class Gauge implements GaugeInterface
{
    protected \Prometheus\Gauge $gauge;

    /**
     * @var string[]
     */
    protected array $labelValues = [];

    public function __construct(protected \Prometheus\CollectorRegistry $registry, string $namespace, string $name, string $help, array $labelNames)
    {
        $this->gauge = $registry->getOrRegisterGauge($namespace, $name, $help, $labelNames);
    }

    public function with(string ...$labelValues): static
    {
        $this->labelValues = $labelValues;
        return $this;
    }

    public function set(float $value): void
    {
        $this->gauge->set($value, $this->labelValues);
    }

    public function add(float $delta): void
    {
        $this->gauge->incBy($delta, $this->labelValues);
    }
}
