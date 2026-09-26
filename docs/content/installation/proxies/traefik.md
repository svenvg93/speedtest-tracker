# Traefik

[Traefik](https://traefik.io) can be used as a Reverse Proxy in front of Speedtest Tracker when you want to expose the Dashboard publicly with a trusted certificate. You will need at add the `APP_URL` environment and needed labels to the docker compose have Traefik apply the certificate and routing.

Docker-Compose:

```yaml hl_lines="15 16 21 22 23 24 25 26"
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
        labels:
            - "traefik.enable=true" # (3)!
            - "traefik.http.routers.speedtest-tracker.rule=Host(`speedtest.yourdomain.com`)" # (4)!
            - "traefik.http.routers.speedtest-tracker.entrypoints=websecure" # (5)!
            - "traefik.http.routers.speedtest-tracker.tls=true" # (6)!
            - "traefik.http.routers.speedtest-tracker.tls.certresolver=yourresolver" # (7)!
            - "traefik.http.services.speedtest-tracker.loadbalancer.server.port=80" # (8)!
        image: lscr.io/linuxserver/speedtest-tracker:latest
        restart: unless-stopped
```

1. URL you want to access Speedtest Tracker on. Change this to your domain name.
2. URL used to load the assets like CSS and JavaScript. Must be the same as `APP_URL`.
3. Explicitly tell Traefik to expose this container.
4. The domain the service will respond to.
5. Only allow requests from the `websecure` entry point. Change this to match your Traefik configuration.
6. Only accept HTTPS requests on this router.
7. The certificate resolver to use. Change this to match your Traefik configuration.
8. The port Traefik uses to connect to the container.

!!! info

    Depending on your Traefik configuration, you need to make sure the Speedtest Tracker and Traefik are on the same docker network.
