---
description: Common error messages, what causes them and how to fix them.
---

# Error Messages

### Troubleshooting

Most errors below have more details in the container logs. Start there, and enable debugging if the logs don't tell you enough.

#### Check the logs

```bash
docker logs speedtest-tracker
```

!!! note ""

    Replace `speedtest-tracker` with your container name if it's different.

#### Enable debugging

By default `APP_DEBUG` is set to `false` in production to prevent verbose error output. To debug an issue:

1. Add the environment variable to your container:

    ```bash
    APP_DEBUG=true
    ```

2. Restart the container.
3. Reproduce the error by visiting the page or performing the action that caused it.
4. View the output in the UI or in the [logs](#check-the-logs). The line that starts with `[timestamp] production.ERROR:` is the error the server ran into.
5. Once the issue is resolved, remove the `APP_DEBUG` environment variable.

If you can't resolve it, open an issue on [GitHub](https://github.com/alexjustesen/speedtest-tracker/issues) and include the error from the logs.

### Application

??? failure "I'm getting a `500 | SERVER ERROR` error"

    The application ran into a bug or a misconfiguration.

    **How to fix**

    * [Enable debugging](#enable-debugging) to see the exact cause of the error.

??? failure "Unsupported cipher or incorrect key length. Supported ciphers are: `aes-128-cbc`, `aes-256-cbc`, `aes-128-gcm`, `aes-256-gcm`."

    The [`APP_KEY`](../installation/environment-variables.md#application) is not set or not set correctly.

    **How to fix**

    * [Generate an application key](../installation/installation/docker.md#generate-an-application-key) and set it as `APP_KEY`, including the `base64:` prefix.
    * Restart the container.

??? failure "403 | FORBIDDEN on every page"

    [`ALLOWED_IPS`](../installation/environment-variables.md#application) is set and the IP address you're connecting from isn't in the list.

    **Possible reasons**

    * Your IP address changed, or you're connecting through a reverse proxy or VPN with a different address.
    * You used a range like `192.168.1.0/24`. `ALLOWED_IPS` only matches exact IP addresses, not ranges.

    **How to fix**

    * Add your exact IP address to `ALLOWED_IPS` (comma separated), or remove the variable, and restart the container.

### Speedtest Process

??? failure "Failed to connected to hostname"

    ```
    Failed to connected to hostname "<hostname>". Error received "<error>". HTTP fallback also failed.
    ```

    Before [running a speedtest](../other/speedtest-process.md), Speedtest Tracker checks for an internet connection. It first sends an ICMP ping to [icanhazip.com](https://icanhazip.com). If that fails it falls back to an HTTP request, and this error is shown when both fail.

    When the ping command itself isn't available you'll see `Ping command is unavailable and HTTP fallback also failed.` instead.

    **Possible reasons**

    * There is a Docker network problem or no internet connection.
    * A DNS block list blocks the hostname. If your server has internet access, add it to your allow list.
    * Your Docker user doesn't have permission to send ICMP requests. _Most_ Docker setups can without elevated privileges, but if yours can't you need to add the permission to the container.

    **How to fix**

    * Use a different hostname with [`SPEEDTEST_INTERNET_CHECK_HOSTNAME`](../installation/environment-variables.md#speed-tests).

??? failure "Failed to fetch external IP address"

    ```
    Failed to fetch external IP address from "<url>". See the logs for more details.
    ```

    When [`SPEEDTEST_SKIP_IPS`](../installation/environment-variables.md#speed-tests) is set, Speedtest Tracker fetches your external (WAN) IP address from [icanhazip.com](https://icanhazip.com) to check if the test should be skipped. This error is shown when that request fails.

    **Possible reasons**

    * There is a Docker network problem or no internet connection.
    * A DNS block list blocks the domain. If your server has internet access, add it to your allow list.

    **How to fix**

    * Use a different service with [`SPEEDTEST_EXTERNAL_IP_URL`](../installation/environment-variables.md#speed-tests).

    !!! warning

        The service you choose must return only the IP address in the body of the response.

??? info "Test skipped: IP address found in skip list"

    ```
    "<ip>" was found in external IP address skip list.
    "<ip>" was found in external IP address skip list within range "<range>".
    ```

    Not an error: the scheduled test was **skipped** because your external IP address matches [`SPEEDTEST_SKIP_IPS`](../installation/environment-variables.md#speed-tests). Only scheduled tests are skipped, manual tests always run.

    **How to fix**

    * If the test shouldn't have been skipped, remove the IP address or range from `SPEEDTEST_SKIP_IPS`.

### Ookla Related

Most of these errors come from the Ookla speedtest CLI. When the CLI returns more than one error, they're shown together separated by ` | `.

??? failure "An unexpected error occurred while running the Ookla CLI."

    The speedtest CLI failed, but its output didn't contain an error message Speedtest Tracker could read.

    **How to fix**

    * [Enable debugging](#enable-debugging) and check [the logs](#check-the-logs) for the CLI output.

??? failure "Configuration - Could not retrieve or read configuration (ConfigurationError)"

    The CLI couldn't reach the internet or the specified server.

    **How to fix**

    * Check the internet connection of the container and [the logs](#check-the-logs).

??? failure "Configuration - No servers defined (NoServersException)"

    The defined server is most likely no longer available.

    **How to fix**

    * Remove the server from [`SPEEDTEST_SERVERS`](../installation/environment-variables.md#speed-tests) and pick another one from the [Ookla server list](https://www.speedtest.net/api/js/servers).

??? failure "Server Selection - Failed to find a working test server. (NoServers)"

    The CLI can't find a server near you. The exact cause of this error isn't known yet.

    **How to fix**

    * Specify a list of servers with [`SPEEDTEST_SERVERS`](../installation/environment-variables.md#speed-tests).

??? failure "⚠️ Unable to retrieve Ookla servers, check internet connection and see logs."

    Shown when the server list can't be retrieved while selecting a server for a manual speedtest. The list is fetched from the [Ookla server list](https://www.speedtest.net/api/js/servers).

    **Possible reasons**

    * There is a Docker network problem or no internet connection.

    **How to fix**

    * Check [the logs](#check-the-logs) for more details.

??? failure "Error fetching servers"

    Shown on **Tools → List Ookla Servers** when the server list can't be fetched. The message below the title has the reason.

    **Possible reasons**

    * There is a Docker network problem or no internet connection.

    **How to fix**

    * Check the internet connection of the container and [the logs](#check-the-logs).

### Notifications

These are shown when you use the test buttons under **Settings → Notifications**.

??? failure "You need to add Apprise channel URLs!"

    No channel URLs are configured for [Apprise](../settings/notifications/apprise.md).

    **How to fix**

    * Add at least one [notification channel](../settings/notifications/apprise.md#notification-channels) URL.

??? failure "Apprise Server URL is not configured"

    The URL of your Apprise server isn't set.

    **How to fix**

    * Set the Apprise Server URL. Speedtest Tracker doesn't include an Apprise server, see [Apprise Server](../settings/notifications/apprise.md#apprise-server).

??? failure "Failed to send Apprise test notification"

    The test notification couldn't be delivered. The message below the title tells you why:

    | Message | Cause |
    | --- | --- |
    | `Could not connect to Apprise server at <url>` | The hostname can't be resolved. Check the URL and your DNS. |
    | `Connection refused by Apprise server at <url>` | Nothing is listening on that address and port. Check that the Apprise container is running. |
    | `Connection to Apprise server at <url> timed out` | The server didn't respond. Check the network between the containers. |
    | `Failed to connect to Apprise server at <url>` | Another connection error. Check [the logs](#check-the-logs). |
    | `Apprise returned an error, please check Apprise logs for details` | Apprise was reached but couldn't send the notification, usually because of an invalid channel URL. |

    **How to fix**

    * Make sure the Apprise server is reachable from the Speedtest Tracker container.
    * Check the channel URL format in the [Apprise documentation](https://github.com/caronc/apprise?tab=readme-ov-file#supported-notifications).

??? failure "Add email recipients!"

    No recipients are configured for [Mail](../settings/notifications/mail.md) notifications.

    **How to fix**

    * Add at least one [recipient](../settings/notifications/mail.md#recipients).

??? failure "Test webhook failed"

    The [webhook](../settings/notifications/webhook.md) test couldn't be delivered. The result appears in the 🔔 notifications, with the error below the title.

    **How to fix**

    * Check that the webhook URL is correct and reachable from the container.

### Data Integrations

??? failure "Influxdb test failed"

    Shown after **Test connection** on the [InfluxDB v2](../settings/data-platforms/influxdb2.md) settings, when test data can't be written.

    **Possible reasons**

    * InfluxDB can't be reached from the container.
    * The token is wrong or doesn't have write access.
    * The bucket doesn't exist in InfluxDB.

    **How to fix**

    * Check the [InfluxDB v2 settings](../settings/data-platforms/influxdb2.md#settings) and [the logs](#check-the-logs).

??? failure "Failed to bulk write to Influxdb."

    Shown when **Export current results** to InfluxDB fails. The possible reasons are the same as for the connection test above.

    **How to fix**

    * Use **Test connection** first, then check [the logs](#check-the-logs).

??? failure "403 | FORBIDDEN on `/prometheus`"

    Your Prometheus server's IP address isn't in the [allowed IPs](../settings/data-platforms/prometheus.md#allowed-ips) list.

    **How to fix**

    * Add the IP address or range (e.g. `172.18.0.0/16`) of your Prometheus server to the allowed IPs.

??? failure "404 | NOT FOUND on `/prometheus`"

    The Prometheus endpoint is disabled.

    **How to fix**

    * Enable [Prometheus](../settings/data-platforms/prometheus.md) under **Settings → Data Integration**.
