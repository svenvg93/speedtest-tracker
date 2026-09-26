---
description: A running list of frequently asked questions and their answers.
---

# Frequently Asked Questions

### :lucide-container: Docker

??? question "I get a warning on container start up that the `APP_KEY` is missing"

    You need an [`APP_KEY`](../installation/environment-variables.md#application) for encryption. See [Generate an Application Key](../installation/installation/docker.md#generate-an-application-key) for how to create one.

### :lucide-bell: Notifications

??? question "Links in emails don't point to the correct URL"

    1. Set the correct URL as the [`APP_URL`](../installation/environment-variables.md#application) environment variable.
    2. Restart the container.

### :lucide-clock: Time zones

??? question "My display timestamps or scheduled tests aren't correct"

    Speedtest Tracker assumes your application and database containers are set to UTC by default. If your database uses your local time zone, it needs to **match** the [`APP_TIMEZONE`](../installation/environment-variables.md#application) and [`DISPLAY_TIMEZONE`](../installation/environment-variables.md#display) environment variables.

    Restart the container after changing them.

### :lucide-gauge: Speedtest

??? question "Scheduled tests give lower results than manual tests"

    Many schedules run exactly on the hour, so speedtest servers are busiest then. Start your [`SPEEDTEST_SCHEDULE`](../installation/environment-variables.md#speed-tests) at an off-peak minute to reduce network congestion and avoid overloaded servers, e.g. [`6 */2 * * *`](https://crontab.guru/#6_*/2_*_*_*) (at minute 6 past every 2nd hour).

    This [GitHub comment](https://github.com/alexjustesen/speedtest-tracker/issues/552#issuecomment-2028532010) explains how to get the cron formatting right.
