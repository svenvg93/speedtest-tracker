# Prometheus

After each test, Speedtest Tracker exposes the metrics for Prometheus to scrape. For long term storage or custom visualizations.

### Allowed IPs

You can configure the Prometheus endpoint so it’s only accessible from specific IP addresses or networks. This can include single IPs or entire CIDR ranges.

![](../../assets/images/prometheus-settings.png)

### Grafana Dashboard

You can use this community made Grafana Dashboard to visualize your data.

[CrazyWolf13/Speedtest-Tracker-Prometheus](https://github.com/CrazyWolf13/Speedtest-Tracker-Prometheus)

### Data pattern

Speedtest Tracker exports data in two categories: labels and metrics. Labels are used for filtering, while metrics are used for displaying data.

!!! info "Only the latest result is exported"

    The metrics are rebuilt when a speedtest completes or fails, and cached until the next test. Each sample carries the timestamp of the result, not the time of the scrape.

    For a failed test only `speedtest_tracker_info` is exported, with `status="failed"`. The speed and latency metrics are left out.

#### Labels

These labels are added to `speedtest_tracker_info` and every result metric.

| Label | Description |
| --- | --- |
| `app_name` | Value of [`APP_NAME`](../../installation/environment-variables.md#application). |
| `isp` | Your internet service provider. |
| `server_name` | Name of the speedtest server. |
| `server_location` | Location of the speedtest server. |
| `scheduled` | `true` for a scheduled test, `false` for a manual test. |
| `healthy` | `true` when the result passed the [thresholds](../../installation/environment-variables.md#speed-tests), otherwise `false`. |
| `status` | `completed` or `failed`. |

#### Metrics

All metrics are gauges.

| Metric | Unit | Description |
| --- | --- | --- |
| `speedtest_tracker_up` | | Always `1`, the endpoint is responding. Has no labels. |
| `speedtest_tracker_build_info` | | Always `1`, with the application version in the `version` label. |
| `speedtest_tracker_info` | | Always `1`, carries the labels of the latest result. Also exported for failed tests. |
| `speedtest_tracker_download_bytes_per_second` | bytes/s | Download speed. |
| `speedtest_tracker_upload_bytes_per_second` | bytes/s | Upload speed. |
| `speedtest_tracker_download_bits_per_second` | bits/s | Download speed. |
| `speedtest_tracker_upload_bits_per_second` | bits/s | Upload speed. |
| `speedtest_tracker_ping_ms` | ms | Ping latency. |
| `speedtest_tracker_ping_low_ms` | ms | Lowest ping latency. |
| `speedtest_tracker_ping_high_ms` | ms | Highest ping latency. |
| `speedtest_tracker_ping_jitter_ms` | ms | Ping jitter. |
| `speedtest_tracker_download_jitter_ms` | ms | Download jitter. |
| `speedtest_tracker_upload_jitter_ms` | ms | Upload jitter. |
| `speedtest_tracker_packet_loss_percent` | % | Packet loss. |
| `speedtest_tracker_download_latency_iqm_ms` | ms | Download latency, interquartile mean. :lucide-info:{ title="The mean of the middle 50% of samples, which ignores outliers and is more reliable than a plain average." } |
| `speedtest_tracker_download_latency_low_ms` | ms | Lowest download latency. |
| `speedtest_tracker_download_latency_high_ms` | ms | Highest download latency. |
| `speedtest_tracker_upload_latency_iqm_ms` | ms | Upload latency, interquartile mean. :lucide-info:{ title="The mean of the middle 50% of samples, which ignores outliers and is more reliable than a plain average." } |
| `speedtest_tracker_upload_latency_low_ms` | ms | Lowest upload latency. |
| `speedtest_tracker_upload_latency_high_ms` | ms | Highest upload latency. |
| `speedtest_tracker_test_downloaded_bytes_total` | bytes | Total bytes downloaded during the test. |
| `speedtest_tracker_test_uploaded_bytes_total` | bytes | Total bytes uploaded during the test. |
| `speedtest_tracker_download_elapsed_ms` | ms | Duration of the download test. |
| `speedtest_tracker_upload_elapsed_ms` | ms | Duration of the upload test. |

### Prometheus Scrape Config

Below is an example Prometheus scrape configuration:

```yaml
scrape_configs:
  - job_name: 'speedtest-tracker'
    scrape_interval: 60s # Adjust to your set schedule
    scrape_timeout: 10s
    metrics_path: /prometheus
    static_configs:
      - targets: ['speedtest-tracker.local']
```
