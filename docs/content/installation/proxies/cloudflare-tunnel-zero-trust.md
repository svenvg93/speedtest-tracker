# Cloudflare Tunnel 

A [Cloudflare tunnel](https://www.cloudflare.com/products/tunnel/) can be used as a reverse proxy in front of Speedtest Tracker when you want to expose the application publicly without exposing your IP address.

--8<-- "community-proxy.md"

### Cloudflare Tunnel Configuration

* Update your `APP_URL` to the public URL you are going to use and restart the service.
* In the Cloudflare panel go to **Zero Trust** -> **Networks** -> **Tunnels** page.
* For the tunnel you want to add the Speedtest Tracker to click on **Edit** or add a new tunnel.
* Go to **Public Hostname.**
* Click on **Add a public hostname.**
* Fill in the following fields.
  * **Subdomain:** The subdomain you want to access the Speedtest Tracker on.
  * **Domain:** The domain you want to access the Speedtest Tracker on.
  * **Type:** Connection type to the Speedtest Tracker (http/https)
    * When choosing HTTPS you will need to disable the TLS verification under `Additional application settings -> TLS -> No TLS Verify`
  * **URL:** The URL to access the Speedtest Tracker. This can be either the `IP Address:Port` or the `container_name:port`.

!!! info
    When using the `container_name` Cloudflare Tunnel and Speedtest Tracker need to be on the same Docker network.


### Docker Configuration

Docker-Compose:

```yaml hl_lines="15 16"
services:
    speedtest-tracker:
        container_name: speedtest-tracker
        environment:
            - PUID=1000
            - PGID=1000
            - APP_KEY=
            - DB_CONNECTION=sqlite
            - SPEEDTEST_SCHEDULE=
            - SPEEDTEST_SERVERS=
            - PRUNE_RESULTS_OLDER_THAN=
            - CHART_DATETIME_FORMAT= 
            - DATETIME_FORMAT=
            - APP_TIMEZONE=
            - APP_URL=https://speedtest.yourdomain.com # (1)!
            - ASSET_URL=https://speedtest.yourdomain.com # (2)!
        volumes:
            - /path/to/data:/config
            - /path/to-custom-ssl-keys:/config/keys
        image: lscr.io/linuxserver/speedtest-tracker:latest
        restart: unless-stopped
```

1. URL you want to access Speedtest Tracker on. Change this to your domain name.
2. URL used to load the assets like CSS and JavaScript. Must be the same as `APP_URL`.

!!! info 

    Depending on your Cloudflare Tunnel configuration, you need to make sure the Speedtest Tracker and Cloudflare Tunnel are on the same docker network.
