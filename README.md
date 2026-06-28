# PHP 8.5 vs Node.js vs Go vs Python: I/O Performance Benchmark (2026)

This repository contains all test scripts, configuration files, and raw results for the article:

**"PHP 8.5 vs Node.js vs Go vs Python: I/O Performance Under Real Database Load (2026)"**

## Test Environment

- Server: 4 vCPU, 8 GB RAM, Ubuntu 22.04
- Database: MySQL 8.4
- PHP: 8.5.7 with JIT tracing, buffer 128M
- Swoole: 6.2.1, 20 workers, coroutine + PDO connection pool
- Node.js: 22.x, connection pool size 20
- Go: 1.24.x, database/sql connection pool size 20
- Python: 3.12.x, aiomysql connection pool size 20

## Key Results

| Language | 50ms RPS | 50ms P99 | 200ms RPS | 200ms P99 |
|----------|----------|----------|-----------|-----------|
| PHP+Swoole | **1,912** | **62** | **495** | **208** |
| Node.js | 393 | 256 | 99 | 1,008 |
| Go | 394 | 914 | 108 | 2,001 |
| Python | 393 | 461 | 99 | 2,600 |
| PHP-FPM | 388 | 264 | 99 | 1,016 |

## How to Replicate

1. Install PHP 8.5, Node.js 22, Go 1.24, Python 3.12, MySQL 8.4
2. Clone this repository
3. Run `./run-all.sh` to start all services
4. Run `./benchmark.sh` to execute the benchmarks
5. Results are saved in `results/` directory

## Repository Structure

https://phpbenchlab.com/
## License

MIT
