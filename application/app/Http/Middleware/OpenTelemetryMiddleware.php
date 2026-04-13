<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\API\Trace\StatusCode;
use App\Services\MetricsService;


class OpenTelemetryMiddleware
{
    public function __construct(private TracerInterface $tracer,
     private MetricsService $metrics) {}

    public function handle(Request $request, Closure $next): mixed
    {        

        $startTime = microtime(true);  // ← start timer

        $span = $this->tracer
            ->spanBuilder($request->method() . ' ' . $request->path())
            ->startSpan();

        $span->setAttribute('http.method', $request->method());
        $span->setAttribute('http.url', $request->fullUrl());
        $span->setAttribute('http.route', $request->route()?->uri() ?? 'unknown');
        $span->setAttribute('http.user_agent', $request->userAgent());

        $scope = $span->activate();

        try {
            $response = $next($request);

            $span->setAttribute('http.status_code', $response->getStatusCode());

            if ($response->getStatusCode() >= 400) {
                $span->setStatus(StatusCode::STATUS_ERROR);
            }

            return $response;
        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;
        } finally {

            // record how long this request took as a metric
            $duration = (microtime(true) - $startTime) * 1000;
            $this->metrics->recordRequestDuration(
                $duration,
                $request->route()?->uri() ?? 'unknown'
            );
            $scope->detach();
            $span->end();
        }
    }
    }