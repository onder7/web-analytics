-- Repeatable schema update for per-user TOTP two-factor authentication.

SET @database_name = DATABASE();

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = @database_name AND table_name = 'users' AND column_name = 'totp_enabled') = 0,
    'ALTER TABLE users ADD COLUMN totp_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER password',
    'SELECT 1'
);
PREPARE totp_stmt FROM @ddl;
EXECUTE totp_stmt;
DEALLOCATE PREPARE totp_stmt;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = @database_name AND table_name = 'users' AND column_name = 'totp_secret_encrypted') = 0,
    'ALTER TABLE users ADD COLUMN totp_secret_encrypted TEXT NULL AFTER totp_enabled',
    'SELECT 1'
);
PREPARE totp_stmt FROM @ddl;
EXECUTE totp_stmt;
DEALLOCATE PREPARE totp_stmt;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = @database_name AND table_name = 'users' AND column_name = 'totp_pending_secret_encrypted') = 0,
    'ALTER TABLE users ADD COLUMN totp_pending_secret_encrypted TEXT NULL AFTER totp_secret_encrypted',
    'SELECT 1'
);
PREPARE totp_stmt FROM @ddl;
EXECUTE totp_stmt;
DEALLOCATE PREPARE totp_stmt;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = @database_name AND table_name = 'users' AND column_name = 'totp_recovery_codes') = 0,
    'ALTER TABLE users ADD COLUMN totp_recovery_codes TEXT NULL AFTER totp_pending_secret_encrypted',
    'SELECT 1'
);
PREPARE totp_stmt FROM @ddl;
EXECUTE totp_stmt;
DEALLOCATE PREPARE totp_stmt;

SET @ddl = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = @database_name AND table_name = 'users' AND column_name = 'totp_last_step') = 0,
    'ALTER TABLE users ADD COLUMN totp_last_step BIGINT NULL AFTER totp_recovery_codes',
    'SELECT 1'
);
PREPARE totp_stmt FROM @ddl;
EXECUTE totp_stmt;
DEALLOCATE PREPARE totp_stmt;
