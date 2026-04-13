<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SemConv\ResourceAttributes;
use OpenTelemetry\API\Common\Time\SystemClock;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MeterProviderBuilder;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingMetricReader;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;


use App\Services\MetricsService;              
use OpenTelemetry\API\Metrics\MeterInterface;

class OpenTelemetryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 1. shared resource
        $this->app->singleton('otel.resource', function () {
            $defaultResource = ResourceInfoFactory::defaultResource();
            $customResource  = ResourceInfo::create(Attributes::create([
                ResourceAttributes::SERVICE_NAME    => config('app.name'),
                ResourceAttributes::SERVICE_VERSION => config('app.version', '1.0.0'),
            ]));
            return $defaultResource->merge($customResource);
        });

        // 2. tracer provider
        $this->app->singleton(TracerProvider::class, function ($app) {
            $transport = (new OtlpHttpTransportFactory())->create(
                config('otel.endpoint', env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4318')) . '/v1/traces',
                'application/x-protobuf'
            );

            return new TracerProvider(
                new BatchSpanProcessor(new SpanExporter($transport),SystemClock::create()),
                null,
                $app->make('otel.resource')
            );
        });

        // 3. tracer
        $this->app->singleton(TracerInterface::class, function ($app) {
            return $app->make(TracerProvider::class)
                ->getTracer(config('app.name'), config('app.version', '1.0.0'));
        });

        // 4. meter provider
        $this->app->singleton(MeterProvider::class, function ($app) {
            $transport = (new OtlpHttpTransportFactory())->create(
                config('otel.endpoint', env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4318')),
                'application/json'
            );
            $exporter = new MetricExporter($transport);
            $reader   = new ExportingReader($exporter, SystemClock::create());


            return MeterProvider::builder()
                ->setResource($app->make('otel.resource'))
                ->addReader($reader)
                ->build();
        });

        // 5. meter
        $this->app->singleton(MeterInterface::class, function ($app) {
            return $app->make(MeterProvider::class)
                ->getMeter(config('app.name'), config('app.version', '1.0.0'));
        });

        // 6. metrics service  ← THIS WAS MISSING
        $this->app->singleton(MetricsService::class, function ($app) {
            return new MetricsService($app->make(MeterInterface::class));
        });


    }

    public function boot(): void
    {
            // force flush metrics after every request
    app()->terminating(function () {
        $meterProvider = app(MeterProvider::class);
        $meterProvider->forceFlush();
    });
    }
}