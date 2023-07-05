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
namespace Hyperf\Metric\Support;

final class Uri
{
    public static function sanitize(string $uri): string
    {
        return preg_replace(
            [
                '/\/(?<=\/)([a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12})(?=\/)?/',
                '/\/(?<=\/)\d+(?=\/)?/',
            ],
            [
                '/<UUID>',
                '/<NUMBER>',
            ],
            $uri
        );
    }
}
