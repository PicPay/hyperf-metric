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
use Hyperf\Metric\Contract\CounterInterface;

class Counter implements CounterInterface
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

    public function add(int $delta): void
    {
        $this->client->count($this->name, $delta, $this->sampleRate, array_combine($this->labelNames, $this->labelValues));
    }
}
