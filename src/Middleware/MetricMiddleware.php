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
namespace Hyperf\Metric\Middleware;

use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Metric\Timer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class MetricMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $labels = [
            'request_status' => '500',
            'request_path' => $this->getPath($request),
            'request_method' => $request->getMethod(),
        ];

        $timer = new Timer('http_requests', $labels);

        try {
            $response = $handler->handle($request);
            $labels['request_status'] = (string) $response->getStatusCode();
            $timer->end($labels);

            return $response;
        } catch (Throwable $exception) {
            $this->countException($request, $exception);
            $timer->end($labels);

            throw $exception;
        }
    }

    protected function getPath(ServerRequestInterface $request): string
    {
        $dispatched = $request->getAttribute(Dispatched::class);
        if (! $dispatched) {
            return $request->getUri()->getPath();
        }
        if (! $dispatched->handler) {
            return 'not_found';
        }
        return $dispatched->handler->route;
    }

    protected function countException(ServerRequestInterface $request, Throwable $exception): void
    {
        $labels = [
            'request_path' => $this->getPath($request),
            'request_method' => $request->getMethod(),
            'class' => $exception::class,
            'message' => $exception->getMessage(),
            'code' => (string) $exception->getCode(),
            'line' => (string) $exception->getLine(),
        ];

        Metric::count('exception_count', 1, $labels);
    }
}
