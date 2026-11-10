CREATE TABLE IF NOT EXISTS notification_deliveries (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
