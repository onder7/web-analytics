<?php
class SystemDiagnostics {
    private $db;

    private const RETENTION_TABLES = [
        'page_views' => ['column' => 'created_at', 'label' => 'Sayfa görüntülemeleri'],
        'events' => ['column' => 'created_at', 'label' => 'Olaylar'],
        'sessions' => ['column' => 'start_time', 'label' => 'Oturumlar'],
        'daily_stats' => ['column' => 'date', 'label' => 'Günlük özetler'],
        'api_requests' => ['column' => 'created_at', 'label' => 'API istekleri'],
        'system_logs' => ['column' => 'created_at', 'label' => 'Sistem kayıtları']
    ];

    private const OPTIMIZABLE_TABLES = [
        'api_requests',
        'daily_stats',
        'events',
        'page_views',
        'sessions',
        'visitors'
    ];

    private const REQUIRED_INDEXES = [
        'page_views' => ['idx_website_visitor_created', 'website_id, visitor_id, created_at'],
        'events' => ['idx_website_category_created', 'website_id, event_category, created_at'],
        'sessions' => ['idx_website_start_time', 'website_id, start_time']
    ];

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public static function isAdmin($userId) {
        try {
            $statement = Database::getConnection()->prepare('SELECT is_admin FROM users WHERE id = ?');
            $statement->execute([(int) $userId]);
            return (int) $statement->fetchColumn() === 1;
        } catch (PDOException $error) {
            AppLogger::error('system-admin', 'Admin yetkisi doğrulanamadı: ' . $error->getMessage());
            return false;
        }
    }

    public function getOverview($severity = '') {
        $severity = in_array($severity, ['error', 'warning', 'info'], true) ? $severity : '';
        $database = $this->db->query(
            'SELECT DATABASE() AS name, VERSION() AS version'
        )->fetch();
        $tablesStatement = $this->db->prepare(
            'SELECT table_name, engine, table_rows, data_length, index_length
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
             ORDER BY data_length + index_length DESC'
        );
        $tablesStatement->execute();
        $tables = array_map(function($table) {
            return array_change_key_case($table, CASE_LOWER);
        }, $tablesStatement->fetchAll());

        $indexStatement = $this->db->prepare(
            'SELECT table_name, index_name
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()'
        );
        $indexStatement->execute();
        $availableIndexes = [];
        foreach ($indexStatement->fetchAll() as $index) {
            $index = array_change_key_case($index, CASE_LOWER);
            $availableIndexes[$index['table_name']][$index['index_name']] = true;
        }

        $requiredIndexes = [];
        foreach (self::REQUIRED_INDEXES as $table => $index) {
            $requiredIndexes[] = [
                'table' => $table,
                'name' => $index[0],
                'columns' => $index[1],
                'available' => isset($availableIndexes[$table][$index[0]])
            ];
        }

        $expiredCounts = [];
        foreach (self::RETENTION_TABLES as $table => $definition) {
            if (!isset($availableIndexes[$table])) {
                continue;
            }
            $column = $definition['column'];
            $cutoff = $table === 'system_logs'
                ? 'DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 DAY)'
                : 'DATE_SUB(UTC_TIMESTAMP(), INTERVAL 12 MONTH)';
            $statement = $this->db->query(
                "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` < {$cutoff}"
            );
            $expiredCounts[] = [
                'table' => $table,
                'label' => $definition['label'],
                'count' => (int) $statement->fetchColumn(),
                'retention' => $table === 'system_logs' ? '30 gün' : '12 ay'
            ];
        }

        return [
            'database' => $database,
            'tables' => $tables,
            'requiredIndexes' => $requiredIndexes,
            'expiredCounts' => $expiredCounts,
            'logs' => $this->getRecentLogs($severity),
            'logFilter' => $severity,
            'logDirectory' => AppLogger::getLogDirectory(),
            'phpLogName' => $this->getPhpLogName(),
            'logTableAvailable' => isset($availableIndexes['system_logs']),
            'flash' => $_SESSION['maintenance_flash'] ?? null,
            'csrfToken' => $this->getCsrfToken()
        ];
    }

    public function optimizeTable($table) {
        if (!in_array($table, self::OPTIMIZABLE_TABLES, true)) {
            throw new InvalidArgumentException('Seçilen tablo optimize edilemiyor.');
        }

        $statement = $this->db->prepare(
            'SELECT engine FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $statement->execute([$table]);
        $engine = $statement->fetchColumn();
        if ($engine === false) {
            throw new InvalidArgumentException('Tablo bulunamadı.');
        }
        if (strtoupper((string) $engine) !== 'MYISAM') {
            throw new InvalidArgumentException('Yalnızca MyISAM tabloları bu ekrandan optimize edilebilir.');
        }

        $result = $this->db->query("OPTIMIZE TABLE `{$table}`")->fetch();
        if (($result['Msg_type'] ?? '') === 'Error') {
            throw new RuntimeException('MySQL tablo optimizasyonu başarısız oldu.');
        }

        return $result['Msg_text'] ?? 'Tablo optimize edildi.';
    }

    private function getRecentLogs($severity) {
        $logs = [];
        if ($this->tableExists('system_logs')) {
            $sql = 'SELECT created_at, severity, source, message FROM system_logs
                    WHERE created_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 DAY)';
            if ($severity !== '') {
                $sql .= ' AND severity = ?';
            }
            $sql .= ' ORDER BY id DESC LIMIT 100';
            $statement = $this->db->prepare($sql);
            $statement->execute($severity !== '' ? [$severity] : []);
            $logs = $statement->fetchAll();
        }

        foreach ($this->readRecentFileLogs() as $entry) {
            if ($severity !== '' && $entry['severity'] !== $severity) {
                continue;
            }
            $logs[] = $entry;
        }
        foreach ($this->readPhpErrorLog() as $entry) {
            if ($severity !== '' && $entry['severity'] !== $severity) {
                continue;
            }
            $logs[] = $entry;
        }

        $unique = [];
        foreach ($logs as $entry) {
            $key = implode('|', [
                $entry['created_at'] ?? '',
                $entry['severity'] ?? '',
                $entry['source'] ?? '',
                $entry['message'] ?? ''
            ]);
            $unique[$key] = $entry;
        }
        $logs = array_values($unique);
        usort($logs, function($left, $right) {
            return strcmp((string) ($right['created_at'] ?? ''), (string) ($left['created_at'] ?? ''));
        });

        return array_slice($logs, 0, 100);
    }

