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

use Couchbase\Bucket;
use Couchbase\Cluster;
use Couchbase\ClusterOptions;
use Couchbase\InsertOptions;

/**
 * The helper class to parse the KV report from Couchbase server.
 */
class CouchbaseKvReport {
    /**
     * @var int The major version of the Couchbase PHP SDK.
     */
    protected readonly int $majorVersion;

    /**
     * @var string The field name for the state/status of the Couchbase node, depending on the SDK version.
     */
    protected readonly string $fieldState;

    /**
     * @var array The report data structure to hold the parsed KV report.
     */
    protected array $report = [];

    public function __construct() {
        $this->majorVersion = (int) explode('.', (phpversion('couchbase') ?: ''))[0];
        $this->fieldState   = match ($this->majorVersion) {
            3 => 'status',
            4 => 'state',
        };
    }

    /**
     * To parse the KV report from Couchbase server.
     */
    public function parseKvReport(Bucket $bucket)
    {
        $result = match ($this->majorVersion) {
            3 => $bucket->diagnostics('kv')['kv'],
            4 => $bucket->diagnostics('kv')['services']['kv'],
        };

        foreach ($result as $row) {
            if (!isset($this->report[$row[$this->fieldState]])) {
                $this->report[$row[$this->fieldState]] = [];
            }
            if (!isset($this->report[$row[$this->fieldState]][$row['remote']])) {
                $this->report[$row[$this->fieldState]][$row['remote']] = 0;
            }
            $this->report[$row[$this->fieldState]][$row['remote']]++;
        }
    }

    /**
     * @return array The parsed KV report.
     */
    public function getKvReport(): array
    {
        return $this->report;
    }
}

// Set customized log level if specified.
$logLevel = $_SERVER['COUCHBASE_LOG_LEVEL'] ?? '';
if (!empty($logLevel)) {
    $couchbaseVersion = (int) explode('.', (phpversion('couchbase') ?: ''))[0];
    ini_set('couchbase.log_level', ($couchbaseVersion >= 4) ? strtolower($logLevel) : strtoupper($logLevel));
}

$connstr  = $_SERVER['COUCHBASE_CONNSTR'] ?? 'couchbase://server';
$username = $_SERVER['COUCHBASE_USER'] ?? 'username';
$password = $_SERVER['COUCHBASE_PASS'] ?? 'password';
$bucket   = $_SERVER['COUCHBASE_BUCKET'] ?? 'test';
$readOnly = isset($_SERVER['COUCHBASE_READONLY']);

$clusterOptions = new ClusterOptions();
$clusterOptions->credentials($username, $password);
$cluster = new Cluster($connstr, $clusterOptions);
$bucket  = $cluster->bucket($bucket);

$keys   = array_map(fn() => uniqid('test-') . '-' . rand(1, PHP_INT_MAX), range(1, 23));
$report = new CouchbaseKvReport();
if ($readOnly) {
    foreach ($keys as $key) {
        $bucket->defaultCollection()->getMulti($keys);
        $report->parseKvReport($bucket);
    }
} else {
    $insertOptions = new InsertOptions();
    $insertOptions->expiry(new \DateTime('+60 seconds'));
    $value = json_encode(['foo' => 'bar']);
    foreach ($keys as $key) {
        $bucket->defaultCollection()->insert($key, $value, $insertOptions);
        $report->parseKvReport($bucket);
    }
}

foreach ($report->getKvReport() as $state => $row) {
    foreach ($row as $host => $count) {
        printf("There are %d \"%s\" connections for Couchbase node %s.\n", $count, $state, $host);
    }
}
