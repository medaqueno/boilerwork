#!/usr/bin/env bash

# Place here scripts to be executed on boot

set -e

if [[ "${BOOT_MODE}" == "SERVICE" ]] ; then
    echo "Docker container is running in SERVICE mode."
fi

if [[ "${BOOT_MODE}" == "TASK" ]] ; then
    echo "Docker container is running in TASK mode."
fi

echo "This line is printed out both in SERVICE mode and in TASK mode."
