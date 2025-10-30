Test and check Couchbase network connectivity using PHP SDKs.

## Features

- Test Couchbase connectivity using PHP SDK v3.x and v4.x
- Supports read-only and full checks
- Configurable log verbosity
- Dockerized for easy usage

## Quick Start

Pull and run the Docker image with required environment variables:

```bash
docker run --rm \
  -e COUCHBASE_CONNSTR="couchbase://host.docker.internal" \
  -e COUCHBASE_USER="username" \
  -e COUCHBASE_PASS="password" \
  -ti deminy/couchbase-network-check
```

## Environment Variables

### Required

- `COUCHBASE_CONNSTR`: Couchbase connection string (e.g., `couchbase://host.docker.internal`)
- `COUCHBASE_USER`: Couchbase username
- `COUCHBASE_PASS`: Couchbase password

### Optional

- `COUCHBASE_BUCKET`: To perform read/write checks on a specific bucket. If not set, only basic connectivity checks are performed.
- `COUCHBASE_READONLY`: Set to `1` to perform read-only checks on the specified bucket. Requires `COUCHBASE_BUCKET`.
- `COUCHBASE_LOG_LEVEL`: Set to `debug` or `trace` for verbose logging.

## Usage Examples

### Read-only Checks

```bash
docker run --rm \
  -e COUCHBASE_CONNSTR="couchbase://host.docker.internal" \
  -e COUCHBASE_USER="username" \
  -e COUCHBASE_PASS="password" \
  -e COUCHBASE_BUCKET="test" \
  -e COUCHBASE_READONLY=1 \
  -ti deminy/couchbase-network-check
```

### Debug-level Logging

```bash
docker run --rm \
  -e COUCHBASE_CONNSTR="couchbase://host.docker.internal" \
  -e COUCHBASE_USER="username" \
  -e COUCHBASE_PASS="password" \
  -e COUCHBASE_LOG_LEVEL=debug \
  -ti deminy/couchbase-network-check
```

## Local Development with Docker

### 1. Build Docker Images Manually

```bash
docker build --build-arg COUCHBASE_VERSION=4.4.0 -t deminy/couchbase-network-check .
docker build --build-arg COUCHBASE_VERSION=4.4.0 -t deminy/couchbase-network-check:4.4.0 .

docker build --platform linux/amd64 \
  --build-arg COUCHBASE_VERSION=3.2.2 \
  -t deminy/couchbase-network-check:3.2.2 .
```

### 2. Start the Docker Containers

```bash
docker compose up -d

docker compose exec -ti couchbase4 php --ri couchbase # PHP SDK v4.4.0
docker compose exec -ti couchbase3 php --ri couchbase # PHP SDK v3.2.2
```

### 3. Check Network Connectivity

```bash
# Basic connectivity checks:
docker compose exec -ti couchbase4 php ./check.php
docker compose exec -ti couchbase3 php ./check.php

# Perform read/write checks on a specific bucket:
docker compose exec -e COUCHBASE_BUCKET=test -ti couchbase4 php ./check.php
docker compose exec -e COUCHBASE_BUCKET=test -ti couchbase3 php ./check.php

# Read-only checks:
docker compose exec -e COUCHBASE_BUCKET=test -e COUCHBASE_READONLY=1 -ti couchbase4 php ./check.php
docker compose exec -e COUCHBASE_BUCKET=test -e COUCHBASE_READONLY=1 -ti couchbase3 php ./check.php

# Debug-level logging:
docker compose exec -e COUCHBASE_LOG_LEVEL=debug -ti couchbase4 php ./check.php
docker compose exec -e COUCHBASE_LOG_LEVEL=debug -ti couchbase3 php ./check.php

# Trace-level logging:
docker compose exec -e COUCHBASE_LOG_LEVEL=trace -ti couchbase4 php ./check.php
docker compose exec -e COUCHBASE_LOG_LEVEL=trace -ti couchbase3 php ./check.php
```

### 4. Clean Up

```bash
docker compose down
```

## License

This project is licensed under the terms of the MIT License. See [LICENSE.txt](LICENSE.txt) for details.
