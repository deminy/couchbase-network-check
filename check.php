<?php

/**
 * This script is to check the diagnostics of Couchbase server using the PHP SDKs.
 *
 * Usage:
 *     docker compose exec -ti couchbase3 php ./check.php # Check diagnostics using PHP SDK v3.
 *     docker compose exec -ti couchbase4 php ./check.php # Check diagnostics using PHP SDK v4.
 */

declare(strict_types=1);

if (is_readable($_SERVER['HOME'] . '/.composer/vendor/autoload.php')) {
    // Load PHP SDK of Couchbase v4 if available.
    require_once $_SERVER['HOME'] . '/.composer/vendor/autoload.php';
}

use Couchbase\Cluster;
use Couchbase\ClusterOptions;

$connstr  = $_SERVER['COUCHBASE_CONNSTR'] ?? 'couchbase://server';
$username = $_SERVER['COUCHBASE_USER'] ?? 'username';
$password = $_SERVER['COUCHBASE_PASS'] ?? 'password';
$bucket   = $_SERVER['COUCHBASE_BUCKET'] ?? 'test';

$options = new ClusterOptions();
$options->credentials($username, $password);

$cluster = new Cluster($connstr, $options);
print_r($cluster->bucket($bucket)->diagnostics('kv'));
