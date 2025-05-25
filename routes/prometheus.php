<?php

use App\Services\PrometheusService;
use App\Settings\DataIntegrationSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Prometheus\RenderTextFormat;

Route::get('/metrics', function (PrometheusService $svc) {

    $settings = app(DataIntegrationSettings::class);
    if (! $settings->prometheus_enabled) {
        abort(404);
    }

    // Load the last stored payload
    $payload = Cache::get('speedtest.last_payload');
    if ($payload) {
        $svc->updateMetricsFromPayload($payload);
    }

    return response(
        $svc->renderMetrics(),
        200,
        ['Content-Type' => RenderTextFormat::MIME_TYPE]
    );
});
