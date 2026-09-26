# Apprise

[Apprise](https://github.com/caronc/apprise) sends notifications to 90+ services, like Discord, Pushover, Ntfy and many more, through a single notification channel.

??? info "Why Apprise"

    Using Apprise lets us focus on features instead of maintaining a separate integration for every notification service.

### Apprise Server

??? info "Support"

    We don't offer support on setting up Apprise. In case of any problems with the Apprise container, please reach out to the Apprise team.

To use Apprise you need to run your own Apprise server. It isn't included with Speedtest Tracker, so add it to your deployment. See the [Apprise API](https://github.com/caronc/apprise-api) repository for the setup instructions.

| Setting | Description |
| --- | --- |
| Apprise Server URL | URL of your Apprise server. It must end with `/notify`, e.g. `http://apprise:8000/notify`. The server needs to be reachable from the Speedtest Tracker container. |
| Verify SSL | Verify the SSL certificate of the Apprise server. Turn off when using a self-signed certificate. |

### Notification Channels

Notification channels are the URLs Apprise uses to send notifications to each service. They use the Apprise URL format, not `http://` or `https://`. See the [Apprise documentation](https://github.com/caronc/apprise?tab=readme-ov-file#supported-notifications) for all supported services and their URL formats.

You can add as many channels as you like. Notifications are sent to all of them.

### Test Notification

Save your settings first, then use **Test Apprise** to send a test notification to all channels. If it fails, see the [Apprise errors](../../help/error-messages.md#notifications) for what the message means.

### Tips and Tricks

#### Format

Messages are sent in `markdown` format, so they can include formatting like bold text.

#### Preview Images

By default Apprise doesn't show preview images for links. This is a setting of the Apprise server. Depending on the service you can override it in the notification channel URL. Check the Apprise documentation to see if and how your service supports it.

### Triggers

--8<-- "notification-triggers.md"
