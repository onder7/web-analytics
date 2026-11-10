<?php
class AnalyticsDashboard {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function fetchAll($sql, $params = []) {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    private function fetchOne($sql, $params = []) {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return $row ?: [];
    }

    private function breakdown($rows, $labelColumn, $countColumn = 'count') {
        $total = array_sum(array_map(function($row) use ($countColumn) {
            return (int) $row[$countColumn];
        }, $rows));
        $colors = ['#3b82f6', '#06b6d4', '#f59e0b', '#10b981', '#f43f5e', '#94a3b8'];
        $result = [];

        foreach ($rows as $index => $row) {
            $count = (int) $row[$countColumn];
            $result[] = [
                'label' => (string) ($row[$labelColumn] ?: 'Bilinmiyor'),
                'count' => $count,
                'value' => $total > 0 ? round($count * 100 / $total, 1) : 0,
                'color' => $colors[$index % count($colors)]
            ];
        }

        return $result;
    }

    // Sosyal platform tanımları: domain parçası → [görünen ad, renk, lucide ikon]
    private static $socialPlatformMap = [
        'facebook'   => ['Facebook',   '#1877f2', 'facebook'],
        'instagram'  => ['Instagram',  '#e1306c', 'instagram'],
        'twitter'    => ['Twitter/X',  '#1da1f2', 'twitter'],
        'x.com'      => ['Twitter/X',  '#000000', 'twitter'],
        'youtube'    => ['YouTube',    '#ff0000', 'youtube'],
        'tiktok'     => ['TikTok',     '#010101', 'music-2'],
        'linkedin'   => ['LinkedIn',   '#0a66c2', 'linkedin'],
        'reddit'     => ['Reddit',     '#ff4500', 'message-square'],
        'pinterest'  => ['Pinterest',  '#e60023', 'image'],
        'snapchat'   => ['Snapchat',   '#fffc00', 'camera'],
        'whatsapp'   => ['WhatsApp',   '#25d366', 'message-circle'],
        'telegram'   => ['Telegram',   '#229ed9', 'send'],
        'discord'    => ['Discord',    '#5865f2', 'message-square'],
        'tumblr'     => ['Tumblr',     '#35465d', 'layout'],
        'vk'         => ['VKontakte',  '#4a76a8', 'users'],
        'weibo'      => ['Weibo',      '#df2029', 'share-2'],
        'mastodon'   => ['Mastodon',   '#6364ff', 'share-2'],
        'threads'    => ['Threads',    '#000000', 'at-sign'],
        'bluesky'    => ['Bluesky',    '#0085ff', 'cloud'],
    ];

    // Arama motoru sorgu parametreleri: domain parçası → sorgu param adı
    private static $searchEngineQueryParams = [
        'google'     => 'q',
        'bing'       => 'q',
        'yahoo'      => 'p',
        'duckduckgo' => 'q',
        'yandex'     => 'text',
        'baidu'      => 'wd',
        'naver'      => 'query',
        'ask'        => 'q',
        'ecosia'     => 'q',
        'startpage'  => 'query',
    ];

    private function classifyReferrer($host) {
        $host = strtolower((string) $host);
        if ($host === '') {
            return 'direct';
        }
        foreach (array_keys(self::$searchEngineQueryParams) as $engine) {
            if (preg_match('/(^|\\.)' . preg_quote($engine, '/') . '\\./', $host)) {
                return 'search';
            }
        }
        foreach (array_keys(self::$socialPlatformMap) as $slug) {
            $pattern = $slug === 'x.com'
                ? '/(^|\\.)x\\.com$/'
                : '/(^|\\.)' . preg_quote(explode('.', $slug)[0], '/') . '\\./';
            if (preg_match($pattern, $host)) {
                return 'social';
            }
        }
        return 'referral';
    }

