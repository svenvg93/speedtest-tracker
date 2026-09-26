| Trigger | When it's sent |
| --- | --- |
| Notify on every completed scheduled speedtest run | After every scheduled speedtest that completes and passes your thresholds, or when no thresholds are configured. |
| Notify on threshold failures for scheduled speedtests | When a scheduled speedtest fails any of your configured [thresholds](../../installation/environment-variables.md#speed-tests). |

!!! info

    Notifications are only sent for **scheduled** speedtests. Manual speedtests and speedtests that fail to run don't send a notification.
