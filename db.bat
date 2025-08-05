@echo off
docker build --shm-size 1g -f Dockerfile -t sppa-fet/admin-gui:latest .

