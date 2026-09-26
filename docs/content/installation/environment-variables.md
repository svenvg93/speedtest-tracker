---
description: >-
  A complete inventory of all environment variables for configuring Speedtest
  Tracker.
---

# Environment Variables

### Application

| Name | Required | Description | Example |
| --- | --- | --- | --- |
| `PUID` | :lucide-circle-check:{ .green } | Used to set the user the container should run as. | `1000` |
| `PGID` | :lucide-circle-check:{ .green } | Used to set the group the container should run as. | `1000` |
| `APP_KEY` | :lucide-circle-check:{ .green } | Key used to encrypt and decrypt data. See the [install](installation/docker.md#generate-an-application-key) docs to generate a key. |  |
| `APP_URL` | :lucide-circle-check:{ .green } | URL used for links in emails and notifications. | `https://speedtest.example.com` |
| `APP_NAME` | :lucide-circle-x:{.red} | Used to define the application's name in the dashboard and in notifications. |  |
| `ADMIN_NAME` | :lucide-circle-x:{ .red } | Name of the initial admin user.<br>Note: Only effective during initial setup. | `Admin` |
| `ADMIN_EMAIL` | :lucide-circle-x:{ .red } | Email of the initial admin user.<br>Note: Only effective during initial setup. | `admin@example.com` |
| `ADMIN_PASSWORD` | :lucide-circle-x:{ .red } | Password of the initial admin user.<br>Note: Only effective during initial setup. | `password` |
| `ASSET_URL` | :lucide-circle-x:{ .red } | URL used for assets, needed when using a reverse proxy. | `https://speedtest.example.com` |
| `APP_LOCALE` | :lucide-circle-x:{ .red } | Change the default language. |  |
| `APP_TIMEZONE` | :lucide-circle-x:{ .red } | Application timezone should be set if your database does not use UTC as its default timezone. | `Europe/London` |
| `ALLOWED_IPS` | :lucide-circle-x:{ .red } | Block requests to the application unless from the allowed addresses. | `127.0.0.1,127.0.0.2` |

***

### Display

| Name | Required | Description | Example |
| --- | --- | --- | --- |
| `CHART_BEGIN_AT_ZERO` | :lucide-circle-x:{ .red } | Begin the dashboard axis charts at zero.<br><br>- Default: `true` | `true` or `false` |
| `CHART_DATETIME_FORMAT` | :lucide-circle-x:{ .red } | Set the formatting of timestamps in charts.<br><br>Formatting: [https://www.php.net/manual/en/datetime.format.php](https://www.php.net/manual/en/datetime.format.php) | `j/m G:i`<br>(18/10 20:06) |
| `DATETIME_FORMAT` | :lucide-circle-x:{ .red } | Set the formatting of timestamps in tables and notifications.<br><br>Formatting: [https://www.php.net/manual/en/datetime.format.php](https://www.php.net/manual/en/datetime.format.php) | `j M Y, G:i:s`<br>(18 Oct 2024, 20:06:01) |
| `DISPLAY_TIMEZONE` | :lucide-circle-x:{ .red } | Display timestamps in your local time. | `America/New_York` |
| `CONTENT_WIDTH` | :lucide-circle-x:{ .red } | Width of the content section of each page. Can be set to any value found in the Filament [docs](https://filamentphp.com/docs/4.x/panel-configuration#customizing-the-maximum-content-width).<br><br>- Default: `7xl` |  |
| `PUBLIC_DASHBOARD` | :lucide-circle-x:{ .red } | Enables the public dashboard for guest (unauthenticated) users.<br><br>- Default: `false` |  |
| `DEFAULT_CHART_RANGE` | :lucide-circle-x:{ .red } | Set the default time range for the dashboards<br><br>- Default: `24h` | Options: `24h`, `week` or `month` |

***

### Speed tests

| Name | Required | Description | Example |
| --- | --- | --- | --- |
| `SPEEDTEST_SKIP_IPS` | :lucide-circle-x:{ .red } | A comma separated list of public IP addresses where tests will be skipped when present. | `127.0.0.1` or `127.0.0.0/16` |
| `SPEEDTEST_SCHEDULE` | :lucide-circle-x:{ .red } | Cron expression used to run speedtests on a scheduled basis. https://crontab.guru/ is a helpful tool. | `6 */2 * * *`<br>(_At minute 6 past every 2nd hour)_ |
| `SPEEDTEST_SERVERS` | :lucide-circle-x:{ .red } | Comma separated list of server IDs to randomly use for speedtest.<br>To find servers near you visit: [https://www.speedtest.net/api/js/servers](https://www.speedtest.net/api/js/servers) | `52365` or `36998,52365` |
| `SPEEDTEST_BLOCKED_SERVERS` | :lucide-circle-x:{ .red } | Comma separated list of server IDs that should not be used when running an Ookla Speedtest. |  |
| `SPEEDTEST_INTERFACE` | :lucide-circle-x:{ .red } | Set the network interface to use for the test. This need to be the network interface available inside the container | `eth0` |
| `SPEEDTEST_EXTERNAL_IP_URL` | :lucide-circle-x:{ .red } | URL of a service used to get the external WAN IP address. URL should contain the protocol i.e. `https://` | `https://icanhazip.com` |
| `SPEEDTEST_INTERNET_CHECK_HOSTNAME` | :lucide-circle-x:{ .red } | Hostname used to ping for an active internet connection. |  |
| `THRESHOLD_ENABLED` | :lucide-circle-x:{ .red } | Enable the thresholds. Note: Only effective during initial setup. | `true` |
| `THRESHOLD_DOWNLOAD` | :lucide-circle-x:{ .red } | Set the Download Threshold<br>Note: Only effective during initial setup. | `900` |
| `THRESHOLD_UPLOAD` | :lucide-circle-x:{ .red } | Set the Upload Threshold<br>Note: Only effective during initial setup. | `900` |
| `THRESHOLD_PING` | :lucide-circle-x:{ .red } | Set the Ping Threshold<br>Note: Only effective during initial setup. | `25` |
| `PRUNE_RESULTS_OLDER_THAN` | :lucide-circle-x:{ .red } | Set the value to greater than zero to prune stored results. This value should be represented in days, e.g. `7` will purge all results over 7 days old. | `7` |

***

### API

| Name | Required | Description | Example |
| --- | --- | --- | --- |
| `API_RATE_LIMIT` | :lucide-circle-x:{ .red } | Number of requests per minute to the API.<br><br>- Default: `60` | `100` |
| `API_MAX_RESULTS` | :lucide-circle-x:{ .red } | Sets the maximum number of results returned by API.<br><br>- Default `500`<br> | `500` |
