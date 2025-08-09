
<?php
class Website {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create($domain, $name) {
        $apiKey = bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO websites (domain, name, api_key, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$domain, $name, $apiKey]);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM websites ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM websites WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByApiKey($apiKey) {
        $sql = "SELECT * FROM websites WHERE api_key = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$apiKey]);
        return $stmt->fetch();
    }
    
    public function update($id, $domain, $name) {
        $sql = "UPDATE websites SET domain = ?, name = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$domain, $name, $id]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM websites WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
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
