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

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Redis\Pool\PoolFactory;

/**
 * A simple mysql connection watcher served as an example.
 * This listener is not auto enabled.Tweak it to fit your
 * own need.
 */
class RedisPoolWatcher extends PoolWatcher implements ListenerInterface
{
    public function getPrefix()
    {
        return 'redis';
    }

    /**
     * Periodically scan metrics.
     */
    public function process(object $event): void
    {
        $config = $this->container->get(ConfigInterface::class);
        $poolNames = array_keys($config->get('redis', ['default' => []]));
        foreach ($poolNames as $poolName) {
            $workerId = $event->workerId;
            $pool = $this
                ->container
                ->get(PoolFactory::class)
                ->getPool($poolName);
            $this->watch($pool, $poolName, $workerId);
        }
    }
}
