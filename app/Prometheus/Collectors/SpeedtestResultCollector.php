<?php

namespace App\Prometheus\Collectors;

use App\Models\Result;
use Illuminate\Support\Facades\Cache;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class SpeedtestResultCollector implements Collector
{
    public function register(): void
    {
        $this->registerSpeedMetrics();
        $this->registerLatencyMetrics();
        $this->registerDownloadLatencyMetrics();
        $this->registerUploadLatencyMetrics();
        $this->registerQualityMetrics();
        $this->registerVolumeMetrics();
        $this->registerDurationMetrics();
    }

    protected function registerSpeedMetrics(): void
    {
        Prometheus::addGauge('Download speed in bytes per second')
            ->name('download_bytes')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('download'));

        Prometheus::addGauge('Upload speed in bytes per second')
            ->name('upload_bytes')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('upload'));

        Prometheus::addGauge('Download speed in bits per second')
            ->name('download_bits')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('download', fn ($val) => toBits($val)));

        Prometheus::addGauge('Upload speed in bits per second')
            ->name('upload_bits')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('upload', fn ($val) => toBits($val)));
    }

    protected function registerLatencyMetrics(): void
    {
        Prometheus::addGauge('Ping latency in milliseconds')
            ->name('ping_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('ping'));

        Prometheus::addGauge('Ping jitter in milliseconds')
            ->name('ping_jitter_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('ping_jitter'));

        Prometheus::addGauge('Ping low latency in milliseconds')
            ->name('ping_low_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('ping_low'));

        Prometheus::addGauge('Ping high latency in milliseconds')
            ->name('ping_high_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('ping_high'));
    }

    protected function registerDownloadLatencyMetrics(): void
    {
        Prometheus::addGauge('Download latency interquartile mean in milliseconds')
            ->name('download_latency_iqm_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('downloadlatencyiqm'));

        Prometheus::addGauge('Download latency low in milliseconds')
            ->name('download_latency_low_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('downloadlatency_low'));

        Prometheus::addGauge('Download latency high in milliseconds')
            ->name('download_latency_high_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('downloadlatency_high'));

        Prometheus::addGauge('Download jitter in milliseconds')
            ->name('download_jitter_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('download_jitter'));
    }

    protected function registerUploadLatencyMetrics(): void
    {
        Prometheus::addGauge('Upload latency interquartile mean in milliseconds')
            ->name('upload_latency_iqm_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('uploadlatencyiqm'));

        Prometheus::addGauge('Upload latency low in milliseconds')
            ->name('upload_latency_low_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('uploadlatency_low'));

        Prometheus::addGauge('Upload latency high in milliseconds')
            ->name('upload_latency_high_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('uploadlatency_high'));

        Prometheus::addGauge('Upload jitter in milliseconds')
            ->name('upload_jitter_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('upload_jitter'));
    }

    protected function registerQualityMetrics(): void
    {
        Prometheus::addGauge('Packet loss percentage')
            ->name('packet_loss_percent')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('packet_loss'));
    }

    protected function registerVolumeMetrics(): void
    {
        Prometheus::addGauge('Total bytes downloaded during test')
            ->name('downloaded_bytes')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('downloaded_bytes'));

        Prometheus::addGauge('Total bytes uploaded during test')
            ->name('uploaded_bytes')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('uploaded_bytes'));
    }

    protected function registerDurationMetrics(): void
    {
        Prometheus::addGauge('Download test duration in milliseconds')
            ->name('download_elapsed_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('download_elapsed'));

        Prometheus::addGauge('Upload test duration in milliseconds')
            ->name('upload_elapsed_ms')
            ->labels($this->getLabelNames())
            ->value(fn () => $this->collectMetric('upload_elapsed'));
    }

    protected function collectMetric(string $attribute, ?callable $transform = null): array
    {
        $result = $this->getLatestResult();

        if (! $result) {
            return [];
        }

        $value = $result->{$attribute} ?? 0;

        if ($transform) {
            $value = $transform($value);
        }

        return [[$value, $this->buildLabelValues($result)]];
    }

    protected function getLatestResult(): ?Result
    {
        $resultId = Cache::get('prometheus:latest_result');

        if (! $resultId) {
            return null;
        }

        return Result::find($resultId);
    }

    protected function getLabelNames(): array
    {
        return [
            'server_id',
            'server_name',
            'server_country',
            'server_location',
            'isp',
            'scheduled',
            'healthy',
            'status',
            'app_name',
        ];
    }

    protected function buildLabelValues(Result $result): array
    {
        return [
            (string) ($result->server_id ?? ''),
            $result->server_name ?? '',
            $result->server_country ?? '',
            $result->server_location ?? '',
            $result->isp ?? '',
            $result->scheduled ? 'true' : 'false',
            $result->healthy ? 'true' : 'false',
            $result->status->value,
            config('app.name'),
        ];
    }
}