    /**
     * Referrer URL'inden arama anahtar kelimesini çıkar.
     * Bilinmeyen motor için de 'q' parametresine bakar.
     */
    private function extractKeyword($referrerUrl) {
        if (!$referrerUrl) {
            return null;
        }
        $parsed = parse_url((string) $referrerUrl);
        if (empty($parsed['host']) || empty($parsed['query'])) {
            return null;
        }
        $host = strtolower($parsed['host']);
        parse_str($parsed['query'], $queryParams);

        foreach (self::$searchEngineQueryParams as $engine => $param) {
            if (preg_match('/(^|\\.)' . preg_quote($engine, '/') . '\\./', $host)) {
                $keyword = trim((string) ($queryParams[$param] ?? ''));
                return $keyword !== '' ? $keyword : null;
            }
        }
        // Bilinmeyen motor — genel 'q' parametresi dene
        $keyword = trim((string) ($queryParams['q'] ?? ''));
        return $keyword !== '' ? $keyword : null;
    }

    /**
     * Sosyal platform görünen adı ve meta bilgilerini döndür.
     */
    public static function getSocialPlatformMeta($host) {
        $host = strtolower((string) $host);
        foreach (self::$socialPlatformMap as $slug => $meta) {
            $pattern = $slug === 'x.com'
                ? '/(^|\\.)x\\.com$/'
                : '/(^|\\.)' . preg_quote(explode('.', $slug)[0], '/') . '\\./';
            if (preg_match($pattern, $host)) {
                return $meta; // [name, color, icon]
            }
        }
        return [$host, '#10b981', 'share-2'];
    }

    public function getWebsites($userId) {
        return $this->fetchAll(
            "SELECT w.id, w.name, w.domain, w.timezone,
                    CASE WHEN w.user_id = ? THEN 'owner' ELSE m.role END AS access_role
             FROM websites w
             LEFT JOIN website_members m ON m.website_id = w.id AND m.user_id = ?
             WHERE w.user_id = ? OR m.user_id = ?
             ORDER BY w.name",
            [$userId, $userId, $userId, $userId]
        );
    }

    public function getWebsite($websiteId, $userId) {
        return $this->fetchOne(
            "SELECT w.id, w.name, w.domain, w.timezone,
                    CASE WHEN w.user_id = ? THEN w.api_key ELSE NULL END AS api_key,
                    CASE WHEN w.user_id = ? THEN 'owner' ELSE m.role END AS access_role,
                    w.user_id AS owner_id
             FROM websites w
             LEFT JOIN website_members m ON m.website_id = w.id AND m.user_id = ?
             WHERE w.id = ? AND (w.user_id = ? OR m.user_id = ?)",
            [$userId, $userId, $userId, $websiteId, $userId, $userId]
        );
    }

    public function getActiveVisitors($websiteId, $userId) {
        if (!$websiteId || !$this->getWebsite($websiteId, $userId)) {
            return null;
        }

        $result = $this->fetchOne(
            "SELECT COUNT(DISTINCT visitor_id) AS active_now
             FROM page_views
             WHERE website_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
            [$websiteId]
        );

        return (int) ($result['active_now'] ?? 0);
    }

