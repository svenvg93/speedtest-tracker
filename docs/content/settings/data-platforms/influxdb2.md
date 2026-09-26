# InfluxDB v2

After every test the Speedtest Tracker can send the results to InfluxDB for long term storage or custom visualizations.

### Settings

To configure Speedtest Tracker to send results to InfluxDB, set the following settings.

| Name | Default | Description |
| --- | --- | --- |
| URL | | URL of the InfluxDB v2 instance, including the protocol, e.g. `http://influxdb:8086`. |
| Org | | Organization in which you created your bucket. |
| Bucket | `speedtest-tracker` | Name of the bucket you created in your org. |
| Token | | API token with write access to the org and bucket above. |
| Verify SSL | On | Verify the SSL certificate of the InfluxDB instance. Turn off when using a self-signed certificate. |

<figure markdown="span">
  ![Influxdb v2 Settings](../../assets/images/influxdbv2-settings.png)
  <figcaption>Influxdb v2 Settings</figcaption>
</figure>

If you have a history of results, use **Export current results** to write all completed results to InfluxDB.

### Grafana Dashboard

You can use this community made Grafana Dashboard to visualize your data.

[masterwishx/Speedtest-Tracker-v2-InfluxDBv2](https://github.com/masterwishx/Speedtest-Tracker-v2-InfluxDBv2)

### Data pattern

Every result is written as one point to the `speedtest` measurement. Tags are used for filtering, while fields are used for displaying the data.

!!! info "When results are written"

    A point is written when a speedtest completes or fails, with the time the test started as its timestamp (second precision).

    For a failed test the speed and latency fields are empty, so only the tags and `log_message` are written. **Export current results** only exports completed results.

#### Tags

All tags are strings.

| Tag | Description |
| --- | --- |
| `app_name` | Value of [`APP_NAME`](../../installation/environment-variables.md#application). |
| `result_id` | ID of the result in Speedtest Tracker. |
| `id` | Same as `result_id`. |
| `external_ip` | Your external (WAN) IP address during the test. |
| `isp` | Your internet service provider. |
| `service` | Service used to run the test, `ookla`. |
| `server_id` | ID of the speedtest server. |
| `server_name` | Name of the speedtest server. |
| `server_country` | Country of the speedtest server. |
| `server_location` | Location of the speedtest server. |
| `scheduled` | `true` for a scheduled test, `false` for a manual test. |
| `healthy` | `true` when the result passed the [thresholds](../../installation/environment-variables.md#speed-tests), otherwise `false`. Not set when thresholds are disabled. |
| `status` | `completed` or `failed`. |

#### Fields

| Field | Type | Unit | Description |
| --- | --- | --- | --- |
| `download` | int | bytes/s | Download speed. |
| `upload` | int | bytes/s | Upload speed. |
| `download_bits` | int | bits/s | Download speed. |
| `upload_bits` | int | bits/s | Upload speed. |
| `ping` | float | ms | Ping latency. |
| `ping_jitter` | float | ms | Ping jitter. |
| `download_jitter` | float | ms | Download jitter. |
| `upload_jitter` | float | ms | Upload jitter. |
| `download_latency_avg` | float | ms | Download latency, interquartile mean. :lucide-info:{ title="Despite the name this is the interquartile mean: the mean of the middle 50% of samples, which ignores outliers." } |
| `download_latency_low` | float | ms | Lowest download latency. |
| `download_latency_high` | float | ms | Highest download latency. |
| `upload_latency_avg` | float | ms | Upload latency, interquartile mean. :lucide-info:{ title="Despite the name this is the interquartile mean: the mean of the middle 50% of samples, which ignores outliers." } |
| `upload_latency_low` | float | ms | Lowest upload latency. |
| `upload_latency_high` | float | ms | Highest upload latency. |
| `packet_loss` | float | % | Packet loss. |
| `downloaded_bytes` | float | bytes | Total bytes downloaded during the test. |
| `uploaded_bytes` | float | bytes | Total bytes uploaded during the test. |
| `download_elapsed` | float | ms | Duration of the download test. |
| `upload_elapsed` | float | ms | Duration of the upload test. |
| `log_message` | string | | Error message of a failed test. |
