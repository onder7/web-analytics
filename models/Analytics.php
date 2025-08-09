<?php
// models/Analytics.php - Düzeltilmiş versiyon

class Analytics {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function getTotalWebsites() {
        $sql = "SELECT COUNT(*) as total FROM websites";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
    
    public function getTotalVisitors($websiteId = null, $days = 30) {
        $sql = "SELECT COUNT(DISTINCT v.visitor_id) as total FROM visitors v 
                INNER JOIN page_views pv ON v.id = pv.visitor_id 
                WHERE pv.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $params = [$days];
        
        if ($websiteId) {
            $sql .= " AND pv.website_id = ?";
            $params[] = $websiteId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
    
    public function getTotalPageViews($websiteId = null, $days = 30) {
        $sql = "SELECT COUNT(*) as total FROM page_views 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $params = [$days];
        
        if ($websiteId) {
            $sql .= " AND website_id = ?";
            $params[] = $websiteId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
    
    public function getDailyStats($websiteId = null, $days = 7) {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as page_views,
                    COUNT(DISTINCT visitor_id) as unique_visitors
                FROM page_views 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $params = [$days];
        
        if ($websiteId) {
            $sql .= " AND website_id = ?";
            $params[] = $websiteId;
        }
        
        $sql .= " GROUP BY DATE(created_at) ORDER BY date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // DÜZELTME: getPopularPages fonksiyonu
    public function getPopularPages($websiteId, $limit = 10) {
        // LIMIT parametresini string olarak SQL'e dahil et
        $sql = "SELECT 
                    page_url,
                    page_title,
                    COUNT(*) as views
                FROM page_views 
                WHERE website_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY page_url, page_title 
                ORDER BY views DESC 
                LIMIT " . intval($limit); // LIMIT'i doğrudan SQL'e ekle
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId]); // Sadece websiteId parametresi
        return $stmt->fetchAll();
    }
    
    // DÜZELTME: getBrowserStats fonksiyonu
    public function getBrowserStats($websiteId) {
        $sql = "SELECT 
                    browser,
                    COUNT(*) as count,
                    ROUND((COUNT(*) * 100.0 / (
                        SELECT COUNT(*) FROM page_views WHERE website_id = ?
                    )), 2) as percentage
                FROM page_views 
                WHERE website_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND browser IS NOT NULL AND browser != ''
                GROUP BY browser 
                ORDER BY count DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId, $websiteId]);
        return $stmt->fetchAll();
    }
    
    // DÜZELTME: getOSStats fonksiyonu
    public function getOSStats($websiteId) {
        $sql = "SELECT 
                    os,
                    COUNT(*) as count,
                    ROUND((COUNT(*) * 100.0 / (
                        SELECT COUNT(*) FROM page_views WHERE website_id = ?
                    )), 2) as percentage
                FROM page_views 
                WHERE website_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND os IS NOT NULL AND os != ''
                GROUP BY os 
                ORDER BY count DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId, $websiteId]);
        return $stmt->fetchAll();
    }
    
