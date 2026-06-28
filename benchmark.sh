#!/bin/bash
echo "Starting benchmark..."
cd ~/io_bench_results
rm -f *.txt

URLS=(
    "php-fpm_50:http://localhost/index.php?sleep=0.05"
    "swoole_50:http://localhost:9501/?sleep=0.05"
    "nodejs_50:http://localhost:3000/?sleep=0.05"
    "go_50:http://localhost:8080/?sleep=0.05"
    "python_50:http://localhost:5000/?sleep=0.05"
    "php-fpm_200:http://localhost/index.php?sleep=0.2"
    "swoole_200:http://localhost:9501/?sleep=0.2"
    "nodejs_200:http://localhost:3000/?sleep=0.2"
    "go_200:http://localhost:8080/?sleep=0.2"
    "python_200:http://localhost:5000/?sleep=0.2"
)
for entry in "${URLS[@]}"; do
    name="${entry%%:*}"
    url="${entry#*:}"
    echo "=== Testing $name ==="
    for i in 1 2 3; do
        ab -c 100 -t 60 "$url" > "${name}_${i}.txt"
        sleep 5
    done
done
echo "Benchmark complete."
