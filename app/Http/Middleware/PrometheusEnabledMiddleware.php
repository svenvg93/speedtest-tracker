<?php

namespace App\Http\Middleware;

use App\Settings\DataIntegrationSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrometheusEnabledMiddleware
{
    public function __construct(
        protected DataIntegrationSettings $settings
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->settings->prometheus_enabled) {
            abort(404);
        }

        return $next($request);
    }
}
