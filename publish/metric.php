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
return [
    // To disable hyperf/metric temporarily, set default driver to noop.
    'default' => env('METRIC_DRIVER', 'statsd'),
    'use_standalone_process' => env('METRIC_USE_STANDALONE_PROCESS', false),
    'enable_default_metric' => env('METRIC_ENABLE_DEFAULT_METRIC', true),
    'default_metric_interval' => env('DEFAULT_METRIC_INTERVAL', 5),
    'metric' => [
        'statsd' => [
            'driver' => Hyperf\Metric\Adapter\StatsD\MetricFactory::class,
            'namespace' => env('APP_NAME', 'skeleton'),
            'udp_host' => env('STATSD_UDP_HOST', '127.0.0.1'),
            'udp_port' => env('STATSD_UDP_PORT', '8125'),
            'timeout' => env('STATSD_CONNECTION_TIMEOUT', null),
            'persistent' => env('STATSD_CONNECTION_PERSISTENT', false),
            'enable_batch' => env('STATSD_ENABLE_BATCH', true),
            'push_interval' => env('STATSD_PUSH_INTERVAL', 5),
            'sample_rate' => env('STATSD_SAMPLE_RATE', 1.0),
        ],
        'noop' => [
            'driver' => Hyperf\Metric\Adapter\NoOp\MetricFactory::class,
        ],
    ],
];
