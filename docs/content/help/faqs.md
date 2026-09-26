---
description: A running list of frequently ask questions and their answers.
---

# Frequently Asked Questions

### Docker

??? question "I get a warning on container start up that the `APP_KEY` is missing"

    You need a `APP_KEY` for the encryption. See the [installation docs](../installation/installation/index.md) how to generate one.

### Notifications

??? question "Links in emails don't point to the correct URL"

    1. Set the correct URL as the `APP_URL` environment variable
    2. Restart the container

??? question "I'm getting duplicate message via Apprise"

    By default when sending an notifications via Apprise we wait up to 30 seconds for Apprise to respond back with any message. Incase this 30 seconds is exceeded, we will retry 3 times. In case of any very slow Apprise processing this might cause duplicated notifications. Please check the [logs](error-messages.md#troubleshooting) to see the the timeout happend

### Time zones

??? question "My display timestamps or scheduled tests aren't correct."

    Speedtest Tracker assumes your application and database containers are set to `UTC` by default. If your database instance has your local time zone set it needs to **match** that set in `APP_TIMEZONE` and `DISPLAY_TIMEZONE` environment variables.

    Once set restart the container.

### Speedtest

??? question "Scheduled tests give lower results then manual tests"

    Starting your cron schedule at an off-peak minute can help reduce network congestion or avoid overloading a speed test server. This [comment](https://github.com/alexjustesen/speedtest-tracker/issues/552#issuecomment-2028532010) on this issue can help you get the formatting right.
