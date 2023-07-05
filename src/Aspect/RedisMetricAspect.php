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
namespace PicPay\Hyperf\Commons\Observability\Metrics\Aspect;

use Hyperf\Di\Aop\AroundInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Metric\Timer;
use Hyperf\Redis\Redis;

class RedisMetricAspect implements AroundInterface
{
    public array $classes = [Redis::class . '::__call'];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $arguments = $proceedingJoinPoint->arguments['keys'];

        $timer = new Timer('database_queries', [
            'system' => 'redis',
            'operation' => sprintf('Redis %s', $arguments['name']),
        ]);

        $result = $proceedingJoinPoint->process();
        $timer->end();

        return $result;
    }
}
