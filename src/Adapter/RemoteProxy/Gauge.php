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
namespace Hyperf\Metric\Adapter\RemoteProxy;

use Hyperf\Metric\Contract\GaugeInterface;
use Hyperf\Process\ProcessCollector;

class Gauge implements GaugeInterface
{
    /**
     * @var string
     */
    protected const TARGET_PROCESS_NAME = 'metric';

    /**
     * @var string[]
     */
    public $labelValues = [];

    /**
     * @var null|float
     */
    public $delta;

    /**
     * @var null|float
     */
    public $value;

    public function __construct(public string $name, public array $labelNames)
    {
    }

    public function with(string ...$labelValues): static
    {
        $this->labelValues = $labelValues;
        return $this;
    }

    public function set(float $value): void
    {
        $this->value = $value;
        $this->delta = null;
        $process = ProcessCollector::get(static::TARGET_PROCESS_NAME)[0];
        $process->write(serialize($this));
    }

    public function add(float $delta): void
    {
        $this->delta = $delta;
        $this->value = null;
        $process = ProcessCollector::get(static::TARGET_PROCESS_NAME)[0];
        $process->write(serialize($this));
    }
}
