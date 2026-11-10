
<?php
class User {
    private $db;
    private $twoFactorAvailable;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $email, $hashedPassword]);
    }
    
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    public function verifyPassword($userId, $password) {
        $stmt = $this->db->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([(int) $userId]);
        $hash = $stmt->fetchColumn();
        return is_string($hash) && password_verify($password, $hash);
    }

    public function isTwoFactorAvailable() {
        if ($this->twoFactorAvailable !== null) {
            return $this->twoFactorAvailable;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'users'
               AND column_name IN (
                   'totp_enabled', 'totp_secret_encrypted',
                   'totp_pending_secret_encrypted', 'totp_recovery_codes', 'totp_last_step'
               )"
        );
        $stmt->execute();
        $this->twoFactorAvailable = (int) $stmt->fetchColumn() === 5;
        return $this->twoFactorAvailable;
    }

    public function getTwoFactorStatus($userId) {
        if (!$this->isTwoFactorAvailable()) {
            throw new RuntimeException('Önce 2FA veritabanı migration dosyası uygulanmalıdır.');
        }

        $stmt = $this->db->prepare(
            'SELECT totp_enabled, totp_secret_encrypted, totp_pending_secret_encrypted,
                    totp_recovery_codes, totp_last_step
             FROM users WHERE id = ?'
        );
        $stmt->execute([(int) $userId]);
        $status = $stmt->fetch();
        if (!$status) {
            throw new RuntimeException('Hesap bulunamadı.');
        }

        return $status;
    }

    public function savePendingTotpSecret($userId, $encryptedSecret) {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET totp_pending_secret_encrypted = ?
             WHERE id = ? AND totp_enabled = 0'
        );
        $stmt->execute([$encryptedSecret, (int) $userId]);
        return $stmt->rowCount() === 1;
    }

    public function clearPendingTotpSecret($userId) {
        $stmt = $this->db->prepare(
            'UPDATE users SET totp_pending_secret_encrypted = NULL
             WHERE id = ? AND totp_enabled = 0'
        );
        return $stmt->execute([(int) $userId]);
    }

    public function enableTotp($userId, array $recoveryHashes, $lastStep) {
        $status = $this->getTwoFactorStatus($userId);
        if ((int) $status['totp_enabled'] === 1 || empty($status['totp_pending_secret_encrypted'])) {
            return false;
        }

        $recoveryJson = json_encode($recoveryHashes, JSON_THROW_ON_ERROR);
        $stmt = $this->db->prepare(
            'UPDATE users
             SET totp_enabled = 1,
                 totp_secret_encrypted = totp_pending_secret_encrypted,
                 totp_pending_secret_encrypted = NULL,
                 totp_recovery_codes = ?,
                 totp_last_step = ?
             WHERE id = ? AND totp_enabled = 0 AND totp_pending_secret_encrypted = ?'
        );
        $stmt->execute([
            $recoveryJson,
            (int) $lastStep,
            (int) $userId,
            $status['totp_pending_secret_encrypted']
        ]);
        return $stmt->rowCount() === 1;
    }

    public function consumeTotpStep($userId, $step) {
        $stmt = $this->db->prepare(
            'UPDATE users SET totp_last_step = ?
             WHERE id = ? AND totp_enabled = 1
               AND (totp_last_step IS NULL OR totp_last_step < ?)'
        );
        $stmt->execute([(int) $step, (int) $userId, (int) $step]);
        return $stmt->rowCount() === 1;
    }

    public function consumeRecoveryCode($userId, $code) {
        $status = $this->getTwoFactorStatus($userId);
        if ((int) $status['totp_enabled'] !== 1 || empty($status['totp_recovery_codes'])) {
            return false;
        }

        $hashes = json_decode($status['totp_recovery_codes'], true);
        if (!is_array($hashes)) {
            throw new RuntimeException('Kurtarma kodu kayıtları okunamadı.');
        }
        foreach ($hashes as $index => $hash) {
            if (!is_string($hash) || !password_verify($code, $hash)) {
                continue;
            }

            unset($hashes[$index]);
            $updatedJson = json_encode(array_values($hashes), JSON_THROW_ON_ERROR);
            $stmt = $this->db->prepare(
                'UPDATE users SET totp_recovery_codes = ?
                 WHERE id = ? AND totp_enabled = 1 AND totp_recovery_codes = ?'
            );
            $stmt->execute([
                $updatedJson,
                (int) $userId,
                $status['totp_recovery_codes']
            ]);
            return $stmt->rowCount() === 1;
        }

        return false;
    }

    public function disableTotp($userId) {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET totp_enabled = 0,
                 totp_secret_encrypted = NULL,
                 totp_pending_secret_encrypted = NULL,
                 totp_recovery_codes = NULL,
                 totp_last_step = NULL
             WHERE id = ? AND totp_enabled = 1'
        );
        $stmt->execute([(int) $userId]);
        return $stmt->rowCount() === 1;
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function emailExistsForAnotherUser($email, $userId) {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
        $stmt->execute([$email, (int) $userId]);
        return $stmt->fetchColumn() !== false;
    }

    public function usernameExistsForAnotherUser($username, $userId) {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1');
        $stmt->execute([$username, (int) $userId]);
        return $stmt->fetchColumn() !== false;
    }

    public function updateProfile($userId, $username, $email) {
        $stmt = $this->db->prepare(
            'UPDATE users SET username = ?, email = ?, updated_at = NOW() WHERE id = ?'
        );
        return $stmt->execute([$username, $email, (int) $userId]);
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $stmt = $this->db->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([(int) $userId]);
        $passwordHash = $stmt->fetchColumn();
        if (!is_string($passwordHash) || !password_verify($currentPassword, $passwordHash)) {
            return false;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        if (!is_string($newHash)) {
            throw new RuntimeException('Yeni şifre güvenli biçimde saklanamadı.');
        }

        $update = $this->db->prepare(
            'UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?'
        );
        return $update->execute([$newHash, (int) $userId]);
    }
}
