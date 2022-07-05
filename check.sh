#!/usr/bin/env bash

set -e

[[ ! $(composer validate --no-check-publish) =~ "is not valid" ]] || echo "composer.json and composer.lock are synced"
