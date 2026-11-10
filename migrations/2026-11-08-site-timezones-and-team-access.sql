ALTER TABLE websites
    ADD COLUMN timezone VARCHAR(64) NOT NULL DEFAULT 'UTC' AFTER api_key;

CREATE TABLE website_members (
    website_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('read', 'write') NOT NULL DEFAULT 'read',
    invited_by INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (website_id, user_id),
    KEY idx_website_members_user (user_id),
    CONSTRAINT fk_website_members_website
        FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    CONSTRAINT fk_website_members_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_website_members_invited_by
        FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

CREATE TABLE website_invitations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    website_id INT NOT NULL,
    email VARCHAR(191) NOT NULL,
    token_hash CHAR(64) NOT NULL,
    role ENUM('read', 'write') NOT NULL DEFAULT 'read',
    invited_by INT NOT NULL,
    expires_at DATETIME NOT NULL,
    accepted_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_website_invitation_token (token_hash),
    KEY idx_website_invitation_email (website_id, email, accepted_at),
    CONSTRAINT fk_website_invitations_website
        FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    CONSTRAINT fk_website_invitations_user
        FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
