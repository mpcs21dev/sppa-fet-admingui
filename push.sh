#! /bin/bash

[ "$UID" -eq 0 ] || exec sudo -E bash "$0" "$@"

docker push ureh/sppa-fet-admin-gui:$1