    public function getWebsiteStats() {
        $sql = "SELECT 
                    w.id,
                    w.name,
                    w.domain,
                    COALESCE(pv.page_views, 0) as page_views,
                    COALESCE(uv.unique_visitors, 0) as unique_visitors
                FROM websites w
                LEFT JOIN (
                    SELECT website_id, COUNT(*) as page_views 
                    FROM page_views 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY website_id
                ) pv ON w.id = pv.website_id
                LEFT JOIN (
                    SELECT website_id, COUNT(DISTINCT visitor_id) as unique_visitors 
                    FROM page_views 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY website_id
                ) uv ON w.id = uv.website_id
                ORDER BY page_views DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function recordPageView($websiteId, $visitorId, $data) {
        $sql = "INSERT INTO page_views (
                    website_id, visitor_id, page_url, page_title, referrer_url, 
                    ip_address, user_agent, browser, os, device_type, 
                    screen_resolution, viewport_size, language, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $websiteId,
            $visitorId,
            $data['page_url'] ?? '',
            $data['page_title'] ?? '',
            $data['referrer_url'] ?? '',
            $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'],
            $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'],
            $data['browser'] ?? '',
            $data['os'] ?? '',
            $data['device_type'] ?? 'desktop',
            $data['screen_resolution'] ?? '',
            $data['viewport_size'] ?? '',
            $data['language'] ?? 'en-US'
        ]);
    }
    
    public function getOrCreateVisitor($visitorId) {
        // Önce visitor'ı bul
        $sql = "SELECT * FROM visitors WHERE visitor_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$visitorId]);
        $visitor = $stmt->fetch();
        
        if (!$visitor) {
            // Yeni visitor oluştur
            $sql = "INSERT INTO visitors (visitor_id, first_visit, last_visit) VALUES (?, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$visitorId]);
            
            return $this->db->lastInsertId();
        } else {
            // Last visit güncelle
            $sql = "UPDATE visitors SET last_visit = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$visitor['id']]);
            
            return $visitor['id'];
        }
    }
    
    // EK FONKSIYONLAR - Daha detaylı analytics için
    
    public function getTopReferrers($websiteId, $limit = 10) {
        $sql = "SELECT 
                    referrer_url,
                    COUNT(*) as visits,
                    COUNT(DISTINCT visitor_id) as unique_visitors
                FROM page_views 
                WHERE website_id = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND referrer_url IS NOT NULL 
                AND referrer_url != ''
                GROUP BY referrer_url 
                ORDER BY visits DESC 
                LIMIT " . intval($limit);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId]);
        return $stmt->fetchAll();
    }
    
    public function getDeviceStats($websiteId) {
        $sql = "SELECT 
                    device_type,
                    COUNT(*) as count,
                    ROUND((COUNT(*) * 100.0 / (
                        SELECT COUNT(*) FROM page_views WHERE website_id = ?
                    )), 2) as percentage
                FROM page_views 
                WHERE website_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND device_type IS NOT NULL
                GROUP BY device_type 
                ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId, $websiteId]);
        return $stmt->fetchAll();
    }
    
    public function getHourlyStats($websiteId, $days = 7) {
        $sql = "SELECT 
                    HOUR(created_at) as hour,
                    COUNT(*) as page_views,
                    COUNT(DISTINCT visitor_id) as unique_visitors
                FROM page_views 
                WHERE website_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY HOUR(created_at) 
                ORDER BY hour";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId, $days]);
        return $stmt->fetchAll();
    }
    
    public function getLanguageStats($websiteId) {
        $sql = "SELECT 
                    language,
                    COUNT(*) as count,
                    ROUND((COUNT(*) * 100.0 / (
                        SELECT COUNT(*) FROM page_views WHERE website_id = ?
                    )), 2) as percentage
                FROM page_views 
                WHERE website_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND language IS NOT NULL
                GROUP BY language 
                ORDER BY count DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId, $websiteId]);
        return $stmt->fetchAll();
    }
    
    public function getBounceRate($websiteId, $days = 30) {
        $sql = "SELECT 
                    COUNT(DISTINCT s.session_id) as total_sessions,
                    SUM(CASE WHEN s.page_count = 1 THEN 1 ELSE 0 END) as bounce_sessions
                FROM sessions s
                WHERE s.website_id = ? 
                AND s.start_time >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId, $days]);
        $result = $stmt->fetch();
        
        if ($result['total_sessions'] > 0) {
            return round(($result['bounce_sessions'] / $result['total_sessions']) * 100, 2);
        }
        return 0;
    }
    
    public function getAverageSessionDuration($websiteId, $days = 30) {
        $sql = "SELECT AVG(duration) as avg_duration
                FROM sessions 
                WHERE website_id = ? 
                AND start_time >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND duration > 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$websiteId, $days]);
        $result = $stmt->fetch();
        
        return round($result['avg_duration'] ?? 0);
    }
    public function debugQuery($sql, $params = []) {
    echo "<pre>";
    echo "SQL: " . $sql . "\n";
    echo "Params: " . print_r($params, true);
    echo "</pre>";
}
}