    private function readRecentFileLogs() {
        $cutoff = gmdate('Y-m-d', strtotime('-30 days'));
        $logs = [];
        $bytesRead = 0;
        $directories = array_unique([
            AppLogger::getLogDirectory(),
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs'
        ]);

        foreach ($directories as $directory) {
            if (!is_dir($directory) || $bytesRead >= 1048576) {
                continue;
            }
            $files = glob($directory . DIRECTORY_SEPARATOR . 'app-*.log') ?: [];
            rsort($files, SORT_STRING);

            foreach ($files as $file) {
                if (!preg_match('/app-(\d{4}-\d{2}-\d{2})\.log$/', basename($file), $matches)
                    || $matches[1] < $cutoff) {
                    continue;
                }

                $size = filesize($file);
                if ($size === false || $size <= 0 || $bytesRead >= 1048576) {
                    continue;
                }
                $handle = fopen($file, 'rb');
                if ($handle === false) {
                    continue;
                }
                $readLength = min(262144, $size, 1048576 - $bytesRead);
                if ($size > $readLength) {
                    fseek($handle, -$readLength, SEEK_END);
                }
                $content = fread($handle, $readLength);
                fclose($handle);
                if (!is_string($content)) {
                    continue;
                }
                $bytesRead += strlen($content);
                $lines = preg_split('/\r?\n/', $content);

                foreach ($lines as $line) {
                    $entry = json_decode($line, true);
                    if (!is_array($entry) || empty($entry['created_at']) || empty($entry['severity'])) {
                        continue;
                    }
                    $logs[] = $entry;
                }
            }
        }

        return $logs;
    }

    private function getPhpLogName() {
        $path = $this->getPhpLogPath();
        return $path === null ? null : basename($path);
    }

    private function getPhpLogPath() {
        $configuredPath = trim((string) ini_get('error_log'));
        if ($configuredPath === '' || in_array(strtolower($configuredPath), ['syslog', 'stderr'], true)
            || strpos($configuredPath, 'php://') === 0) {
            return null;
        }

        $path = realpath($configuredPath);
        return $path !== false && is_file($path) && is_readable($path) ? $path : null;
    }

    private function readPhpErrorLog() {
        $path = $this->getPhpLogPath();
        if ($path === null) {
            return [];
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return [];
        }
        $size = filesize($path);
        if ($size === false || $size <= 0) {
            fclose($handle);
            return [];
        }
        $readLength = min(262144, $size);
        if ($size > $readLength) {
            fseek($handle, -$readLength, SEEK_END);
        }
        $content = fread($handle, $readLength);
        fclose($handle);
        if (!is_string($content)) {
            return [];
        }

        $fallbackTime = gmdate('Y-m-d H:i:s', (int) filemtime($path));
        $cutoff = gmdate('Y-m-d H:i:s', strtotime('-30 days'));
        $entries = [];
        foreach (array_slice(preg_split('/\r?\n/', $content), -100) as $line) {
            if (trim($line) === '') {
                continue;
            }
            $createdAt = $fallbackTime;
            $message = $line;
            if (preg_match('/^\[([^\]]+)\]\s*(.*)$/', $line, $matches)) {
                $parsedTime = strtotime($matches[1]);
                if ($parsedTime !== false) {
                    $createdAt = gmdate('Y-m-d H:i:s', $parsedTime);
                }
                $message = $matches[2];
            }
            if ($createdAt < $cutoff) {
                continue;
            }
            $severity = preg_match('/\b(fatal|parse|uncaught|error)\b/i', $message)
                ? 'error'
                : (preg_match('/\bwarning\b/i', $message) ? 'warning' : 'info');
            $entries[] = [
                'created_at' => $createdAt,
                'severity' => $severity,
                'source' => 'php-error-log',
                'message' => AppLogger::redact(substr($message, 0, 6000))
            ];
        }

        return $entries;
    }

    private function tableExists($table) {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $statement->execute([$table]);
        return (int) $statement->fetchColumn() > 0;
    }

    private function getCsrfToken() {
        if (empty($_SESSION['maintenance_csrf'])) {
            $_SESSION['maintenance_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['maintenance_csrf'];
    }
}
