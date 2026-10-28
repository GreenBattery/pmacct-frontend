# Container Update Automation

This directory contains scripts and systemd units to automatically rebuild and update the `pmacct-frontend` application containers on a weekly schedule.

## Target Containers Updated

- **`stats-view`**: The Apache/PHP web interface.
- **`stats-day-calc`**: Daily statistics aggregation CLI container.
- **`stats-month-calc`**: Monthly statistics aggregation CLI container.

The update pulls the latest patch releases for the upstream PHP base images (`php:8.4-apache-trixie` and `php:8.4-cli`), rebuilds the application containers, recreates `stats-view` seamlessly with zero database disruption, and cleans up old dangling image layers.

---

## Files

- **`update-containers.sh`**: Standalone bash script performing the build, recreation, and cleanup.
- **`pmacct-update.service`**: Oneshot systemd service running `update-containers.sh`.
- **`pmacct-update.timer`**: Systemd timer triggering the update every **Sunday at 02:00 AM**.

---

## Deployment Options

### Option A: System-wide Systemd Unit (Recommended for servers)

1. Open `pmacct-update.service` and verify the path to your project repository matches your server deployment path (e.g. `/opt/pmacct-frontend` or `/home/user/...`).

2. Copy the service and timer files to the systemd system directory:
   ```bash
   sudo cp pmacct-update.service /etc/systemd/system/
   sudo cp pmacct-update.timer /etc/systemd/system/
   ```

3. Reload systemd and enable the timer:
   ```bash
   sudo systemctl daemon-reload
   sudo systemctl enable --now pmacct-update.timer
   ```

---

### Option B: User-level Systemd Unit (No root required)

If your user account has Docker group permissions (`docker` group) and lingers when logged out:

1. Create the user systemd directory:
   ```bash
   mkdir -p ~/.config/systemd/user
   ```

2. Copy the files:
   ```bash
   cp pmacct-update.service ~/.config/systemd/user/
   cp pmacct-update.timer ~/.config/systemd/user/
   ```

3. Reload and enable:
   ```bash
   systemctl --user daemon-reload
   systemctl --user enable --now pmacct-update.timer
   ```

4. Enable lingering so the timer runs even when logged out:
   ```bash
   loginctl enable-linger $USER
   ```

---

## Verifying & Monitoring

- **Check active timers and next scheduled run:**
  ```bash
  systemctl list-timers pmacct-update.timer
  # or for user units:
  systemctl --user list-timers pmacct-update.timer
  ```

- **Run a manual test update now:**
  ```bash
  ./update-containers.sh
  # or via systemd:
  sudo systemctl start pmacct-update.service
  ```

- **View execution logs in journald:**
  ```bash
  journalctl -u pmacct-update.service -n 50 --no-pager
  # or for user units:
  journalctl --user -u pmacct-update.service -n 50 --no-pager
  ```
