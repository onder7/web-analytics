<?php
class ApiController {
    private $analyticsModel;
    private $websiteModel;
    
    public function __construct() {
        $this->analyticsModel = new Analytics();
        $this->websiteModel = new Website();
    }
    
    public function track($action) {
        // CORS headers - TÜM origin'lere izin ver
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
        header('Access-Control-Max-Age: 86400'); // 24 saat cache
        header('Content-Type: application/json');
        
        // OPTIONS preflight request'i için
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        
        // POST, GET ve diğer methodları kabul et
        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'GET'])) {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed. Use POST or GET.']);
            return;
        }
        
        // API key kontrolü - GET ve POST'tan al
        $apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';
        
        // JSON body'den de API key al
        $rawInput = file_get_contents('php://input');
        if ($rawInput) {
            $jsonData = json_decode($rawInput, true);
            if ($jsonData && isset($jsonData['api_key'])) {
                $apiKey = $jsonData['api_key'];
            }
        }
        
        if (empty($apiKey)) {
            http_response_code(401);
            echo json_encode(['error' => 'API key required']);
            return;
        }
        
        $website = $this->websiteModel->findByApiKey($apiKey);
        if (!$website) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid API key']);
            return;
        }
        
        // Data'yı farklı kaynaklardan al
        $data = [];
        
        // JSON body'den
        if ($rawInput) {
            $jsonData = json_decode($rawInput, true);
            if ($jsonData) {
                $data = $jsonData;
            }
        }
        
        // POST data'dan
        if (empty($data) && !empty($_POST)) {
            $data = $_POST;
        }
        
        // GET data'dan (fallback)
        if (empty($data) && !empty($_GET)) {
            $data = $_GET;
        }
        
        // IP ve coğrafi bilgileri ekle
        $this->enrichWithGeoData($data);
        
        try {
            switch ($action) {
                case 'pageview':
                    $result = $this->trackPageView($website['id'], $data);
                    break;
                case 'event':
                    $result = $this->trackEvent($website['id'], $data);
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid action. Use pageview or event.']);
                    return;
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Data tracked successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to track data']);
            }
            
        } catch (Exception $e) {
            error_log("API Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * IP ve coğrafi bilgileri ekle
     */
    private function enrichWithGeoData(&$data) {
        // Gerçek IP adresini al
        $ip = $this->getRealIpAddress();
        $data['ip_address'] = $ip;
        
        // Coğrafi bilgileri al
        $geoData = $this->getGeoDataFromIp($ip);
        
        // Data'ya coğrafi bilgileri ekle
        $data['country'] = $geoData['country'] ?? null;
        $data['country_code'] = $geoData['country_code'] ?? null;
        $data['region'] = $geoData['region'] ?? null;
        $data['city'] = $geoData['city'] ?? null;
        $data['latitude'] = $geoData['latitude'] ?? null;
        $data['longitude'] = $geoData['longitude'] ?? null;
        $data['timezone'] = $geoData['timezone'] ?? null;
        $data['isp'] = $geoData['isp'] ?? null;
    }
    
    /**
     * Gerçek IP adresini al (proxy ve CDN desteği ile)
     */
    private function getRealIpAddress() {
        // Proxy ve CDN header'larını kontrol et
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_REAL_IP',            // Nginx
            'HTTP_X_FORWARDED_FOR',      // Load Balancer/Proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                
                // Geçerli IP kontrolü
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        // Fallback
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * IP'den coğrafi bilgi al
     * Birden fazla servis ile fallback sistemi
     */
    private function getGeoDataFromIp($ip) {
        // Local/test IP kontrolü
        if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return [
                'country' => 'Local',
                'country_code' => 'LO',
                'region' => 'Local',
                'city' => 'Localhost',
                'latitude' => null,
                'longitude' => null,
                'timezone' => date_default_timezone_get(),
                'isp' => 'Local'
            ];
        }
        
        // Cache kontrolü (opsiyonel - Redis/Memcache kullanabilirsiniz)
        $cacheKey = 'geo_' . md5($ip);
        
        // Servisler (sırayla denenecek)
        $services = [
            'ipapi' => "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,lat,lon,timezone,isp",
            'ipinfo' => "https://ipinfo.io/{$ip}/json",
            'freegeoip' => "https://freegeoip.app/json/{$ip}"
        ];
        
        foreach ($services as $serviceName => $url) {
            try {
                $geoData = $this->fetchGeoData($url, $serviceName);
                if ($geoData) {
                    // Cache'e kaydet (24 saat)
                    // $this->cacheSet($cacheKey, $geoData, 86400);
                    return $geoData;
                }
            } catch (Exception $e) {
                error_log("Geo service {$serviceName} failed: " . $e->getMessage());
                continue;
            }
        }
        
        // Tüm servisler başarısız olursa varsayılan değerler
        return [
            'country' => 'Unknown',
            'country_code' => 'XX',
            'region' => 'Unknown',
            'city' => 'Unknown',
            'latitude' => null,
            'longitude' => null,
            'timezone' => date_default_timezone_get(),
            'isp' => 'Unknown'
        ];
    }
    
    /**
     * Coğrafi veri çek
     */
    private function fetchGeoData($url, $serviceName) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 3, // 3 saniye timeout
                'user_agent' => 'NetworkAnalytics/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception("Failed to fetch data from {$serviceName}");
        }
        
        $data = json_decode($response, true);
        
        if (!$data) {
            throw new Exception("Invalid JSON response from {$serviceName}");
        }
        
        // Servis-specific parsing
        switch ($serviceName) {
            case 'ipapi':
                if (isset($data['status']) && $data['status'] === 'fail') {
                    throw new Exception("IP-API error: " . ($data['message'] ?? 'Unknown error'));
                }
                return [
                    'country' => $data['country'] ?? null,
                    'country_code' => $data['countryCode'] ?? null,
                    'region' => $data['regionName'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'isp' => $data['isp'] ?? null
                ];
                
            case 'ipinfo':
                $location = explode(',', $data['loc'] ?? '');
                return [
                    'country' => $data['country'] ?? null,
                    'country_code' => $data['country'] ?? null,
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => isset($location[0]) ? floatval($location[0]) : null,
                    'longitude' => isset($location[1]) ? floatval($location[1]) : null,
                    'timezone' => $data['timezone'] ?? null,
                    'isp' => $data['org'] ?? null
                ];
                
            case 'freegeoip':
                return [
                    'country' => $data['country_name'] ?? null,
                    'country_code' => $data['country_code'] ?? null,
                    'region' => $data['region_name'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'timezone' => $data['time_zone'] ?? null,
                    'isp' => null
                ];
        }
        
        return null;
    }
    
    private function trackPageView($websiteId, $data) {
        $visitorId = $data['visitor_id'] ?? '';
        if (empty($visitorId)) {
            throw new Exception('Visitor ID required');
        }
        
        try {
            $visitorDbId = $this->analyticsModel->getOrCreateVisitor($visitorId);
            return $this->analyticsModel->recordPageView($websiteId, $visitorDbId, $data);
        } catch (Exception $e) {
            error_log("TrackPageView Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function trackEvent($websiteId, $data) {
        try {
            $db = Database::getConnection();
            
            $visitorId = $data['visitor_id'] ?? '';
            if (empty($visitorId)) {
                throw new Exception('Visitor ID required');
            }
            
            $visitorDbId = $this->analyticsModel->getOrCreateVisitor($visitorId);
            
            $sql = "INSERT INTO events (
                        website_id, visitor_id, session_id, event_type, 
                        event_category, event_action, event_label, event_value,
                        page_url, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                $websiteId,
                $visitorDbId,
                $data['session_id'] ?? '',
                $data['event_type'] ?? 'custom',
                $data['event_category'] ?? null,
                $data['event_action'] ?? null,
                $data['event_label'] ?? null,
                $data['event_value'] ?? null,
                $data['page_url'] ?? ''
            ]);
            
        } catch (Exception $e) {
            error_log("TrackEvent Error: " . $e->getMessage());
            throw $e;
        }
    }
}