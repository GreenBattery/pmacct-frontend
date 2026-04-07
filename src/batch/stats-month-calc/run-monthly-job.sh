#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

cd "$(dirname "$0")" || exit 1

echo "Starting job..."
docker compose run --build --rm -T stats-month-calc
echo "Job finished."