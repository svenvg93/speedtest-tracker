---
description: >-
  A complete inventory of all environment variables for configuring Speedtest
  Tracker.
---

# Environment Variables

### Application

| Name | Required | Default | Description |
| --- | --- | --- | --- |
| `PUID` | :lucide-circle-check:{ .green } | | Used to set the user the container should run as, e.g. `1000`. |
| `PGID` | :lucide-circle-check:{ .green } | | Used to set the group the container should run as, e.g. `1000`. |
| `APP_KEY` | :lucide-circle-check:{ .green } | | Key used to encrypt and decrypt data. See [Generate an Application Key](installation/docker.md#generate-an-application-key). |
| `APP_URL` | :lucide-circle-check:{ .green } | | URL used for links in emails and notifications, e.g. `https://speedtest.example.com`. |
| `APP_NAME` | :lucide-circle-x:{ .red } | `Speedtest Tracker` | Used to define the application's name in the dashboard and in notifications. |
| `ADMIN_NAME` | :lucide-circle-x:{ .red } | `Admin` | Name of the initial admin user. :lucide-info:{ title="Only effective during initial setup." } |
| `ADMIN_EMAIL` | :lucide-circle-x:{ .red } | `admin@example.com` | Email of the initial admin user. :lucide-info:{ title="Only effective during initial setup." } |
| `ADMIN_PASSWORD` | :lucide-circle-x:{ .red } | `password` | Password of the initial admin user. :lucide-info:{ title="Only effective during initial setup." } |
| `ASSET_URL` | :lucide-circle-x:{ .red } | | URL used for assets, needed when using a reverse proxy, e.g. `https://speedtest.example.com`. |
| `APP_LOCALE` | :lucide-circle-x:{ .red } | `en` | Change the default language. |
| `APP_TIMEZONE` | :lucide-circle-x:{ .red } | `UTC` | Application timezone should be set if your database does not use UTC as its default timezone, e.g. `Europe/London`. |
| `ALLOWED_IPS` | :lucide-circle-x:{ .red } | | Block requests to the application unless from the allowed addresses, e.g. `127.0.0.1,127.0.0.2`. |

***

### Display

| Name | Required | Default | Description |
| --- | --- | --- | --- |
| `CHART_BEGIN_AT_ZERO` | :lucide-circle-x:{ .red } | `true` | Begin the dashboard axis charts at zero. |
| `CHART_DATETIME_FORMAT` | :lucide-circle-x:{ .red } | `M. j - G:i` | Set the formatting of timestamps in charts, e.g. `j/m G:i` (18/10 20:06). Uses [PHP date format](https://www.php.net/manual/en/datetime.format.php) characters. |
| `CHART_ONLY_SHOW_AVG_LATENCY` | :lucide-circle-x:{ .red } | `false` | Only show the average latency on the download and upload latency charts, instead of the high and low values. |
| `DATETIME_FORMAT` | :lucide-circle-x:{ .red } | `M. j, Y g:ia` | Set the formatting of timestamps in tables and notifications, e.g. `j M Y, G:i:s` (18 Oct 2024, 20:06:01). Uses [PHP date format](https://www.php.net/manual/en/datetime.format.php) characters. |
| `DISPLAY_TIMEZONE` | :lucide-circle-x:{ .red } | `UTC` | Display timestamps in your local time, e.g. `America/New_York`. |
| `CONTENT_WIDTH` | :lucide-circle-x:{ .red } | `7xl` | Width of the content section of each page. Can be set to any [Filament max content width](https://filamentphp.com/docs/4.x/panel-configuration#customizing-the-maximum-content-width) value. |
| `PUBLIC_DASHBOARD` | :lucide-circle-x:{ .red } | `false` | Enables the public dashboard for guest (unauthenticated) users. |
| `DEFAULT_CHART_RANGE_DAYS` | :lucide-circle-x:{ .red } | `7` | Number of days shown on the dashboard charts by default. :lucide-info:{ title="Only effective during initial setup, afterwards change it under Settings → General." } |

***

### Speed tests

| Name | Required | Default | Description |
| --- | --- | --- | --- |
| `SPEEDTEST_SKIP_IPS` | :lucide-circle-x:{ .red } | | A comma separated list of public IP addresses where tests will be skipped when present, e.g. `127.0.0.1` or `127.0.0.0/16`. |
| `SPEEDTEST_SCHEDULE` | :lucide-circle-x:{ .red } | | Cron expression used to run speedtests on a scheduled basis, e.g. [`6 */2 * * *`](https://crontab.guru/#6_*/2_*_*_*) (at minute 6 past every 2nd hour). Use [crontab.guru](https://crontab.guru/) to build an expression. |
| `SPEEDTEST_SERVERS` | :lucide-circle-x:{ .red } | | Comma separated list of server IDs to randomly use for speedtest, e.g. `52365` or `36998,52365`. Find servers near you in the [Ookla server list](https://www.speedtest.net/api/js/servers). |
| `SPEEDTEST_BLOCKED_SERVERS` | :lucide-circle-x:{ .red } | | Comma separated list of server IDs that should not be used when running an Ookla Speedtest. |
| `SPEEDTEST_INTERFACE` | :lucide-circle-x:{ .red } | | Set the network interface to use for the test. This need to be the network interface available inside the container, e.g. `eth0`. |
| `SPEEDTEST_EXTERNAL_IP_URL` | :lucide-circle-x:{ .red } | `https://icanhazip.com` | URL of a service used to get the external WAN IP address. URL should contain the protocol i.e. `https://` |
| `SPEEDTEST_INTERNET_CHECK_HOSTNAME` | :lucide-circle-x:{ .red } | `icanhazip.com` | Hostname used to ping for an active internet connection. |
| `THRESHOLD_ENABLED` | :lucide-circle-x:{ .red } | `false` | Enable the thresholds. :lucide-info:{ title="Only effective during initial setup." } |
| `THRESHOLD_DOWNLOAD` | :lucide-circle-x:{ .red } | `0` | Set the Download Threshold in Mbps. :lucide-info:{ title="Only effective during initial setup." } |
| `THRESHOLD_UPLOAD` | :lucide-circle-x:{ .red } | `0` | Set the Upload Threshold in Mbps. :lucide-info:{ title="Only effective during initial setup." } |
| `THRESHOLD_PING` | :lucide-circle-x:{ .red } | `0` | Set the Ping Threshold in ms. :lucide-info:{ title="Only effective during initial setup." } |
| `PRUNE_RESULTS_OLDER_THAN` | :lucide-circle-x:{ .red } | `0` | Set the value to greater than zero to prune stored results. This value should be represented in days, e.g. `7` will purge all results over 7 days old. |

***

### API

| Name | Required | Default | Description |
| --- | --- | --- | --- |
| `API_RATE_LIMIT` | :lucide-circle-x:{ .red } | `60` | Number of requests per minute to the API. |
| `API_MAX_RESULTS` | :lucide-circle-x:{ .red } | `500` | Sets the maximum number of results returned by API. |
