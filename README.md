Test and check Couchbase network connectivity using PHP SDKs.

## How to Use

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
  -ti deminy/couchbase-network-check:4.3.0
```

## Local Development with Docker

### 1. Build Docker Images Manually

```bash
docker build --platform linux/amd64 \
  --build-arg COUCHBASE_VERSION=3.2.2 \
  -t deminy/couchbase-network-check:3.2.2 .

docker build \
  --build-arg COUCHBASE_VERSION=4.3.0 \
  -t deminy/couchbase-network-check:4.3.0 .
```

### 2. Start the Docker Containers

```bash
# Use Docker Compose to start Docker containers.
docker compose up -d

# Check Couchbase extension information.
docker compose exec -ti couchbase3 php --ri couchbase # PHP SDK v3.2.2
docker compose exec -ti couchbase4 php --ri couchbase # PHP SDK v4.3.0
```

### 3. Check Network Connectivity

```bash
docker compose exec -ti couchbase3 php ./check.php
docker compose exec -ti couchbase4 php ./check.php
```

### 4. Clean Up

Stop the Docker containers to clean up:

```bash
docker compose down
```
