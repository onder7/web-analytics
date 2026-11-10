<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';
require 'config/database.php';
require 'models/AppLogger.php';

$dryRun = !in_array('--execute', $argv, true) || in_array('--dry-run', $argv, true);
$batchSize = 500;
foreach ($argv as $argument) {
    if (strpos($argument, '--batch-size=') === 0) {
        $batchSize = filter_var(substr($argument, strlen('--batch-size=')), FILTER_VALIDATE_INT);
    }
}
if ($batchSize === false || $batchSize < 1 || $batchSize > 5000) {
    fwrite(STDERR, "Kullanım: php bin/run-maintenance.php [--dry-run] [--batch-size=1-5000]\n");
    exit(2);
}

$retentionTargets = [
    'page_views' => ['created_at', '12 MONTH'],
    'events' => ['created_at', '12 MONTH'],
    'sessions' => ['start_time', '12 MONTH'],
    'daily_stats' => ['date', '12 MONTH'],
    'api_requests' => ['created_at', '12 MONTH'],
    'system_logs' => ['created_at', '30 DAY']
];

try {
    $db = Database::getConnection();
    foreach ($retentionTargets as $table => $target) {
        $column = $target[0];
        $interval = $target[1];
        $countQuery = "SELECT COUNT(*) FROM `{$table}`
                       WHERE `{$column}` < DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$interval})";
        $eligible = (int) $db->query($countQuery)->fetchColumn();

        if ($dryRun) {
            fwrite(STDOUT, "{$table}: silinmeye uygun {$eligible} kayıt (dry-run)\n");
            continue;
        }

        $deleteQuery = "DELETE FROM `{$table}`
                        WHERE `{$column}` < DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$interval})
                        LIMIT {$batchSize}";
        $statement = $db->query($deleteQuery);
        fwrite(STDOUT, "{$table}: bu çalıştırmada silinen " . $statement->rowCount() . " kayıt; uygun toplam {$eligible}\n");
    }

    $cutoff = gmdate('Y-m-d', strtotime('-30 days'));
    $logDirectories = array_unique([
        AppLogger::getLogDirectory(),
        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs'
    ]);
    $removedFiles = 0;
    foreach ($logDirectories as $logDirectory) {
        foreach (glob($logDirectory . DIRECTORY_SEPARATOR . 'app-*.log') ?: [] as $file) {
            if (!preg_match('/app-(\d{4}-\d{2}-\d{2})\.log$/', basename($file), $matches)
                || $matches[1] >= $cutoff) {
                continue;
            }

            if ($dryRun) {
                $removedFiles++;
                continue;
            }
            if (!unlink($file)) {
                throw new RuntimeException('Eski uygulama günlük dosyası silinemedi: ' . basename($file));
            }
            $removedFiles++;
        }
    }

    fwrite(
        STDOUT,
        '30 günden eski günlük dosyaları: ' . $removedFiles . ($dryRun ? ' (dry-run)' : ' silindi') . "\n"
    );
    if (!$dryRun) {
        AppLogger::info('maintenance', 'Günlük veri saklama temizliği tamamlandı.');
    }
} catch (Throwable $error) {
    fwrite(STDERR, 'Bakım işlemi başarısız: ' . $error->getMessage() . "\n");
    AppLogger::error('maintenance', $error->getMessage());
    exit(1);
}
