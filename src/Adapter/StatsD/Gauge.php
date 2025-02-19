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
namespace Hyperf\Metric\Adapter\StatsD;

use Domnikl\Statsd\Client;
use Hyperf\Metric\Contract\GaugeInterface;

class Gauge implements GaugeInterface
{
    /**
     * @var string[]
     */
    protected array $labelValues = [];

    /**
     * @param string[] $labelNames
     */
    public function __construct(
        protected Client $client,
        protected string $name,
        protected float $sampleRate,
        protected array $labelNames
    ) {
    }

    public function with(string ...$labelValues): static
    {
        $this->labelValues = array_merge($this->labelValues, $labelValues);
        return $this;
    }

    public function set(float $value): void
    {
        if ($value < 0) {
            // StatsD gauge doesn't support negative values.
            $value = 0;
        }
        $this->client->gauge($this->name, (string) $value, array_combine($this->labelNames, $this->labelValues));
    }

    public function add(float $delta): void
    {
        if ($delta >= 0) {
            $deltaStr = '+' . $delta;
        } else {
            $deltaStr = (string) $delta;
        }
        $this->client->gauge($this->name, $deltaStr, array_combine($this->labelNames, $this->labelValues));
    }
}
