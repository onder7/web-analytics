<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';
require 'models/AppLogger.php';
require 'config/database.php';
require 'models/GeoIpLookup.php';

$limit = isset($argv[1]) ? filter_var($argv[1], FILTER_VALIDATE_INT) : 50;
if ($limit === false || $limit === 0 || $limit < 1 || $limit > 500) {
    fwrite(STDERR, "Kullanım: php bin/process-geo-lookups.php [1-500]\n");
    exit(2);
}

$geoIpLookup = new GeoIpLookup();
$backfill = $geoIpLookup->enqueueMissingPageViews($limit * 10);
$processed = $geoIpLookup->processPending($limit);
fwrite(
    STDOUT,
    'Eksik geçmiş IP kuyruğa eklenen: ' . $backfill['queued'] .
    '; önbellekten güncellenen: ' . $backfill['updated'] .
    '; işlenen Geo-IP işleri: ' . $processed . "\n"
);
