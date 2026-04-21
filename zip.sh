#!/usr/bin/env bash

set -euo pipefail

PLUGIN_SLUG="configuratore-vernici"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DIST_DIR="${ROOT_DIR}/dist"
BUILD_DIR="${DIST_DIR}/build"
PACKAGE_DIR="${BUILD_DIR}/${PLUGIN_SLUG}"
ZIP_FILE="${DIST_DIR}/${PLUGIN_SLUG}.zip"

INCLUDE_ITEMS=(
	"admin"
	"includes"
	"languages"
	"public"
	"configuratore-vernici.php"
	"index.php"
	"LICENSE.txt"
	"README.txt"
	"uninstall.php"
)

if ! command -v zip >/dev/null 2>&1; then
	echo "Errore: il comando 'zip' non e disponibile." >&2
	exit 1
fi

rm -rf "${BUILD_DIR}" "${ZIP_FILE}"
mkdir -p "${PACKAGE_DIR}"

for item in "${INCLUDE_ITEMS[@]}"; do
	if [ -e "${ROOT_DIR}/${item}" ]; then
		cp -R "${ROOT_DIR}/${item}" "${PACKAGE_DIR}/"
	fi
done

find "${PACKAGE_DIR}" -name ".DS_Store" -delete

(
	cd "${BUILD_DIR}"
	zip -qr "${ZIP_FILE}" "${PLUGIN_SLUG}"
)

rm -rf "${BUILD_DIR}"

echo "ZIP creato: ${ZIP_FILE}"
