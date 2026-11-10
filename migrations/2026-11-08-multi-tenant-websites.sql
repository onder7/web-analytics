ALTER TABLE websites ENGINE=InnoDB;

ALTER TABLE websites
    ADD COLUMN user_id INT NULL AFTER id,
    ADD KEY idx_websites_user_id (user_id),
    ADD CONSTRAINT fk_websites_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE;

UPDATE websites
SET user_id = (SELECT id FROM users ORDER BY id LIMIT 1)
WHERE user_id IS NULL
  AND (SELECT COUNT(*) FROM users) = 1;