    public function getExportRows($websiteId, $userId, $type, $dateRange) {
        $website = $websiteId ? $this->getWebsite($websiteId, $userId) : null;
        if (!$website) {
            return null;
        }

        $timezone = new DateTimeZone($website['timezone'] ?: 'UTC');
        $utc = new DateTimeZone('UTC');
        $start = (new DateTimeImmutable($dateRange['start_date'] . ' 00:00:00', $timezone))->setTimezone($utc)->format('Y-m-d H:i:s');
        $end = (new DateTimeImmutable($dateRange['end_date'], $timezone))->modify('+1 day')->setTimezone($utc)->format('Y-m-d H:i:s');
        if ($type === 'pages') {
            return $this->fetchAll(
                "SELECT page_url, MAX(page_title) AS page_title, COUNT(*) AS views,
                        COUNT(DISTINCT visitor_id) AS unique_views
                 FROM page_views
                 WHERE website_id = ? AND created_at >= ? AND created_at < ?
                 GROUP BY page_url ORDER BY views DESC",
                [$websiteId, $start, $end]
            );
        }
        if ($type === 'visitors') {
            $rows = $this->fetchAll(
                "SELECT v.visitor_id, v.first_visit, MAX(pv.created_at) AS last_visit,
                        CASE WHEN v.first_visit >= ? AND v.first_visit < ?
                             THEN 'Yeni' ELSE 'Geri dönen' END AS visitor_type,
                        COUNT(pv.id) AS pageviews,
                        SUBSTRING_INDEX(GROUP_CONCAT(pv.page_url ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS last_page,
                        SUBSTRING_INDEX(GROUP_CONCAT(COALESCE(pv.city, '') ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS city,
                        SUBSTRING_INDEX(GROUP_CONCAT(COALESCE(pv.country, '') ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS country,
                        SUBSTRING_INDEX(GROUP_CONCAT(COALESCE(pv.device_type, '') ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS device_type
                 FROM page_views pv
                 INNER JOIN visitors v ON v.id = pv.visitor_id
                 WHERE pv.website_id = ? AND pv.created_at >= ? AND pv.created_at < ?
                 GROUP BY v.id, v.visitor_id, v.first_visit
                 ORDER BY last_visit DESC",
                [$start, $end, $websiteId, $start, $end]
            );
            foreach ($rows as &$row) {
                foreach (['first_visit', 'last_visit'] as $timestampColumn) {
                    if (!empty($row[$timestampColumn])) {
                        $row[$timestampColumn] = (new DateTimeImmutable($row[$timestampColumn], $utc))
                            ->setTimezone($timezone)->format('Y-m-d H:i:s');
                    }
                }
            }
            unset($row);
            return $rows;
        }
        if ($type === 'traffic') {
            return $this->load($websiteId, $userId, $dateRange)['trafficSources'];
        }

        throw new InvalidArgumentException('Unsupported analytics export type.');
    }

    public function load($websiteId, $userId, $dateRange = null) {
        $empty = [
            'summary' => [],
            'comparison' => [],
            'sessionSummary' => [],
            'trend' => [],
            'popularPages' => [],
            'pages' => [],
            'browserStats' => [],
            'osStats' => [],
            'deviceStats' => [],
            'hourlyStats' => [],
            'heatmap' => [],
            'activity' => [],
            'visitors' => [],
            'geoStats' => [],
            'visitorTypes' => [],
            'trafficSources' => [],
            'sourceCategories' => [],
            'socialPlatforms' => [],
            'referralSites' => [],
            'keywords' => [],
            'entryPages' => [],
            'exitPages' => [],
            'funnel' => []
        ];

        $website = $websiteId ? $this->getWebsite($websiteId, $userId) : null;
        if (!$website) {
            return $empty;
        }

        $timezoneName = $website['timezone'] ?: 'UTC';
        $timezoneSql = $timezoneName === 'UTC' ? '+00:00' : $timezoneName;
        $timezone = new DateTimeZone($timezoneName);
        $utc = new DateTimeZone('UTC');
        $timezoneAvailable = $this->fetchOne(
            "SELECT CONVERT_TZ(UTC_TIMESTAMP(), '+00:00', ?) AS local_time",
            [$timezoneSql]
        );
        if ($timezoneName !== 'UTC' && empty($timezoneAvailable['local_time'])) {
            throw new RuntimeException('MySQL saat dilimi tabloları yüklenmemiş. Site saat dilimini kullanmak için MySQL timezone tablolarını yükleyin.');
        }

        $dateRange = $dateRange ?: [
            'start_date' => (new DateTimeImmutable('today', $timezone))->modify('-29 days')->format('Y-m-d'),
            'end_date' => (new DateTimeImmutable('today', $timezone))->format('Y-m-d')
        ];
        $rangeStart = (new DateTimeImmutable($dateRange['start_date'] . ' 00:00:00', $timezone))->setTimezone($utc)->format('Y-m-d H:i:s');
        $rangeEnd = (new DateTimeImmutable($dateRange['end_date'], $timezone))->modify('+1 day')->setTimezone($utc)->format('Y-m-d H:i:s');
        $data = $empty;
        $data['summary'] = $this->fetchOne(
            "SELECT COUNT(*) AS pageviews,
                    COUNT(DISTINCT visitor_id) AS visitors,
                    COUNT(DISTINCT DATE(CONVERT_TZ(created_at, '+00:00', ?))) AS active_days,
                    COUNT(DISTINCT page_url) AS unique_pages,
                    COUNT(DISTINCT NULLIF(country, '')) AS countries
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?",
            [$timezoneSql, $websiteId, $rangeStart, $rangeEnd]
        );
        $rangeDays = (new DateTimeImmutable($dateRange['start_date'], $timezone))
            ->diff(new DateTimeImmutable($dateRange['end_date'], $timezone))->days + 1;
        $previousStart = (new DateTimeImmutable($dateRange['start_date'] . ' 00:00:00', $timezone))
            ->modify('-' . $rangeDays . ' days')->setTimezone($utc)->format('Y-m-d H:i:s');
        $previousEnd = $rangeStart;
        $data['comparison'] = $this->fetchOne(
            "SELECT COUNT(*) AS pageviews,
                    COUNT(DISTINCT visitor_id) AS visitors,
                    COUNT(DISTINCT DATE(CONVERT_TZ(created_at, '+00:00', ?))) AS active_days,
                    COUNT(DISTINCT page_url) AS unique_pages
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?",
            [$timezoneSql, $websiteId, $previousStart, $previousEnd]
        );
        $data['summary'] = array_merge($data['summary'], $this->fetchOne(
            "SELECT COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 1 END) AS views_hour,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 1 END) AS views_day,
                    COUNT(DISTINCT CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN visitor_id END) AS active_now
             FROM page_views WHERE website_id = ?",
            [$websiteId]
        ));
        $data['summary']['avg_page_duration'] = $this->fetchOne(
            "SELECT COALESCE(AVG(duration), 0) AS avg_duration
             FROM (
                 SELECT session_id, page_url, MAX(event_value) AS duration
                 FROM events
                 WHERE website_id = ? AND event_category = 'time-on-page'
                   AND event_value IS NOT NULL
                   AND created_at >= ? AND created_at < ?
                 GROUP BY session_id, page_url
             ) page_durations",
            [$websiteId, $rangeStart, $rangeEnd]
        )['avg_duration'] ?? 0;
        $data['sessionSummary'] = $this->fetchOne(
            "SELECT COUNT(*) AS sessions, COALESCE(AVG(duration), 0) AS avg_duration,
                    COALESCE(AVG(bounce) * 100, 0) AS bounce_rate,
                    COUNT(CASE WHEN page_count > 1 THEN 1 END) AS engaged_sessions
             FROM sessions
             WHERE website_id = ? AND start_time >= ? AND start_time < ?",
            [$websiteId, $rangeStart, $rangeEnd]
        );

        $trendRows = $this->fetchAll(
            "SELECT DATE(CONVERT_TZ(created_at, '+00:00', ?)) AS day, COUNT(*) AS views,
                    COUNT(DISTINCT visitor_id) AS visitors
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?
             GROUP BY DATE(CONVERT_TZ(created_at, '+00:00', ?)) ORDER BY day",
            [$timezoneSql, $websiteId, $rangeStart, $rangeEnd, $timezoneSql]
        );
        $trendByDay = [];
        foreach ($trendRows as $row) {
            $trendByDay[$row['day']] = $row;
        }
        $startDay = new DateTimeImmutable($dateRange['start_date'], $timezone);
        $endDay = new DateTimeImmutable($dateRange['end_date'], $timezone);
        if ($startDay->diff($endDay)->days > 365) {
            foreach ($trendRows as $row) {
                $data['trend'][] = [
                    'day' => $row['day'],
                    'views' => (int) $row['views'],
                    'visitors' => (int) $row['visitors']
                ];
            }
        } else {
            for ($day = $startDay; $day <= $endDay; $day = $day->modify('+1 day')) {
                $dayKey = $day->format('Y-m-d');
                $data['trend'][] = [
                    'day' => $dayKey,
                    'views' => (int) ($trendByDay[$dayKey]['views'] ?? 0),
                    'visitors' => (int) ($trendByDay[$dayKey]['visitors'] ?? 0)
                ];
            }
        }

        $data['popularPages'] = $this->fetchAll(
            "SELECT page_url, MAX(page_title) AS page_title, COUNT(*) AS views,
                    COUNT(DISTINCT visitor_id) AS unique_views
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?
             GROUP BY page_url ORDER BY views DESC LIMIT 10",
            [$websiteId, $rangeStart, $rangeEnd]
        );
        $data['pages'] = $this->fetchAll(
            "SELECT p.page_url, p.page_title, p.views, p.unique_views,
                    COALESCE(d.avg_duration, 0) AS avg_duration
             FROM (
                 SELECT page_url, MAX(page_title) AS page_title, COUNT(*) AS views,
                        COUNT(DISTINCT visitor_id) AS unique_views
                 FROM page_views
                 WHERE website_id = ? AND created_at >= ? AND created_at < ?
                 GROUP BY page_url
             ) p
             LEFT JOIN (
                 SELECT page_url, AVG(duration) AS avg_duration
                 FROM (
                     SELECT session_id, page_url, MAX(event_value) AS duration
                     FROM events
                     WHERE website_id = ? AND event_category = 'time-on-page'
                       AND event_value IS NOT NULL
                       AND created_at >= ? AND created_at < ?
                     GROUP BY session_id, page_url
                 ) page_durations
                 GROUP BY page_url
             ) d ON d.page_url = p.page_url
             ORDER BY p.views DESC LIMIT 100",
            [$websiteId, $rangeStart, $rangeEnd, $websiteId, $rangeStart, $rangeEnd]
        );

        foreach ([
            'browserStats' => ['browser', "browser IS NOT NULL AND browser != ''"],
            'osStats' => ['os', "os IS NOT NULL AND os != ''"],
            'deviceStats' => ['device_type', "device_type IS NOT NULL AND device_type != ''"]
        ] as $key => $definition) {
            $rows = $this->fetchAll(
                "SELECT {$definition[0]} AS label, COUNT(*) AS count
                 FROM page_views
                 WHERE website_id = ? AND created_at >= ? AND created_at < ?
                   AND {$definition[1]}
                 GROUP BY {$definition[0]} ORDER BY count DESC",
                [$websiteId, $rangeStart, $rangeEnd]
            );
            $data[$key] = $this->breakdown($rows, 'label');
        }

        $hourStart = (new DateTimeImmutable('now', $utc))->modify('-1 day')->format('Y-m-d H:i:s');
        $hourRows = $this->fetchAll(
            "SELECT HOUR(CONVERT_TZ(created_at, '+00:00', ?)) AS hour, COUNT(*) AS views
             FROM page_views
             WHERE website_id = ? AND created_at >= ?
             GROUP BY HOUR(CONVERT_TZ(created_at, '+00:00', ?))",
            [$timezoneSql, $websiteId, $hourStart, $timezoneSql]
        );
        $hours = array_fill(0, 24, 0);
        foreach ($hourRows as $row) {
            $hours[(int) $row['hour']] = (int) $row['views'];
        }
        for ($hour = 0; $hour < 24; $hour++) {
            $data['hourlyStats'][] = ['hour' => $hour, 'views' => $hours[$hour]];
        }

        $localToday = new DateTimeImmutable('today', $timezone);
        $heatStart = $localToday->modify('-6 days')->setTimezone($utc)->format('Y-m-d H:i:s');
        $heatEnd = $localToday->modify('+1 day')->setTimezone($utc)->format('Y-m-d H:i:s');
        $heatRows = $this->fetchAll(
            "SELECT DAYOFWEEK(CONVERT_TZ(created_at, '+00:00', ?)) AS weekday,
                    HOUR(CONVERT_TZ(created_at, '+00:00', ?)) AS hour, COUNT(*) AS views
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?
             GROUP BY DAYOFWEEK(CONVERT_TZ(created_at, '+00:00', ?)),
                      HOUR(CONVERT_TZ(created_at, '+00:00', ?))",
            [$timezoneSql, $timezoneSql, $websiteId, $heatStart, $heatEnd, $timezoneSql, $timezoneSql]
        );
        $dayNames = [1 => 'Paz', 2 => 'Pzt', 3 => 'Sal', 4 => 'Çar', 5 => 'Per', 6 => 'Cum', 7 => 'Cmt'];
        $heatValues = [];
        foreach ($heatRows as $row) {
            $heatValues[(int) $row['weekday']][(int) $row['hour']] = (int) $row['views'];
        }
        foreach ($dayNames as $day => $name) {
            $dayValues = [];
            for ($hour = 0; $hour < 24; $hour++) {
                $dayValues[] = $heatValues[$day][$hour] ?? 0;
            }
            $data['heatmap'][] = ['day' => $name, 'hours' => $dayValues];
        }

        $data['activity'] = $this->fetchAll(
            "SELECT page_url, city, country, created_at, device_type
             FROM page_views WHERE website_id = ?
               AND created_at >= ? AND created_at < ?
             ORDER BY created_at DESC LIMIT 8",
            [$websiteId, $rangeStart, $rangeEnd]
        );
        $data['visitors'] = $this->fetchAll(
            "SELECT v.visitor_id, v.first_visit, MAX(pv.created_at) AS last_visit,
                    CASE WHEN v.first_visit >= ? AND v.first_visit < ?
                         THEN 'Yeni' ELSE 'Geri dönen' END AS visitor_type,
                    COUNT(pv.id) AS pageviews,
                    SUBSTRING_INDEX(GROUP_CONCAT(pv.page_url ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS last_page,
                    SUBSTRING_INDEX(GROUP_CONCAT(COALESCE(pv.city, '') ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS city,
                    SUBSTRING_INDEX(GROUP_CONCAT(COALESCE(pv.country, '') ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS country,
                    SUBSTRING_INDEX(GROUP_CONCAT(COALESCE(pv.device_type, '') ORDER BY pv.created_at DESC SEPARATOR '||'), '||', 1) AS device_type
             FROM page_views pv
             INNER JOIN visitors v ON v.id = pv.visitor_id
             WHERE pv.website_id = ? AND pv.created_at >= ? AND pv.created_at < ?
             GROUP BY v.id, v.visitor_id, v.first_visit
             ORDER BY last_visit DESC LIMIT 100",
            [$rangeStart, $rangeEnd, $websiteId, $rangeStart, $rangeEnd]
        );

        $geoRows = $this->fetchAll(
            "SELECT COALESCE(NULLIF(country, ''), 'Bilinmiyor') AS label, COUNT(DISTINCT visitor_id) AS count
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?
             GROUP BY label ORDER BY count DESC",
            [$websiteId, $rangeStart, $rangeEnd]
        );
        $data['geoStats'] = $this->breakdown($geoRows, 'label');

        $visitorTypeRows = $this->fetchAll(
            "SELECT CASE WHEN v.first_visit >= ? AND v.first_visit < ?
                         THEN 'Yeni' ELSE 'Geri dönen' END AS label,
                    COUNT(DISTINCT pv.visitor_id) AS count
             FROM page_views pv
             INNER JOIN visitors v ON v.id = pv.visitor_id
             WHERE pv.website_id = ? AND pv.created_at >= ? AND pv.created_at < ?
             GROUP BY label",
            [$rangeStart, $rangeEnd, $websiteId, $rangeStart, $rangeEnd]
        );
        $data['visitorTypes'] = $this->breakdown($visitorTypeRows, 'label');

        $referrerRows = $this->fetchAll(
            "SELECT CASE
                        WHEN referrer_url IS NULL OR referrer_url = '' THEN ''
                        ELSE SUBSTRING_INDEX(
                            SUBSTRING_INDEX(
                                SUBSTRING_INDEX(referrer_url, '://', -1),
                                '/', 1
                            ),
                            '?', 1
                        )
                    END AS referrer_host,
                    COUNT(*) AS visits
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?
             GROUP BY referrer_host ORDER BY visits DESC",
            [$websiteId, $rangeStart, $rangeEnd]
        );
        $sourceCounts = [];
        $socialCounts = [];
        $referralCounts = [];
        $domain = strtolower((string) ($this->getWebsite($websiteId, $userId)['domain'] ?? ''));
        $domain = preg_replace('/^www\\./', '', $domain);
        foreach ($referrerRows as $row) {
            $host = strtolower(trim((string) $row['referrer_host']));
            $normalizedHost = preg_replace('/^www\\./', '', $host);
            if ($normalizedHost !== '' && $domain !== '' && ($normalizedHost === $domain || substr($normalizedHost, -strlen('.' . $domain)) === '.' . $domain)) {
                continue;
            }
            $category = $this->classifyReferrer($host);
            if ($category === 'direct') {
                $label = 'Doğrudan';
            } else {
                $label = $host;
            }
            $sourceCounts[$category][$label] = ($sourceCounts[$category][$label] ?? 0) + (int) $row['visits'];
            if ($category === 'social') {
                $socialCounts[$label] = ($socialCounts[$label] ?? 0) + (int) $row['visits'];
            } elseif ($category === 'referral') {
                $referralCounts[$label] = ($referralCounts[$label] ?? 0) + (int) $row['visits'];
            }
        }

        $sourceColors = ['search' => '#3b82f6', 'social' => '#10b981', 'direct' => '#f59e0b', 'referral' => '#f43f5e'];
        $categoryNames = ['search' => 'Arama', 'social' => 'Sosyal', 'direct' => 'Doğrudan', 'referral' => 'Yönlendirme'];
        $sourceTotal = 0;
        foreach ($sourceCounts as $sources) {
            $sourceTotal += array_sum($sources);
        }
        foreach ($sourceCounts as $category => $sources) {
            foreach ($sources as $label => $visits) {
                $data['trafficSources'][] = [
                    'source' => $label,
                    'category' => $category,
                    'category_label' => $categoryNames[$category],
                    'visits' => $visits,
                    'percentage' => $sourceTotal > 0 ? round($visits * 100 / $sourceTotal, 1) : 0,
                    'color' => $sourceColors[$category]
                ];
            }
        }
        usort($data['trafficSources'], function($a, $b) {
            return $b['visits'] <=> $a['visits'];
        });
        foreach ($categoryNames as $category => $label) {
            $count = array_sum($sourceCounts[$category] ?? []);
            $data['sourceCategories'][] = [
                'label' => $label,
                'count' => $count,
                'value' => $sourceTotal > 0 ? round($count * 100 / $sourceTotal, 1) : 0,
                'color' => $sourceColors[$category]
            ];
        }
        foreach ($socialCounts as $label => $visits) {
            $meta = self::getSocialPlatformMeta($label);
            $data['socialPlatforms'][] = [
                'platform' => $meta[0],   // Görünen ad (Facebook, Instagram vb.)
                'host'     => $label,     // Ham domain
                'visits'   => $visits,
                'percentage' => $sourceTotal > 0 ? round($visits * 100 / $sourceTotal, 1) : 0,
                'color'    => $meta[1],
                'icon'     => $meta[2]
            ];
        }
        foreach ($referralCounts as $label => $visits) {
            $data['referralSites'][] = ['domain' => $label, 'visits' => $visits];
        }
        usort($data['socialPlatforms'], function($a, $b) {
            return $b['visits'] <=> $a['visits'];
        });
        usort($data['referralSites'], function($a, $b) {
            return $b['visits'] <=> $a['visits'];
        });
        $data['socialPlatforms'] = array_slice($data['socialPlatforms'], 0, 8);
        $data['referralSites'] = array_slice($data['referralSites'], 0, 8);

        // Arama motoru referrer URL'lerinden anahtar kelimeleri çıkar
        $keywordReferrerRows = $this->fetchAll(
            "SELECT referrer_url, COUNT(*) AS visits
             FROM page_views
             WHERE website_id = ?
               AND created_at >= ? AND created_at < ?
               AND referrer_url IS NOT NULL AND referrer_url != ''
             GROUP BY referrer_url
             ORDER BY visits DESC
             LIMIT 500",
            [$websiteId, $rangeStart, $rangeEnd]
        );
        $keywordCounts = [];
        foreach ($keywordReferrerRows as $row) {
            $keyword = $this->extractKeyword($row['referrer_url']);
            if ($keyword !== null && $keyword !== '') {
                $lower = mb_strtolower($keyword);
                $keywordCounts[$lower] = ($keywordCounts[$lower] ?? 0) + (int) $row['visits'];
            }
        }
        arsort($keywordCounts);
        foreach (array_slice($keywordCounts, 0, 20, true) as $keyword => $visits) {
            $data['keywords'][] = ['keyword' => $keyword, 'visits' => $visits];
        }

        $flowRows = $this->fetchAll(
            "SELECT visitor_id, page_url,
                    MIN(created_at) AS first_seen, MAX(created_at) AS last_seen, COUNT(*) AS views
             FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?
             GROUP BY visitor_id, page_url",
            [$websiteId, $rangeStart, $rangeEnd]
        );
        $entryCounts = [];
        $exitCounts = [];
        $visitorFirst = [];
        $visitorLast = [];
        foreach ($flowRows as $row) {
            $visitor = (string) $row['visitor_id'];
            $url = (string) $row['page_url'];
            if (!isset($visitorFirst[$visitor]) || $row['first_seen'] < $visitorFirst[$visitor]['date']) {
                $visitorFirst[$visitor] = ['date' => $row['first_seen'], 'url' => $url];
            }
            if (!isset($visitorLast[$visitor]) || $row['last_seen'] > $visitorLast[$visitor]['date']) {
                $visitorLast[$visitor] = ['date' => $row['last_seen'], 'url' => $url];
            }
        }
        foreach ($visitorFirst as $item) {
            $entryCounts[$item['url']] = ($entryCounts[$item['url']] ?? 0) + 1;
        }
        foreach ($visitorLast as $item) {
            $exitCounts[$item['url']] = ($exitCounts[$item['url']] ?? 0) + 1;
        }
        arsort($entryCounts);
        arsort($exitCounts);
        foreach (array_slice($entryCounts, 0, 5, true) as $url => $visits) {
            $data['entryPages'][] = ['url' => $url, 'visits' => $visits];
        }
        foreach (array_slice($exitCounts, 0, 5, true) as $url => $visits) {
            $data['exitPages'][] = ['url' => $url, 'visits' => $visits];
        }

        $totalVisitors = (int) ($data['summary']['visitors'] ?? 0);
        $funnelValues = [
            ['label' => 'Benzersiz ziyaretçi', 'count' => $totalVisitors],
            ['label' => 'Sayfa görüntüleme', 'count' => (int) ($data['summary']['pageviews'] ?? 0)],
            ['label' => 'Oturum', 'count' => (int) ($data['sessionSummary']['sessions'] ?? 0)],
            ['label' => 'Etkileşimli oturum', 'count' => (int) ($data['sessionSummary']['engaged_sessions'] ?? 0)],
            ['label' => 'Ölçülen olay', 'count' => (int) $this->fetchOne(
                "SELECT COUNT(*) AS count FROM events
                 WHERE website_id = ? AND created_at >= ? AND created_at < ?",
                [$websiteId, $rangeStart, $rangeEnd]
            )['count']]
        ];
        foreach ($funnelValues as $index => $step) {
            $data['funnel'][] = $step + [
                'step' => $index + 1,
                'rate' => $totalVisitors > 0 ? round($step['count'] * 100 / $totalVisitors, 1) : 0
            ];
        }

        return $data;
    }
}
