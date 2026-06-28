#!/bin/bash
echo "Starting all services..."

# PHP-FPM
systemctl restart php8.5-fpm nginx

# Swoole
pkill -f swoole_server 2>/dev/null
cd /www/cross-lang-io-benchmark
nohup php swoole_server.php > /var/log/swoole.log 2>&1 &

# Node.js
pkill -f node_server 2>/dev/null
nohup node node_server.js > /var/log/node.log 2>&1 &

# Go
pkill -f go_server 2>/dev/null
nohup ./go_server > /var/log/go.log 2>&1 &

# Python
pkill -f python_server 2>/dev/null
nohup python3 python_server.py > /var/log/python.log 2>&1 &

sleep 3
echo "All services started."
curl -s "http://localhost/index.php?sleep=0" | jq .lang
curl -s "http://localhost:9501/?sleep=0" | jq .lang
curl -s "http://localhost:3000/?sleep=0" | jq .lang
curl -s "http://localhost:8080/?sleep=0" | jq .lang
curl -s "http://localhost:5000/?sleep=0" | jq .lang
