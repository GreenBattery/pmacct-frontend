#!/bin/sh
set -e

DB_HOST="${DB_HOST:-db}"
DB_USER="${DB_USER:-router}"
DB_PASSWORD="${DB_PASSWORD:-secret}"
DB_NAME="${DB_NAME:-router}"
NFACCTD_PORT="${NFACCTD_PORT:-2055}"
LOCAL_SUBNET="${LOCAL_SUBNET:-192.168.1.0/24}"
LOCAL_SUBNET_V6="${LOCAL_SUBNET_V6:-fd00::/8}"

export DB_HOST DB_USER DB_PASSWORD DB_NAME NFACCTD_PORT LOCAL_SUBNET LOCAL_SUBNET_V6

echo "Waiting for MySQL database at $DB_HOST..."
until mysqladmin ping -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASSWORD" --silent > /dev/null 2>&1; do
    echo "Database is unavailable - sleeping 2s"
    sleep 2
done
echo "MySQL is up and ready."

echo "Generating /etc/pmacct/nfacctd.conf from template..."
envsubst '$DB_HOST $DB_USER $DB_PASSWORD $DB_NAME $NFACCTD_PORT $LOCAL_SUBNET $LOCAL_SUBNET_V6' < /etc/pmacct/nfacctd.conf.template > /etc/pmacct/nfacctd.conf

echo "Starting nfacctd listening on port $NFACCTD_PORT/udp..."
exec nfacctd -f /etc/pmacct/nfacctd.conf
