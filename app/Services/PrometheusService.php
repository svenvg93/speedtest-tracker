<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

class PrometheusService
{
    protected CollectorRegistry $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Populate gauges from payload.
     *
     * @param  array|object  $data
     */
    public function updateMetricsFromPayload($data): void
    {
        // Dynamically register metrics when first updating
        $definitions = [
            'ping_jitter' => 'Ping jitter (ms)',
            'ping_latency' => 'Ping latency (ms)',
            'ping_low' => 'Ping low value (ms)',
            'ping_high' => 'Ping high value (ms)',
            'download_bandwidth' => 'Download bandwidth (bytes/sec)',
            'download_bytes' => 'Download total bytes',
            'download_elapsed' => 'Download elapsed time (ms)',
            'download_latency_iqm' => 'Download latency IQM (ms)',
            'download_latency_low' => 'Download latency low (ms)',
            'download_latency_high' => 'Download latency high (ms)',
            'download_latency_jitter' => 'Download latency jitter (ms)',
            'upload_bandwidth' => 'Upload bandwidth (bytes/sec)',
            'upload_bytes' => 'Upload total bytes',
            'upload_elapsed' => 'Upload elapsed time (ms)',
            'upload_latency_iqm' => 'Upload latency IQM (ms)',
            'upload_latency_low' => 'Upload latency low (ms)',
            'upload_latency_high' => 'Upload latency high (ms)',
            'upload_latency_jitter' => 'Upload latency jitter (ms)',
            'packet_loss' => 'Packet loss percentage',
        ];
        $labelNames = [
            'service', 'id', 'server_id', 'server_name', 'isp',
            'country', 'location', 'externalIp',
            'scheduled', 'healthy', 'status', 'app',
        ];
        foreach ($definitions as $name => $help) {
            // Register only if not already defined
            try {
                $this->registry->getGauge('speedtest', $name);
            } catch (\Exception $e) {
                $this->registry->registerGauge('speedtest', $name, $help, $labelNames);
            }
        }

        // Handle payload stored in a model array with JSON in 'data'
        $modelData = $data;
        $payload = $data;
        if (isset($modelData['data'])) {
            $payload = is_string($modelData['data'])
                ? json_decode($modelData['data'], true)
                : $modelData['data'];
        }

        if (is_object($payload)) {
            $payload = json_decode(json_encode($payload), true);
        }

        $labels = [
            $modelData['service'] ?? 'unknown',
            $modelData['id'] ?? 'unknown',
            $payload['server']['id'] ?? 'unknown',
            $payload['server']['name'] ?? 'unknown',
            $payload['isp'] ?? 'unknown',
            $payload['server']['country'] ?? 'unknown',
            $payload['server']['location'] ?? 'unknown',
            $payload['interface']['externalIp'] ?? 'unknown',
            ! empty($modelData['scheduled']) ? 'true' : 'false',
            ! empty($modelData['healthy']) ? 'true' : 'false',
            $modelData['status'] ?? 'unknown',
            config('app.name'),
        ];

        // Ping metrics
        $this->registry->getGauge('speedtest', 'ping_jitter')
            ->set($payload['ping']['jitter'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'ping_latency')
            ->set($payload['ping']['latency'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'ping_low')
            ->set($payload['ping']['low'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'ping_high')
            ->set($payload['ping']['high'] ?? 0.0, $labels);

        // Download metrics
        $this->registry->getGauge('speedtest', 'download_bandwidth')
            ->set($payload['download']['bandwidth'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'download_bytes')
            ->set($payload['download']['bytes'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'download_elapsed')
            ->set($payload['download']['elapsed'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'download_latency_iqm')
            ->set($payload['download']['latency']['iqm'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'download_latency_low')
            ->set($payload['download']['latency']['low'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'download_latency_high')
            ->set($payload['download']['latency']['high'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'download_latency_jitter')
            ->set($payload['download']['latency']['jitter'] ?? 0.0, $labels);

        // Upload metrics
        $this->registry->getGauge('speedtest', 'upload_bandwidth')
            ->set($payload['upload']['bandwidth'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'upload_bytes')
            ->set($payload['upload']['bytes'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'upload_elapsed')
            ->set($payload['upload']['elapsed'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'upload_latency_iqm')
            ->set($payload['upload']['latency']['iqm'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'upload_latency_low')
            ->set($payload['upload']['latency']['low'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'upload_latency_high')
            ->set($payload['upload']['latency']['high'] ?? 0.0, $labels);
        $this->registry->getGauge('speedtest', 'upload_latency_jitter')
            ->set($payload['upload']['latency']['jitter'] ?? 0.0, $labels);

        // Packet loss
        $this->registry->getGauge('speedtest', 'packet_loss')
            ->set($payload['packet_loss'] ?? $payload['packetLoss'] ?? 0.0, $labels);
    }

    /**
     * Render Prometheus text format.
     */
    public function renderMetrics(): string
    {
        $renderer = new RenderTextFormat;

        return $renderer->render($this->registry->getMetricFamilySamples());
    }
}
