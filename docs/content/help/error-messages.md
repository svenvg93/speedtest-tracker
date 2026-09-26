# Error Messages

### Troubleshooting

For all below errors there will be more information provided in the container logs. You can check the logs for more details by checking the container logs by running `docker logs speedtest-tracker`.

or any other equivalent command for your setup.

??? tip "Enable Debugging"

    By default `APP_DEBUG` is set to `false` in production to prevent verbose error outputs. To debug the issue follow the steps below.

    1. Set `APP_DEBUG=true` as a environment variable
    2. Restart the container
    3. Reproduce the error by visiting the page or performing the action that caused the error
    4. View the output in the UI or in the logs to help resolve the issue, if you can not resolve it open an issue in the [GitHub](https://github.com/alexjustesen/speedtest-tracker/issues) repository
    5. In the output the line that starts with `[timestamp] production.ERROR:` is the error the server ran into
    6. Once the issue is resolved you can remove the `APP_DEBUG` environment variable

### Application

??? failure "I'm getting a `500 | SERVER ERROR` error"

    The `500 | SERVER ERROR` is caused by either a bug or a misconfiguration. You must enable debugging (see [Troubleshooting](#troubleshooting)) to determine the exact cause of the error.

??? failure "Unsupported cipher or incorrect key length. Supported ciphers are: `aes-128-cbc`, `aes-256-cbc`, `aes-128-gcm,` `aes-256-gcm`."

    This error is shown when the `APP_KEY` is not set or not set correctly. Make suer you set the `APP_KEY` as described in the [installation steps](../installation/installation/docker.md#install-with-docker).

### Speedtest Process

??? failure "Failed to connected to hostname"

    When a speedtest is being [processed](../other/speedtest-process.md) Speedtest Tracker will make a ICMP ping to [icanhazip.com](http://icanhazip.com) to check if there is an internet connection before starting the Speedtest

    **Possible reasons**:

    * There is a docker network problem or no internet connection.
    * Some DNS blocks lists will block this domain, if you're getting errors and your server has access to the internet you'll need to add this to your allow lists.
    * _Most_ Docker setups can send ICMP requests without needed elevated privileges on the host or in the container. That being said if your Docker user doesn't run with elevated permissions or doesn't belong to the Docker group you can get a failure on this step. To allow the user to send ICMP requests you need to add the permission to the container.

    **Configuration options**

    * Use available [Environment Variables](../installation/environment-variables.md#speed-tests) to change the endpoint to your liking

??? failure "Failed to fetch external IP address"

    When the `SPEEDTEST_SKIP_IPS` environment variable is Speedtest Tracker will make a call to [http://icanhazip.com](http://icanhazip.com/) to get your external IP address. This is done check if your external IP address (WAN IP) should be skipped.

    **Possible reasons**:

    * There is a docker network problem or no internet connection.
    * Some DNS blocks lists will block this domain, if you're getting errors and your server has access to the internet you'll need to add this to your allow lists.

    **Configuration options**

    * Use available [Environment Variables](../installation/environment-variables.md#speed-tests) to change the endpoint to your liking. :warning: Whatever service you choose needs to only return an IP address in the body of the response for this to work.

### Ookla Related

??? failure "Configuration - Could not retrieve or read configuration (ConfigurationError)"

    This is usually thrown when the CLI fails to reach the internet (internet down) or the specified server.

??? failure "Configuration - No servers defined (NoServersException)"

    This usually means the defined server is no longer available. Remove it from your server list and try testing with a different server.

??? failure "Server Selection - Failed to find a working test server. (NoServers)"

    Not 100% sure what causes this exception yet but it's likely when the CLI can't locate a local server. You should specify a list of servers to see if that addresses the issue.

??? failure "Unable to retrieve Ookla servers, check internet connection and see logs."

    This errors is shown when we try to retrieve the Ookla server list when selecting an server wehn running an manual speedtest. We get the list from: [https://www.speedtest.net/api/js/servers](https://www.speedtest.net/api/js/servers).

    This error is useually caused by a docker network problem or no internet connection. You can check the [container logs](error-messages.md#troubleshooting) for more details.

### InfluxDB

??? failure "Failed to write to InfluxDB"

    When Speedtest Tracker fails to write data to InfluxDB this error is shown. The [container logs](error-messages.md#troubleshooting) will show more details on why it failed.

    **Possible reasons:**

    * Connectivity problem to influxdb
    * Problem with authentication
    * Specified bucket does not exist in InfluxDB
