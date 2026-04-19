#!/usr/bin/env bash

set -euo pipefail

PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

BACKUP_DIR="/mnt/database_backup"
CONTAINER_NAME="ecolacup-postgres"
DB_NAME="ecolacup"
DB_USER="ecolacup"
DB_PASSWORD="ecolacup"

DOCKER_BIN="$(command -v docker)"

if [[ -z "${DOCKER_BIN}" ]]; then
  echo "docker binary not found" >&2
  exit 1
fi

mkdir -p "${BACKUP_DIR}"

timestamp="$(date +%F_%H-%M-%S)"
tmp_file="${BACKUP_DIR}/${DB_NAME}_${timestamp}.sql"
final_file="${tmp_file}.gz"

"${DOCKER_BIN}" exec -e PGPASSWORD="${DB_PASSWORD}" "${CONTAINER_NAME}" \
  pg_dump -U "${DB_USER}" -d "${DB_NAME}" -F p > "${tmp_file}"

gzip -f "${tmp_file}"

echo "Created backup: ${final_file}"
