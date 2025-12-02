<?php

use App\Models\Result;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

describe('prometheus metrics endpoint', function () {
    test('returns metrics in prometheus format when result exists', function () {
        $result = Result::factory()->create([
            'download' => 100000000,
            'upload' => 50000000,
            'ping' => 10.5,
        ]);

        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/metrics');

        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8')
            ->assertSee('speedtest_tracker_download_bytes')
            ->assertSee('speedtest_tracker_upload_bytes')
            ->assertSee('speedtest_tracker_download_bits')
            ->assertSee('speedtest_tracker_upload_bits')
            ->assertSee('speedtest_tracker_ping_ms');
    });

    test('returns all expected metric types', function () {
        $result = Result::factory()->create();

        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/metrics');

        $metrics = [
            'speedtest_tracker_download_bytes',
            'speedtest_tracker_upload_bytes',
            'speedtest_tracker_download_bits',
            'speedtest_tracker_upload_bits',
            'speedtest_tracker_ping_ms',
            'speedtest_tracker_ping_jitter_ms',
            'speedtest_tracker_ping_low_ms',
            'speedtest_tracker_ping_high_ms',
            'speedtest_tracker_download_latency_iqm_ms',
            'speedtest_tracker_download_latency_low_ms',
            'speedtest_tracker_download_latency_high_ms',
            'speedtest_tracker_download_jitter_ms',
            'speedtest_tracker_upload_latency_iqm_ms',
            'speedtest_tracker_upload_latency_low_ms',
            'speedtest_tracker_upload_latency_high_ms',
            'speedtest_tracker_upload_jitter_ms',
            'speedtest_tracker_packet_loss_percent',
            'speedtest_tracker_downloaded_bytes',
            'speedtest_tracker_uploaded_bytes',
            'speedtest_tracker_download_elapsed_ms',
            'speedtest_tracker_upload_elapsed_ms',
        ];

        $response->assertSuccessful();

        foreach ($metrics as $metric) {
            $response->assertSee($metric);
        }
    });

    test('includes all expected labels', function () {
        $result = Result::factory()->create();

        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/metrics');

        $labels = [
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

        $response->assertSuccessful();

        foreach ($labels as $label) {
            $response->assertSee($label);
        }
    });

    test('returns empty metrics when no result is cached', function () {
        $response = $this->get('/metrics');

        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    });

    test('returns empty metrics when cached result does not exist', function () {
        Cache::forever('prometheus:latest_result', 999999);

        $response = $this->get('/metrics');

        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    });

    test('converts bytes to bits correctly', function () {
        $result = Result::factory()->create([
            'download' => 125000000,
            'upload' => 62500000,
        ]);

        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/metrics');

        $response->assertSuccessful()
            ->assertSee('speedtest_tracker_download_bits')
            ->assertSee('speedtest_tracker_upload_bits');
    });

    test('handles scheduled and healthy flags correctly', function () {
        $result = Result::factory()->create([
            'scheduled' => true,
            'healthy' => true,
        ]);

        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/metrics');

        $response->assertSuccessful()
            ->assertSee('scheduled="true"')
            ->assertSee('healthy="true"');
    });

    test('handles non-scheduled and unhealthy flags correctly', function () {
        $result = Result::factory()->create([
            'scheduled' => false,
            'healthy' => false,
        ]);

        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/metrics');

        $response->assertSuccessful()
            ->assertSee('scheduled="false"')
            ->assertSee('healthy="false"');
    });

    test('includes result status in metrics', function () {
        $result = Result::factory()->create();

        Cache::forever('prometheus:latest_result', $result->id);

        $response = $this->get('/metrics');

        $response->assertSuccessful()
            ->assertSee('status="'.$result->status->value.'"');
    });

    test('updates metrics when cache is updated with new result', function () {
        $firstResult = Result::factory()->create([
            'download' => 100000000,
        ]);

        Cache::forever('prometheus:latest_result', $firstResult->id);

        $firstResponse = $this->get('/metrics');
        $firstResponse->assertSuccessful();

        $secondResult = Result::factory()->create([
            'download' => 200000000,
        ]);

        Cache::forever('prometheus:latest_result', $secondResult->id);

        $secondResponse = $this->get('/metrics');
        $secondResponse->assertSuccessful();
    });
});
