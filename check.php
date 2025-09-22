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
use Couchbase\UpsertOptions;

class DataHelper
{
    /**
     * Default number of items to generate.
     */
    protected const DEFAULT_COUNT = 20;

    /**
     * Get Couchbase data.
     * @param int $count number of items to generate
     * @return array<string, array<string, string>>
     */
    public static function getCouchbaseData(int $count = self::DEFAULT_COUNT): array
    {
        return self::getData(fn (string $key) => [$key, json_encode(['foo' => 'bar'])], $count);
    }

    /**
     * Get Couchbase data.
     * @param int $count number of items to generate
     * @return array<string, array<string, string>>
     */
    protected static function getData(callable $callback, int $count = self::DEFAULT_COUNT): array
    {
        $data  = [];
        for ($i = 0; $i < $count; $i++) {
            $key = self::getNewKey();
            if (!array_key_exists($key, $data)) {
                $data[$key] = $callback($key);
            } else {
                $count++;
            }
        }

        return $data;
    }

    protected static function getNewKey(): string
    {
        return uniqid('test-');
    }
}

$connstr  = $_SERVER['COUCHBASE_CONNSTR'] ?? 'couchbase://server';
$username = $_SERVER['COUCHBASE_USER'] ?? 'username';
$password = $_SERVER['COUCHBASE_PASS'] ?? 'password';
$bucket   = $_SERVER['COUCHBASE_BUCKET'] ?? 'test';

$options = new ClusterOptions();
$options->credentials($username, $password);
$cluster = new Cluster($connstr, $options);
$bucket  = $cluster->bucket($bucket);

$upsertOptions = new UpsertOptions();
$upsertOptions->expiry(new \DateTime('+60 seconds'));
$bucket->defaultCollection()->upsertMulti(array_values(DataHelper::getCouchbaseData()), $upsertOptions);

print_r($bucket->diagnostics('kv'));
