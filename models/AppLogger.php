<?php
class AppLogger {
    private static $logTableAvailable = null;

    public static function error($source, $message) {
        self::write('error', $source, $message);
    }

    public static function warning($source, $message) {
        self::write('warning', $source, $message);
    }

    public static function info($source, $message) {
        self::write('info', $source, $message);
    }

    private static function write($severity, $source, $message) {
        $entry = [
            'created_at' => gmdate('Y-m-d H:i:s'),
            'severity' => $severity,
            'source' => substr((string) $source, 0, 100),
            'message' => self::redact(substr((string) $message, 0, 6000))
        ];
        $line = json_encode(
            $entry,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
        $directory = self::getLogDirectory();

        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            error_log('Application log directory could not be created.');
            return;
        }

        $file = $directory . DIRECTORY_SEPARATOR . 'app-' . gmdate('Y-m-d') . '.log';
        if (file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            error_log('Application log file could not be written.');
        }

        try {
            $db = Database::getConnection();
            if (self::$logTableAvailable === null) {
                $statement = $db->prepare(
                    "SELECT COUNT(*) FROM information_schema.tables
                     WHERE table_schema = DATABASE() AND table_name = 'system_logs'"
                );
                $statement->execute();
                self::$logTableAvailable = (int) $statement->fetchColumn() > 0;
            }

            if (self::$logTableAvailable) {
                $statement = $db->prepare(
                    'INSERT INTO system_logs (severity, source, message, created_at) VALUES (?, ?, ?, ?)'
                );
                $statement->execute([
                    $entry['severity'],
                    $entry['source'],
                    $entry['message'],
                    $entry['created_at']
                ]);
            }
        } catch (Throwable $error) {
            error_log('System log database write failed: ' . $error->getMessage());
        }
    }

    public static function getLogDirectory() {
        $configuredDirectory = getenv('APP_LOG_DIRECTORY');
        if (is_string($configuredDirectory) && trim($configuredDirectory) !== '') {
            return rtrim($configuredDirectory, '\\/');
        }

        $projectRoot = dirname(__DIR__);
        return dirname($projectRoot) . DIRECTORY_SEPARATOR . basename($projectRoot) . '-logs';
    }

    public static function redact($message) {
        return preg_replace(
            '/\b(password|passwd|api[_-]?key|token|secret)\b\s*[:=]\s*([^\s,;]+)/i',
            '$1=[REDACTED]',
            $message
        );
    }
}
