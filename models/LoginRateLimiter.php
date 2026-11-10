<?php
class LoginRateLimiter {
    private $db;
    private $windowMinutes = 15;
    private $ipLimit = 20;
    private $pairLimit = 5;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getRetryAfter($ip, $email) {
        $retryAfter = 0;
        foreach ($this->keys($ip, $email) as $key) {
            $statement = $this->db->prepare(
                'SELECT GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), locked_until)) AS retry_after
                 FROM login_attempts WHERE bucket_key = ?'
            );
            $statement->execute([$key['hash']]);
            $retryAfter = max($retryAfter, (int) $statement->fetchColumn());
        }
        return $retryAfter;
    }

    public function recordFailure($ip, $email) {
        foreach ($this->keys($ip, $email) as $key) {
            $this->recordBucketFailure($key);
        }
    }

    public function clear($ip, $email) {
        $statement = $this->db->prepare('DELETE FROM login_attempts WHERE bucket_key = ?');
        foreach ($this->keys($ip, $email) as $key) {
            $statement->execute([$key['hash']]);
        }
    }

    private function keys($ip, $email) {
        $ip = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'unknown';
        $normalizedEmail = strtolower(trim((string) $email));
        return [
            [
                'hash' => hash('sha256', 'ip:' . $ip),
                'limit' => $this->ipLimit
            ],
            [
                'hash' => hash('sha256', 'pair:' . $ip . ':' . $normalizedEmail),
                'limit' => $this->pairLimit
            ]
        ];
    }

    private function recordBucketFailure(array $key) {
        $this->db->beginTransaction();
        try {
            $insert = $this->db->prepare(
                'INSERT IGNORE INTO login_attempts (bucket_key, failed_attempts, window_started_at)
                 VALUES (?, 0, NOW())'
            );
            $insert->execute([$key['hash']]);

            $select = $this->db->prepare(
                'SELECT failed_attempts, window_started_at,
                        window_started_at >= DATE_SUB(NOW(), INTERVAL ' . $this->windowMinutes . ' MINUTE) AS within_window
                 FROM login_attempts WHERE bucket_key = ? FOR UPDATE'
            );
            $select->execute([$key['hash']]);
            $row = $select->fetch();
            if (!$row) {
                throw new RuntimeException('Giriş denemesi sayacı okunamadı.');
            }

            $withinWindow = (bool) $row['within_window'];
            $attempts = $withinWindow ? (int) $row['failed_attempts'] + 1 : 1;
            $update = $this->db->prepare(
                'UPDATE login_attempts SET
                    failed_attempts = ?,
                    window_started_at = ' . ($withinWindow ? 'window_started_at' : 'NOW()') . ',
                    locked_until = ' . ($attempts >= $key['limit']
                    ? 'DATE_ADD(NOW(), INTERVAL ' . $this->windowMinutes . ' MINUTE)'
                    : 'NULL') . '
                 WHERE bucket_key = ?'
            );
            $update->execute([$attempts, $key['hash']]);
            $this->db->commit();
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }
}
