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
namespace Hyperf\Metric\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Metric\Annotation\Counter;
use Hyperf\Metric\Contract\MetricFactoryInterface;

class CounterAnnotationAspect extends AbstractAspect
{
    public array $classes = [];

    public array $annotations = [
        Counter::class,
    ];

    public function __construct(private MetricFactoryInterface $factory)
    {
    }

    /**
     * @return mixed return the value from process method of ProceedingJoinPoint, or the value that you handled
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $metadata = $proceedingJoinPoint->getAnnotationMetadata();
        $source = $this->fromCamelCase($proceedingJoinPoint->className . '::' . $proceedingJoinPoint->methodName);
        /** @var Counter $annotation */
        if ($annotation = $metadata->method[Counter::class] ?? null) {
            $name = $annotation->name ?: $source;
        } else {
            $name = $source;
        }
        $counter = $this->factory->makeCounter($name, ['class', 'method']);
        $result = $proceedingJoinPoint->process();
        $counter
            ->with(
                $proceedingJoinPoint->className,
                $proceedingJoinPoint->methodName
            )
            ->add(1);
        return $result;
    }

    private function fromCamelCase(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}
