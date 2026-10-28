#!/usr/bin/env bash
#
# update-containers.sh
# Weekly automated patch/security updates for pmacct-frontend containers:
#   - stats-view (Apache/PHP web frontend)
#   - stats-day-calc (Daily stats aggregation job)
#   - stats-month-calc (Monthly stats aggregation job)
#

set -euo pipefail

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"
}

# Resolve project root relative to this script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

log "Starting pmacct-frontend container update in: ${PROJECT_ROOT}"
cd "${PROJECT_ROOT}"

# Verify docker compose is available
if docker compose version >/dev/null 2>&1; then
    DOCKER_COMPOSE="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
    DOCKER_COMPOSE="docker-compose"
else
    log "ERROR: Neither 'docker compose' nor 'docker-compose' was found." >&2
    exit 1
fi

# Step 1: Pull upstream base images and rebuild target containers
log "Pulling updated base images and rebuilding stats-view, stats-day-calc, stats-month-calc..."
${DOCKER_COMPOSE} build --pull stats-view stats-day-calc stats-month-calc

# Step 2: Recreate running web application container with newly built image
log "Recreating stats-view container with updated image..."
${DOCKER_COMPOSE} up -d --no-deps stats-view

# Step 3: Prune dangling/untagged images to prevent disk space buildup
log "Pruning dangling images..."
docker image prune -f

log "pmacct-frontend container update completed successfully."
