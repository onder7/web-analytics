<?php
class NotificationService {
    private $db;
    private $settings;
    private $mailer;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->settings = new NotificationSettings();
        $this->mailer = new SmtpMailer();
        $this->ensureTables();
    }

    private function ensureTables() {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS notification_deliveries (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                notification_type VARCHAR(32) NOT NULL,
                period_key VARCHAR(100) NOT NULL,
                website_id INT NOT NULL DEFAULT 0,
                payload MEDIUMTEXT NOT NULL,
                status VARCHAR(16) NOT NULL DEFAULT 'pending',
                attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                available_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                sent_at DATETIME NULL,
                last_error VARCHAR(1000) NULL,
                UNIQUE KEY unique_notification_delivery (user_id, notification_type, period_key, website_id),
                KEY idx_notification_due (status, available_at, id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    public function run($limit = 50) {
        $scheduled = $this->schedule();
        $sent = $this->processPending($limit);
        return ['scheduled' => $scheduled, 'sent' => $sent];
    }

    public function queueTrackingError($websiteId) {
        $websiteQuery = $this->db->prepare('SELECT user_id, domain FROM websites WHERE id = ?');
        $websiteQuery->execute([(int) $websiteId]);
        $website = $websiteQuery->fetch();
        if (!$website || empty($website['user_id'])) {
            return false;
        }

        $settings = $this->settings->getForUser((int) $website['user_id']);
        if (empty($settings['enabled']) || empty($settings['tracking_errors'])) {
            return false;
        }

        $hour = date('Y-m-d H:00:00');
        return $this->enqueue(
            (int) $website['user_id'],
            'tracking_errors',
            $hour . ':' . (int) $websiteId,
            (int) $websiteId,
            [
                'subject' => 'İzleme isteğinde sunucu hatası',
                'body' => "Web Analitik izleme endpoint'i bir sunucu hatası döndürdü.\n"
                    . 'Site: ' . $website['domain'] . "\n"
                    . 'Zaman aralığı: ' . $hour . "\n"
                    . "Ayrıntılar güvenlik nedeniyle e-postaya eklenmedi; sunucu hata günlüklerini kontrol edin."
            ]
        );
    }

    public function schedule(?DateTimeImmutable $now = null) {
        $rows = $this->db->query(
            'SELECT user_id, daily_summary, weekly_summary, monthly_summary, traffic_anomaly
             FROM notification_settings
             WHERE enabled = 1 AND recipient_email <> "" AND smtp_host <> "" AND from_email <> ""'
        )->fetchAll();
        $now = $now ?: new DateTimeImmutable('now');
        $today = $now->setTime(0, 0, 0);
        $scheduled = 0;

        foreach ($rows as $settings) {
            $userId = (int) $settings['user_id'];
            if (!empty($settings['daily_summary'])) {
                $scheduled += $this->scheduleSummary($userId, 'daily_summary', $today->modify('-1 day'), $today);
            }
            if (!empty($settings['weekly_summary']) && $now->format('N') === '1') {
                $weekStart = $today->modify('-7 days');
                $scheduled += $this->scheduleSummary($userId, 'weekly_summary', $weekStart, $today);
            }
            if (!empty($settings['monthly_summary']) && $now->format('j') === '1') {
                $monthStart = $today->modify('first day of last month');
                $scheduled += $this->scheduleSummary($userId, 'monthly_summary', $monthStart, $today);
            }
            if (!empty($settings['traffic_anomaly'])) {
                $scheduled += $this->scheduleAnomalies($userId, $now);
            }
        }

        return $scheduled;
    }

    private function scheduleSummary($userId, $type, DateTimeImmutable $start, DateTimeImmutable $end) {
        $websitesQuery = $this->db->prepare('SELECT id, name, domain FROM websites WHERE user_id = ? ORDER BY name');
        $websitesQuery->execute([$userId]);
        $websites = $websitesQuery->fetchAll();
        if (!$websites) {
            return 0;
        }

        $startAt = $start->format('Y-m-d H:i:s');
        $endAt = $end->format('Y-m-d H:i:s');
        $lines = [
            'Rapor dönemi: ' . $start->format('Y-m-d') . ' - ' . $end->modify('-1 second')->format('Y-m-d'),
            ''
        ];
        foreach ($websites as $website) {
            $metrics = $this->summaryMetrics((int) $website['id'], $startAt, $endAt);
            $lines[] = $website['name'] . ' (' . $website['domain'] . ')';
            $lines[] = 'Sayfa görüntüleme: ' . $metrics['pageviews'];
            $lines[] = 'Tekil ziyaretçi: ' . $metrics['visitors'];
            $lines[] = 'Oturum: ' . $metrics['sessions'];
            if ($metrics['top_pages']) {
                $lines[] = 'En çok görüntülenen sayfalar:';
                foreach ($metrics['top_pages'] as $page) {
                    $lines[] = '  - ' . $page['page_url'] . ' (' . $page['views'] . ')';
                }
            }
            $lines[] = '';
        }

        $period = $start->format('Y-m-d');
        $label = [
            'daily_summary' => 'Günlük',
            'weekly_summary' => 'Haftalık',
            'monthly_summary' => 'Aylık'
        ][$type];
        return $this->enqueue(
            $userId,
            $type,
            $period,
            0,
            ['subject' => $label . ' web analitik özeti', 'body' => implode("\n", $lines)]
        ) ? 1 : 0;
    }

    private function summaryMetrics($websiteId, $startAt, $endAt) {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) AS pageviews, COUNT(DISTINCT visitor_id) AS visitors,
                    COUNT(DISTINCT session_id) AS sessions
             FROM page_views WHERE website_id = ? AND created_at >= ? AND created_at < ?'
        );
        $statement->execute([$websiteId, $startAt, $endAt]);
        $metrics = $statement->fetch();
        $topPages = $this->db->prepare(
            'SELECT page_url, COUNT(*) AS views FROM page_views
             WHERE website_id = ? AND created_at >= ? AND created_at < ?
             GROUP BY page_url ORDER BY views DESC LIMIT 5'
        );
        $topPages->execute([$websiteId, $startAt, $endAt]);
        $metrics['top_pages'] = $topPages->fetchAll();
        return $metrics;
    }

    private function scheduleAnomalies($userId, DateTimeImmutable $now) {
        $websitesQuery = $this->db->prepare('SELECT id, name, domain FROM websites WHERE user_id = ? ORDER BY id');
        $websitesQuery->execute([$userId]);
        $websites = $websitesQuery->fetchAll();
        $hourEnd = $now->setTime((int) $now->format('G'), 0, 0);
        $hourStart = $hourEnd->modify('-1 hour');
        $baselineStart = $hourStart->modify('-24 hours');
        $currentStart = $hourStart->format('Y-m-d H:i:s');
        $currentEnd = $hourEnd->format('Y-m-d H:i:s');
        $baseline = $this->db->prepare(
            'SELECT COUNT(*) FROM page_views WHERE website_id = ? AND created_at >= ? AND created_at < ?'
        );
        $current = $this->db->prepare(
            'SELECT COUNT(*) FROM page_views WHERE website_id = ? AND created_at >= ? AND created_at < ?'
        );
        $scheduled = 0;

        foreach ($websites as $website) {
            $websiteId = (int) $website['id'];
            $baseline->execute([
                $websiteId,
                $baselineStart->format('Y-m-d H:i:s'),
                $currentStart
            ]);
            $average = (int) $baseline->fetchColumn() / 24;
            $current->execute([$websiteId, $currentStart, $currentEnd]);
            $currentViews = (int) $current->fetchColumn();

            if ($currentViews >= max(10, $average * 2)) {
                $direction = 'artış';
            } elseif ($average >= 10 && $currentViews <= $average / 2) {
                $direction = 'düşüş';
            } else {
                continue;
            }

            $scheduled += $this->enqueue(
                $userId,
                'traffic_anomaly',
                $hourStart->format('Y-m-d H:00:00'),
                $websiteId,
                [
                    'subject' => 'Trafik ' . $direction . ' uyarısı: ' . $website['domain'],
                    'body' => $website['name'] . ' (' . $website['domain'] . ") sitesinde olağan dışı trafik {$direction}i algılandı.\n"
                        . 'Saat: ' . $hourStart->format('Y-m-d H:00') . "\n"
                        . 'Son saatteki görüntüleme: ' . $currentViews . "\n"
                        . 'Önceki 24 saatin saatlik ortalaması: ' . round($average, 1)
                ]
            ) ? 1 : 0;
        }

        return $scheduled;
    }

    private function enqueue($userId, $type, $periodKey, $websiteId, array $payload) {
        $statement = $this->db->prepare(
            'INSERT IGNORE INTO notification_deliveries
                (user_id, notification_type, period_key, website_id, payload)
             VALUES (?, ?, ?, ?, ?)'
        );
        $statement->execute([
            (int) $userId,
            $type,
            $periodKey,
            (int) $websiteId,
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR)
        ]);
        return $statement->rowCount() > 0;
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
                $settings = $this->settings->getForSending((int) $job['user_id']);
                if (empty($settings['enabled'])) {
                    $this->finishJob($job['id'], 'cancelled');
                    continue;
                }
                $payload = json_decode($job['payload'], true, 512, JSON_THROW_ON_ERROR);
                if (!isset($payload['subject'], $payload['body']) ||
                    !is_string($payload['subject']) || !is_string($payload['body'])) {
                    throw new RuntimeException('Kuyruktaki bildirim içeriği geçersiz.');
                }

                $this->mailer->send($settings, $payload['subject'], $payload['body']);
                $this->finishJob($job['id'], 'sent');
            } catch (Throwable $error) {
                $this->retryJob($job, $error);
            }
        }

        return $processed;
    }

    private function claimJob() {
        $this->db->beginTransaction();
        try {
            $job = $this->db->query(
                "SELECT id, user_id, payload, attempts FROM notification_deliveries
                 WHERE status = 'pending' AND available_at <= NOW()
                 ORDER BY id LIMIT 1 FOR UPDATE"
            )->fetch();
            if (!$job) {
                $this->db->commit();
                return null;
            }

            $claim = $this->db->prepare(
                "UPDATE notification_deliveries
                 SET available_at = DATE_ADD(NOW(), INTERVAL 10 MINUTE)
                 WHERE id = ? AND status = 'pending'"
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

    private function finishJob($id, $status) {
        $statement = $this->db->prepare(
            'UPDATE notification_deliveries SET status = ?, sent_at = NOW(), last_error = NULL WHERE id = ?'
        );
        $statement->execute([$status, (int) $id]);
    }

    private function retryJob(array $job, Throwable $error) {
        $attempts = (int) $job['attempts'] + 1;
        $status = $attempts >= 5 ? 'failed' : 'pending';
        $delayMinutes = min(60, 2 ** $attempts);
        $statement = $this->db->prepare(
            'UPDATE notification_deliveries
             SET status = ?, attempts = ?, available_at = DATE_ADD(NOW(), INTERVAL ? MINUTE),
                 last_error = ? WHERE id = ?'
        );
        $statement->execute([
            $status,
            $attempts,
            $delayMinutes,
            substr($error->getMessage(), 0, 1000),
            (int) $job['id']
        ]);
        AppLogger::error('notification-worker', 'İş ' . (int) $job['id'] . ': ' . $error->getMessage());
    }
}
