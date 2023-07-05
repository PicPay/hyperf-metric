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
namespace Hyperf\Metric\Listener;

use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Metric\Contract\MetricFactoryInterface;

class DbQueryExecutedMetricListener implements ListenerInterface
{
    public function listen(): array
    {
        return [QueryExecuted::class];
    }

    public function process(object $event): void
    {
        if (! $event instanceof QueryExecuted) {
            return;
        }

        $labels = [
            'system' => 'mysql',
            'operation' => $event->sql,
        ];

        $histogram = make(MetricFactoryInterface::class)
            ->makeHistogram('database_queries', array_keys($labels))
            ->with(...array_values($labels));

        $histogram->put($event->time);
    }
}
