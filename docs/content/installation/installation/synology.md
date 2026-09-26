---
icon: lucide/hard-drive
description: >-
  These instructions will run you through setting up Speedtest Tracker on a
  Synology NAS using Container Manager.
---

# Synology

!!! warning

    The following directions are for the old "Docker" application, if you're using "Container Manager" you can follow the docker compose instructions in [Docker](docker.md).

### Install on a Synology NAS

!!! warning

    This guide assumes you know how to use the old "Docker" application.

#### Download Image

Open the Docker interface of your Synology Device, search for `linuxserver/speedtest-tracker` in the Registry and download it.

#### Create Directory

Create a local directory (i.e. `/volume1/docker/speedtest-tracker`) which later can be mapped to the docker container.

#### Start Image

Launch the image once the download is completed.

#### Map Ports

Map the ports to available ports.

| Local Port | Container Port |
| ---------- | -------------- |
| 8443       | 443            |
| 8080       | 80             |

!!! info

    Make sure the ports you choose are not used by any other application or DSM service on your device and remember to adjust the Synology Firewall settings accordingly.

#### Map Directory

Map the directory you created earlier to the mount path `/config`.

#### Finish

Review your settings and click "done".

You can now access Speedtest-Tracker via `http://YOUR_IP_ADDRESS:8080` or `https://YOUR_IP_ADDRESS:8443`.
