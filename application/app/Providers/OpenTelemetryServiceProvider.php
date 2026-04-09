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
class OpenTelemetryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TracerProvider::class, function () {
            $resource = ResourceInfoFactory::merge(
                ResourceInfo::create(Attributes::create([
                    ResourceAttributes::SERVICE_NAME => config('app.name'),
                    ResourceAttributes::SERVICE_VERSION => config('app.version', '1.0.0'),
                    ResourceAttributes::DEPLOYMENT_ENVIRONMENT => config('app.env'),
                ])),
                ResourceInfoFactory::defaultResource()
            );

            $transport = (new OtlpHttpTransportFactory())->create(
                config('otel.endpoint', env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4318')) . '/v1/traces',
                'application/x-protobuf'
            );

            $exporter = new SpanExporter($transport);

            $tracerProvider = new TracerProvider(
                new BatchSpanProcessor($exporter),
                null,
                $resource
            );

            return $tracerProvider;
        });

        $this->app->singleton(TracerInterface::class, function ($app) {
            return $app->make(TracerProvider::class)
                ->getTracer(config('app.name'), config('app.version', '1.0.0'));
        });
    }

    public function boot(): void
    {
        //
    }
}