ALTER TABLE page_views
    ADD COLUMN session_id VARCHAR(64) NULL AFTER visitor_id,
    MODIFY COLUMN country VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL,
    ADD KEY idx_website_session (website_id, session_id);

ALTER TABLE sessions
    ADD UNIQUE KEY unique_website_session (website_id, session_id);

CREATE TABLE IF NOT EXISTS login_attempts (
    bucket_key CHAR(64) NOT NULL PRIMARY KEY,
    failed_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    window_started_at DATETIME NOT NULL,
    locked_until DATETIME NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS geo_ip_cache (
    ip_address VARCHAR(45) NOT NULL PRIMARY KEY,
    country VARCHAR(100) NULL,
    country_code VARCHAR(5) NULL,
    region VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    timezone VARCHAR(50) NULL,
    isp VARCHAR(255) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS geo_lookup_queue (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL UNIQUE,
    attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    available_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_geo_queue_available (available_at, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO daily_stats (website_id, date, page_views, unique_visitors, sessions)
SELECT website_id, DATE(created_at), COUNT(*), COUNT(DISTINCT visitor_id), 0
FROM page_views
GROUP BY website_id, DATE(created_at)
ON DUPLICATE KEY UPDATE
    page_views = VALUES(page_views),
    unique_visitors = VALUES(unique_visitors);
