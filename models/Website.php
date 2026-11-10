
<?php
class Website {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create($userId, $domain, $name) {
        $apiKey = bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO websites (user_id, domain, name, api_key, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $domain, $name, $apiKey]);
    }
    
    public function getAll($userId) {
        $sql = "SELECT * FROM websites WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function findById($id, $userId) {
        $sql = "SELECT w.id, w.user_id, w.domain, w.name, w.timezone, w.created_at, w.updated_at,
                       CASE WHEN w.user_id = ? THEN 'owner' ELSE m.role END AS access_role,
                       CASE WHEN w.user_id = ? THEN w.api_key ELSE NULL END AS api_key
                FROM websites w
                LEFT JOIN website_members m ON m.website_id = w.id AND m.user_id = ?
                WHERE w.id = ? AND (w.user_id = ? OR m.user_id = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId, $id, $userId, $userId]);
        return $stmt->fetch();
    }
    
    public function findByApiKey($apiKey) {
        $sql = "SELECT * FROM websites WHERE api_key = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$apiKey]);
        return $stmt->fetch();
    }
    
    public function update($id, $userId, $domain, $name, $timezone = 'UTC') {
        $sql = "UPDATE websites
                SET domain = ?, name = ?, timezone = ?, updated_at = NOW()
                WHERE id = ? AND (
                    user_id = ? OR EXISTS (
                        SELECT 1 FROM website_members
                        WHERE website_id = websites.id AND user_id = ? AND role = 'write'
                    )
                )";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$domain, $name, $timezone, $id, $userId, $userId]);
    }
    
    public function delete($id, $userId) {
        $sql = "DELETE FROM websites WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }

    public function rotateApiKey($id, $userId) {
        $apiKey = bin2hex(random_bytes(32));
        $statement = $this->db->prepare(
            'UPDATE websites SET api_key = ?, updated_at = NOW() WHERE id = ? AND user_id = ?'
        );
        if (!$statement->execute([$apiKey, $id, $userId]) || $statement->rowCount() !== 1) {
            return false;
        }
        return $apiKey;
    }

    public function createInvitation($websiteId, $ownerId, $email, $role) {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, ['read', 'write'], true)) {
            throw new InvalidArgumentException('Davet e-postası veya rol geçersiz.');
        }
        if (!$this->isOwner($websiteId, $ownerId)) {
            throw new RuntimeException('Takım üyelerini yalnızca site sahibi yönetebilir.');
        }

        $userQuery = $this->db->prepare('SELECT id FROM users WHERE LOWER(email) = ?');
        $userQuery->execute([$email]);
        $invitee = $userQuery->fetch();
        if (!$invitee) {
            throw new InvalidArgumentException('Bu e-posta ile kayıtlı kullanıcı bulunamadı.');
        }
        if ((int) $invitee['id'] === (int) $ownerId) {
            throw new InvalidArgumentException('Site sahibine davet gönderilemez.');
        }

        $memberQuery = $this->db->prepare('SELECT 1 FROM website_members WHERE website_id = ? AND user_id = ?');
        $memberQuery->execute([$websiteId, $invitee['id']]);
        if ($memberQuery->fetchColumn()) {
            throw new InvalidArgumentException('Bu kullanıcı zaten site ekibinde.');
        }

        $token = bin2hex(random_bytes(32));
        $this->db->beginTransaction();
        try {
            $delete = $this->db->prepare('DELETE FROM website_invitations WHERE website_id = ? AND email = ? AND accepted_at IS NULL');
            $delete->execute([$websiteId, $email]);
            $insert = $this->db->prepare(
                'INSERT INTO website_invitations (website_id, email, token_hash, role, invited_by, expires_at)
                 VALUES (?, ?, ?, ?, ?, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 7 DAY))'
            );
            $insert->execute([$websiteId, $email, hash('sha256', $token), $role, $ownerId]);
            $this->db->commit();
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
        return $token;
    }

    public function getInvitation($token) {
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        $statement = $this->db->prepare(
            'SELECT i.email, i.role, i.expires_at, w.name AS website_name, w.domain
             FROM website_invitations i
             INNER JOIN websites w ON w.id = i.website_id
             WHERE i.token_hash = ? AND i.accepted_at IS NULL AND i.expires_at > UTC_TIMESTAMP()'
        );
        $statement->execute([hash('sha256', $token)]);
        return $statement->fetch() ?: null;
    }

    public function acceptInvitation($token, $userId, $userEmail) {
        $hash = hash('sha256', (string) $token);
        $this->db->beginTransaction();
        try {
            $statement = $this->db->prepare(
                'SELECT id, website_id, email, role FROM website_invitations
                 WHERE token_hash = ? AND accepted_at IS NULL AND expires_at > UTC_TIMESTAMP()
                 FOR UPDATE'
            );
            $statement->execute([$hash]);
            $invitation = $statement->fetch();
            if (!$invitation || strtolower($invitation['email']) !== strtolower((string) $userEmail)) {
                $this->db->rollBack();
                return false;
            }

            $membership = $this->db->prepare(
                'INSERT INTO website_members (website_id, user_id, role, invited_by)
                 VALUES (?, ?, ?, (SELECT invited_by FROM website_invitations WHERE id = ?))'
            );
            $membership->execute([$invitation['website_id'], $userId, $invitation['role'], $invitation['id']]);
            $accepted = $this->db->prepare(
                'UPDATE website_invitations SET accepted_at = UTC_TIMESTAMP()
                 WHERE id = ? AND accepted_at IS NULL'
            );
            $accepted->execute([$invitation['id']]);
            $this->db->commit();
            return true;
        } catch (Throwable $error) {
            $this->db->rollBack();
            throw $error;
        }
    }

    public function getTeam($websiteId, $ownerId) {
        if (!$this->isOwner($websiteId, $ownerId)) {
            return ['members' => [], 'invitations' => []];
        }
        $members = $this->db->prepare(
            'SELECT m.user_id, u.username, u.email, m.role, m.created_at
             FROM website_members m INNER JOIN users u ON u.id = m.user_id
             WHERE m.website_id = ? ORDER BY u.email'
        );
        $members->execute([$websiteId]);
        $invitations = $this->db->prepare(
            'SELECT id, email, role, expires_at FROM website_invitations
             WHERE website_id = ? AND accepted_at IS NULL AND expires_at > UTC_TIMESTAMP()
             ORDER BY created_at DESC'
        );
        $invitations->execute([$websiteId]);
        return ['members' => $members->fetchAll(), 'invitations' => $invitations->fetchAll()];
    }

    public function removeMember($websiteId, $ownerId, $memberId) {
        $statement = $this->db->prepare(
            'DELETE FROM website_members WHERE website_id = ? AND user_id = ? AND EXISTS (
                SELECT 1 FROM websites WHERE id = ? AND user_id = ?
            )'
        );
        return $statement->execute([$websiteId, $memberId, $websiteId, $ownerId]);
    }

    public function cancelInvitation($websiteId, $ownerId, $invitationId) {
        $statement = $this->db->prepare(
            'DELETE FROM website_invitations WHERE id = ? AND website_id = ? AND EXISTS (
                SELECT 1 FROM websites WHERE id = ? AND user_id = ?
            )'
        );
        return $statement->execute([$invitationId, $websiteId, $websiteId, $ownerId]);
    }

    private function isOwner($websiteId, $userId) {
        $statement = $this->db->prepare('SELECT 1 FROM websites WHERE id = ? AND user_id = ?');
        $statement->execute([$websiteId, $userId]);
        return (bool) $statement->fetchColumn();
    }
    
    public function domainExists($domain, $excludeId = null) {
        $sql = "SELECT id FROM websites WHERE domain = ?";
        $params = [$domain];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
}
