<?php
class GeoIpLookup {
    private $db;
    private static $unknown = [
        'country' => null,
        'country_code' => null,
        'region' => null,
        'city' => null,
        'latitude' => null,
        'longitude' => null,
        'timezone' => null,
        'isp' => null
    ];

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function cachedOrQueue($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return array_merge(self::$unknown, [
                'country' => 'Local',
                'country_code' => 'LO',
                'region' => 'Local',
                'city' => 'Localhost',
                'timezone' => date_default_timezone_get(),
                'isp' => 'Local'
            ]);
        }

        $statement = $this->db->prepare(
            'SELECT country, country_code, region, city, latitude, longitude, timezone, isp
             FROM geo_ip_cache WHERE ip_address = ?'
        );
        $statement->execute([$ip]);
        $cached = $statement->fetch();
        if ($cached) {
            return $cached;
        }

        $enqueue = $this->db->prepare('INSERT IGNORE INTO geo_lookup_queue (ip_address) VALUES (?)');
        $enqueue->execute([$ip]);
        return self::$unknown;
    }

    public function enqueueMissingPageViews($limit = 5000) {
        $limit = max(1, min(5000, (int) $limit));
        $statement = $this->db->query(
            'SELECT DISTINCT ip_address FROM page_views
             WHERE country IS NULL AND ip_address IS NOT NULL AND ip_address <> ""
             ORDER BY ip_address LIMIT ' . $limit
        );
        $enqueue = $this->db->prepare('INSERT IGNORE INTO geo_lookup_queue (ip_address) VALUES (?)');
        $updated = 0;
        $queued = 0;

        while ($row = $statement->fetch()) {
            $ip = $row['ip_address'];
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $this->updatePageViews($ip, [
                    'country' => 'Local',
                    'country_code' => 'LO',
                    'region' => 'Local',
                    'city' => 'Localhost',
                    'latitude' => null,
                    'longitude' => null,
                    'timezone' => date_default_timezone_get(),
                    'isp' => 'Local'
                ]);
                $updated++;
                continue;
            }

            $cache = $this->db->prepare(
                'SELECT country, country_code, region, city, latitude, longitude, timezone, isp
                 FROM geo_ip_cache WHERE ip_address = ?'
            );
            $cache->execute([$ip]);
            $geo = $cache->fetch();
            if ($geo) {
                $this->updatePageViews($ip, $geo);
                $updated++;
                continue;
            }

            $enqueue->execute([$ip]);
            $queued += $enqueue->rowCount();
        }

        return ['updated' => $updated, 'queued' => $queued];
    }

    public function processPending($limit = 50) {
        $limit = max(1, min(500, (int) $limit));
        $processed = 0;

        while ($processed < $limit) {
            $job = $this->claimJob();
            if (!$job) {
                break;
            }
            $processed++;

            try {
                $geo = $this->lookup($job['ip_address']);
                $this->store($job['ip_address'], $geo);
                $this->updatePageViews($job['ip_address'], $geo);
                $delete = $this->db->prepare('DELETE FROM geo_lookup_queue WHERE id = ?');
                $delete->execute([(int) $job['id']]);
            } catch (Throwable $error) {
                AppLogger::error('geo-ip-worker', $error->getMessage());
                $attempts = (int) $job['attempts'] + 1;
                $delayMinutes = $attempts >= 5 ? 1440 : min(60, 2 ** $attempts);
                $retry = $this->db->prepare(
                    'UPDATE geo_lookup_queue
                     SET attempts = ?, available_at = DATE_ADD(NOW(), INTERVAL ? MINUTE)
                     WHERE id = ?'
                );
                $retry->execute([$attempts, $delayMinutes, (int) $job['id']]);
            }
        }

        return $processed;
    }

    private function claimJob() {
        $this->db->beginTransaction();
        try {
            $select = $this->db->query(
                'SELECT id, ip_address, attempts FROM geo_lookup_queue
                 WHERE available_at <= NOW() ORDER BY id LIMIT 1 FOR UPDATE'
            );
            $job = $select->fetch();
            if (!$job) {
                $this->db->commit();
                return null;
            }

            $claim = $this->db->prepare(
                'UPDATE geo_lookup_queue SET available_at = DATE_ADD(NOW(), INTERVAL 5 MINUTE)
                 WHERE id = ?'
            );
            $claim->execute([(int) $job['id']]);
            $this->db->commit();
            return $job;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    private function lookup($ip) {
        $services = [
            'ip-api' => "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,regionName,city,lat,lon,timezone,isp",
            'ipinfo' => "https://ipinfo.io/{$ip}/json",
            'freegeoip' => "https://freegeoip.app/json/{$ip}"
        ];

        foreach ($services as $name => $url) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'user_agent' => 'NetworkAnalytics/1.0',
                    'ignore_errors' => true
                ]
            ]);
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                continue;
            }

            $data = json_decode($response, true);
            if (!is_array($data)) {
                continue;
            }
            $geo = $this->parseResponse($name, $data);
            if ($geo !== null) {
                return $geo;
            }
        }

        throw new RuntimeException('Tüm coğrafi konum servisleri başarısız oldu.');
    }

    private function parseResponse($service, array $data) {
        if ($service === 'ip-api') {
            if (($data['status'] ?? '') !== 'success') {
                return null;
            }
            return [
                'country' => $data['country'] ?? null,
                'country_code' => $data['countryCode'] ?? null,
                'region' => $data['regionName'] ?? null,
                'city' => $data['city'] ?? null,
                'latitude' => $data['lat'] ?? null,
                'longitude' => $data['lon'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'isp' => $data['isp'] ?? null
            ];
        }

        if ($service === 'ipinfo' && !empty($data['country'])) {
            $location = explode(',', $data['loc'] ?? '');
            return [
                'country' => $data['country'] ?? null,
                'country_code' => $data['country'] ?? null,
                'region' => $data['region'] ?? null,
                'city' => $data['city'] ?? null,
                'latitude' => isset($location[0]) ? (float) $location[0] : null,
                'longitude' => isset($location[1]) ? (float) $location[1] : null,
                'timezone' => $data['timezone'] ?? null,
                'isp' => $data['org'] ?? null
            ];
        }

        if ($service === 'freegeoip' && !empty($data['country_code'])) {
            return [
                'country' => $data['country_name'] ?? null,
                'country_code' => $data['country_code'] ?? null,
                'region' => $data['region_name'] ?? null,
                'city' => $data['city'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'timezone' => $data['time_zone'] ?? null,
                'isp' => null
            ];
        }

        return null;
    }

    private function store($ip, array $geo) {
        $statement = $this->db->prepare(
            'INSERT INTO geo_ip_cache
                (ip_address, country, country_code, region, city, latitude, longitude, timezone, isp)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                country = VALUES(country), country_code = VALUES(country_code),
                region = VALUES(region), city = VALUES(city), latitude = VALUES(latitude),
                longitude = VALUES(longitude), timezone = VALUES(timezone), isp = VALUES(isp),
                updated_at = CURRENT_TIMESTAMP'
        );
        $statement->execute([
            $ip, $geo['country'], $geo['country_code'], $geo['region'], $geo['city'],
            $geo['latitude'], $geo['longitude'], $geo['timezone'], $geo['isp']
        ]);
    }

    private function updatePageViews($ip, array $geo) {
        $statement = $this->db->prepare(
            'UPDATE page_views
             SET country = ?, country_code = ?, region = ?, city = ?,
                 latitude = ?, longitude = ?, timezone = ?, isp = ?
             WHERE ip_address = ?'
        );
        $statement->execute([
            $geo['country'], $geo['country_code'], $geo['region'], $geo['city'],
            $geo['latitude'], $geo['longitude'], $geo['timezone'], $geo['isp'], $ip
        ]);
    }
}
