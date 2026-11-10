-- Repeatable schema update for admin diagnostics, logging, retention, and dashboard query indexes.

SET @database_name = DATABASE();

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = @database_name AND table_name = 'users' AND column_name = 'is_admin') = 0,
    'ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER password',
    'SELECT 1'
);
PREPARE maintenance_stmt FROM @ddl;
EXECUTE maintenance_stmt;
DEALLOCATE PREPARE maintenance_stmt;

CREATE TABLE IF NOT EXISTS system_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    severity ENUM('error', 'warning', 'info') NOT NULL DEFAULT 'error',
    source VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_system_logs_created (created_at),
    KEY idx_system_logs_severity_created (severity, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.statistics
     WHERE table_schema = @database_name AND table_name = 'page_views'
       AND index_name = 'idx_website_visitor_created') = 0,
    'ALTER TABLE page_views ADD INDEX idx_website_visitor_created (website_id, visitor_id, created_at)',
    'SELECT 1'
);
PREPARE maintenance_stmt FROM @ddl;
EXECUTE maintenance_stmt;
DEALLOCATE PREPARE maintenance_stmt;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.statistics
     WHERE table_schema = @database_name AND table_name = 'events'
       AND index_name = 'idx_website_category_created') = 0,
    'ALTER TABLE events ADD INDEX idx_website_category_created (website_id, event_category, created_at)',
    'SELECT 1'
);
PREPARE maintenance_stmt FROM @ddl;
EXECUTE maintenance_stmt;
DEALLOCATE PREPARE maintenance_stmt;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.statistics
     WHERE table_schema = @database_name AND table_name = 'sessions'
       AND index_name = 'idx_website_start_time') = 0,
    'ALTER TABLE sessions ADD INDEX idx_website_start_time (website_id, start_time)',
    'SELECT 1'
);
PREPARE maintenance_stmt FROM @ddl;
EXECUTE maintenance_stmt;
DEALLOCATE PREPARE maintenance_stmt;
