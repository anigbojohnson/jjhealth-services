<?php

namespace App\Services;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\Context\Context;

class TracingService
{
    public function __construct(private TracerInterface $tracer) {}

    public function startSpan(string $name, array $attributes = []): SpanInterface
    {
        $span = $this->tracer->spanBuilder($name)->startSpan();

        foreach ($attributes as $key => $value) {
            $span->setAttribute($key, $value);
        }

        return $span;
    }

    public function trace(string $name, callable $callback, array $attributes = []): mixed
    {
        $span = $this->startSpan($name, $attributes);
        $scope = $span->activate();

        try {
            $result = $callback($span);
            return $result;
        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(\OpenTelemetry\API\Trace\StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;
        } finally {
            $scope->detach();
            $span->end();
        }
    }
}