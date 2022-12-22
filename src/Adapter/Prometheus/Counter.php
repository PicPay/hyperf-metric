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

use Hyperf\Metric\Contract\CounterInterface;
use Prometheus\CollectorRegistry;

class Counter implements CounterInterface
{
    protected \Prometheus\Counter $counter;

    /**
     * @var string[]
     */
    protected array $labelValues = [];

    public function __construct(protected CollectorRegistry $registry, string $namespace, string $name, string $help, array $labelNames)
    {
        $this->counter = $registry->getOrRegisterCounter($namespace, $name, $help, $labelNames);
    }

    public function with(string ...$labelValues): static
    {
        $this->labelValues = $labelValues;
        return $this;
    }

    public function add(int $delta): void
    {
        $this->counter->incBy($delta, $this->labelValues);
    }
}
