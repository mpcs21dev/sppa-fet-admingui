#!/bin/bash

[ "$UID" -eq 0 ] || exec sudo -E bash "$0" "$@"

docker run --privileged --rm tonistiigi/binfmt --install all
docker build -f Dockerfile --platform linux/amd64 -t ureh/sppa-fet-admin-gui:1.2.42 .

