#!/bin/bash

[ "$UID" -eq 0 ] || exec sudo -E bash "$0" "$@"

docker run --privileged --rm tonistiigi/binfmt --install all
docker build --shm-size 1g -f Dockerfile -t ureh/sppa-fet-admin-gui:$1 --platform linux/amd64 .

