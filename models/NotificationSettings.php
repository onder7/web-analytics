<?php
class NotificationSettings {
    private $db;
    private static $tableReady = false;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->ensureTable();
    }

    private function ensureTable() {
        if (self::$tableReady) {
            return;
        }

        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS notification_settings (
                user_id INT NOT NULL PRIMARY KEY,
                recipient_email VARCHAR(191) NOT NULL DEFAULT '',
                enabled TINYINT(1) NOT NULL DEFAULT 0,
                daily_summary TINYINT(1) NOT NULL DEFAULT 0,
                weekly_summary TINYINT(1) NOT NULL DEFAULT 0,
                monthly_summary TINYINT(1) NOT NULL DEFAULT 0,
                traffic_anomaly TINYINT(1) NOT NULL DEFAULT 0,
                tracking_errors TINYINT(1) NOT NULL DEFAULT 0,
                smtp_host VARCHAR(255) NOT NULL DEFAULT '',
                smtp_port SMALLINT UNSIGNED NOT NULL DEFAULT 587,
                smtp_encryption VARCHAR(8) NOT NULL DEFAULT 'tls',
                smtp_username VARCHAR(191) NOT NULL DEFAULT '',
                smtp_password_encrypted TEXT NULL,
                from_email VARCHAR(191) NOT NULL DEFAULT '',
                from_name VARCHAR(191) NOT NULL DEFAULT '',
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        self::$tableReady = true;
    }

    public function getForUser($userId) {
        $statement = $this->db->prepare('SELECT * FROM notification_settings WHERE user_id = ?');
        $statement->execute([(int) $userId]);
        $settings = $statement->fetch();

        if (!$settings) {
            return $this->defaults();
        }

        $settings['has_smtp_password'] = !empty($settings['smtp_password_encrypted']);
        unset($settings['smtp_password_encrypted']);
        return $settings;
    }

    public function getForSending($userId) {
        $statement = $this->db->prepare('SELECT * FROM notification_settings WHERE user_id = ?');
        $statement->execute([(int) $userId]);
        $settings = $statement->fetch();
        if (!$settings) {
            return array_merge($this->defaults(), ['smtp_password' => '']);
        }

        $settings['smtp_password'] = empty($settings['smtp_password_encrypted'])
            ? ''
            : $this->decrypt($settings['smtp_password_encrypted']);
        unset($settings['smtp_password_encrypted']);
        return $settings;
    }

    public function saveForUser($userId, array $settings, $smtpPassword, $clearSmtpPassword) {
        $existingStatement = $this->db->prepare('SELECT smtp_password_encrypted FROM notification_settings WHERE user_id = ?');
        $existingStatement->execute([(int) $userId]);
        $existing = $existingStatement->fetch();
        $encryptedPassword = $existing ? $existing['smtp_password_encrypted'] : null;

        if ($clearSmtpPassword) {
            $encryptedPassword = null;
        } elseif ($smtpPassword !== '') {
            $encryptedPassword = $this->encrypt($smtpPassword);
        }

        $statement = $this->db->prepare(
            'INSERT INTO notification_settings (
                user_id, recipient_email, enabled, daily_summary, weekly_summary, monthly_summary,
                traffic_anomaly, tracking_errors, smtp_host, smtp_port, smtp_encryption,
                smtp_username, smtp_password_encrypted, from_email, from_name
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                recipient_email = VALUES(recipient_email),
                enabled = VALUES(enabled),
                daily_summary = VALUES(daily_summary),
                weekly_summary = VALUES(weekly_summary),
                monthly_summary = VALUES(monthly_summary),
                traffic_anomaly = VALUES(traffic_anomaly),
                tracking_errors = VALUES(tracking_errors),
                smtp_host = VALUES(smtp_host),
                smtp_port = VALUES(smtp_port),
                smtp_encryption = VALUES(smtp_encryption),
                smtp_username = VALUES(smtp_username),
                smtp_password_encrypted = VALUES(smtp_password_encrypted),
                from_email = VALUES(from_email),
                from_name = VALUES(from_name)'
        );
        $statement->execute([
            (int) $userId,
            $settings['recipient_email'],
            $settings['enabled'],
            $settings['daily_summary'],
            $settings['weekly_summary'],
            $settings['monthly_summary'],
            $settings['traffic_anomaly'],
            $settings['tracking_errors'],
            $settings['smtp_host'],
            $settings['smtp_port'],
            $settings['smtp_encryption'],
            $settings['smtp_username'],
            $encryptedPassword,
            $settings['from_email'],
            $settings['from_name']
        ]);
    }

    private function encrypt($value) {
        $key = $this->getEncryptionKey();
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($value, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($ciphertext === false) {
            throw new RuntimeException('SMTP şifresi şifrelenemedi.');
        }

        return base64_encode($iv . $tag . $ciphertext);
    }

    private function decrypt($value) {
        $encrypted = base64_decode($value, true);
        if (!is_string($encrypted) || strlen($encrypted) < 28) {
            throw new RuntimeException('Kayıtlı SMTP şifresi çözümlenemedi.');
        }

        $iv = substr($encrypted, 0, 12);
        $tag = substr($encrypted, 12, 16);
        $ciphertext = substr($encrypted, 28);
        $password = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->getEncryptionKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        if ($password === false) {
            throw new RuntimeException('SMTP şifresi açılamadı; şifreleme anahtarını doğrulayın.');
        }

        return $password;
    }

    private function getEncryptionKey() {
        $path = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . '.web-analytics-smtp.key';
        if (is_file($path)) {
            $key = file_get_contents($path);
            if ($key === false || strlen($key) !== 32) {
                throw new RuntimeException('SMTP şifreleme anahtarı okunamadı veya geçersiz.');
            }
            return $key;
        }

        $key = random_bytes(32);
        $handle = @fopen($path, 'x');
        if ($handle === false) {
            if (is_file($path)) {
                return $this->getEncryptionKey();
            }
            throw new RuntimeException('SMTP şifreleme anahtarı oluşturulamadı. config klasörünün yazma izinlerini kontrol edin.');
        }

        try {
            if (fwrite($handle, $key) !== strlen($key)) {
                throw new RuntimeException('SMTP şifreleme anahtarı diske yazılamadı.');
            }
            fflush($handle);
        } finally {
            fclose($handle);
        }

        if (DIRECTORY_SEPARATOR !== '\\') {
            if (!chmod($path, 0600)) {
                throw new RuntimeException('SMTP şifreleme anahtarı dosya izinleri güvenli hale getirilemedi.');
            }
        }

        return $key;
    }

    private function defaults() {
        return [
            'recipient_email' => '',
            'enabled' => 0,
            'daily_summary' => 0,
            'weekly_summary' => 0,
            'monthly_summary' => 0,
            'traffic_anomaly' => 0,
            'tracking_errors' => 0,
            'smtp_host' => '',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'smtp_username' => '',
            'has_smtp_password' => false,
            'from_email' => '',
            'from_name' => ''
        ];
    }
}
