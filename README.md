Test and check Couchbase network connectivity using PHP SDKs.

## Features

- Test Couchbase connectivity using PHP SDK v3.x and v4.x
- Supports read-only and full checks
- Configurable log verbosity
- Dockerized for easy usage

## How to Use

Run the following Docker command with four required environment variables specified:

```bash
docker run --rm --platform=linux/amd64 \
  -e COUCHBASE_CONNSTR="couchbase://host.docker.internal" \
  -e COUCHBASE_USER="username" \
  -e COUCHBASE_PASS="password" \
  -e COUCHBASE_BUCKET="test" \
  -ti deminy/couchbase-network-check:3.2.2

docker run --rm \
  -e COUCHBASE_CONNSTR="couchbase://host.docker.internal" \
  -e COUCHBASE_USER="username" \
  -e COUCHBASE_PASS="password" \
  -e COUCHBASE_BUCKET="test" \
  -ti deminy/couchbase-network-check:4.4.0
```

### Environment Variables

#### Required Environment Variables

- `COUCHBASE_CONNSTR`: Couchbase connection string (e.g., `couchbase://host.docker.internal`)
- `COUCHBASE_USER`: Couchbase username
- `COUCHBASE_PASS`: Couchbase password
- `COUCHBASE_BUCKET`: Couchbase bucket name

#### Optional Environment Variables

- `COUCHBASE_READONLY`: Set to `1` to perform read-only checks
- `COUCHBASE_LOG_LEVEL`: Set to `debug` or `trace` for verbose logging

### Examples

#### Example: Read-only Checks

```bash
docker run --rm --platform=linux/amd64 \
  -e COUCHBASE_CONNSTR="couchbase://host.docker.internal" \
  -e COUCHBASE_USER="username" \
  -e COUCHBASE_PASS="password" \
  -e COUCHBASE_BUCKET="test" \
  -e COUCHBASE_READONLY=1 \
  -ti deminy/couchbase-network-check:3.2.2
```

#### Example: Debug-level Logging

```bash
docker run --rm \
  -e COUCHBASE_CONNSTR="couchbase://host.docker.internal" \
  -e COUCHBASE_USER="username" \
  -e COUCHBASE_PASS="password" \
  -e COUCHBASE_BUCKET="test" \
  -e COUCHBASE_LOG_LEVEL=debug \
  -ti deminy/couchbase-network-check:4.4.0
```

## Local Development with Docker

### 1. Build Docker Images Manually

```bash
docker build --platform linux/amd64 \
  --build-arg COUCHBASE_VERSION=3.2.2 \
  -t deminy/couchbase-network-check:3.2.2 .

docker build \
  --build-arg COUCHBASE_VERSION=4.4.0 \
  -t deminy/couchbase-network-check:4.4.0 .
```

### 2. Start the Docker Containers

```bash
# Use Docker Compose to start Docker containers.
docker compose up -d

# Check Couchbase extension information.
docker compose exec -ti couchbase3 php --ri couchbase # PHP SDK v3.2.2
docker compose exec -ti couchbase4 php --ri couchbase # PHP SDK v4.4.0
```

### 3. Check Network Connectivity

```bash
docker compose exec -ti couchbase3 php ./check.php
docker compose exec -ti couchbase4 php ./check.php

# Read-only checks:
docker compose exec -e COUCHBASE_READONLY=1 -ti couchbase3 php ./check.php
docker compose exec -e COUCHBASE_READONLY=1 -ti couchbase4 php ./check.php

# Debug-level logging:
docker compose exec -e COUCHBASE_LOG_LEVEL=debug -ti couchbase3 php ./check.php
docker compose exec -e COUCHBASE_LOG_LEVEL=debug -ti couchbase4 php ./check.php

# Trace-level logging:
docker compose exec -e COUCHBASE_LOG_LEVEL=trace -ti couchbase3 php ./check.php
docker compose exec -e COUCHBASE_LOG_LEVEL=trace -ti couchbase4 php ./check.php
```

### 4. Clean Up

Stop the Docker containers to clean up:

```bash
docker compose down
```

## License

This project is licensed under the terms of the MIT License. See [LICENSE.txt](LICENSE.txt) for details.
