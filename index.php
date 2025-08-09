<?php
// Output buffering başlat - bu header problemlerini önler
ob_start();

// Hata raporlamayı aktifleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

error_log("Script execution started");

// Session'ı güvenli başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_log("Session started successfully");
}

// Autoload kontrolü
if (!file_exists('vendor/autoload.php')) {
    ob_end_clean(); // Buffer'ı temizle
    die('Composer autoload dosyası bulunamadı. "composer install" komutunu çalıştırın.');
}

// Dosya varlık kontrolü
$requiredFiles = [
    'vendor/autoload.php',
    'config/database.php',
    'models/User.php',
    'models/Website.php',
    'models/Analytics.php',
    'controllers/AuthController.php',
    'controllers/WebsiteController.php',
    'controllers/AnalyticsController.php',
    'controllers/ApiController.php'
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        ob_end_clean();
        die("Gerekli dosya bulunamadı: $file");
    }
}

error_log("All required files found");

// Gerekli dosyaları yükle
try {
    require 'vendor/autoload.php';
    error_log("Autoload included successfully");
    
    require 'config/database.php';
    require 'models/User.php';
    require 'models/Website.php';
    require 'models/Analytics.php';
    require 'controllers/AuthController.php';
    require 'controllers/WebsiteController.php';
    require 'controllers/AnalyticsController.php';
    require 'controllers/ApiController.php';
    
    error_log("All files included successfully");
} catch (Exception $e) {
    error_log("File inclusion error: " . $e->getMessage());
    ob_end_clean();
    die("File inclusion error: " . $e->getMessage());
}

// Flight sınıfının varlığını kontrol et
if (!class_exists('Flight')) {
    ob_end_clean();
    die("Flight sınıfı bulunamadı. Composer install kontrolü yapın.");
}

error_log("Flight class found");

// Flight konfigürasyonu
try {
Flight::set('flight.views.path', 'views');
Flight::set('db.host', 'localhost');
Flight::set('db.name', 'networktoolkit_networktoolkit');
Flight::set('db.user', 'networktoolkit_networktoolkit');
Flight::set('db.pass', '=~@iNd,gJX$H{,*1');
    
    error_log("Flight configured successfully");
} catch (Exception $e) {
    error_log("Flight configuration error: " . $e->getMessage());
    ob_end_clean();
    die("Flight configuration error: " . $e->getMessage());
}

// Database bağlantısını test et
try {
    $testConnection = Database::getConnection();
    error_log("Database connection test successful");
} catch (Exception $e) {
    error_log("Database connection test failed: " . $e->getMessage());
    ob_end_clean();
    die("Database bağlantı hatası: " . $e->getMessage());
}

// Views klasörünü kontrol et
if (!is_dir('views')) {
    error_log("Views directory not found");
    ob_end_clean();
    die("Views klasörü bulunamadı.");
}

error_log("Views directory found");

// CUSTOM RENDER FUNCTION - Layout sistemi için
Flight::map('render', function($template, $data = [], $key = null) {
    error_log("Render function called for template: $template");
    
    if ($key !== null) {
        Flight::view()->set($key, Flight::view()->fetch($template, $data));
        return;
    }

    // Default data
    if (!is_array($data)) {
        $data = [];
    }
    
    // Template dosyasının varlığını kontrol et
    $templatePath = 'views/' . $template . '.php';
    if (!file_exists($templatePath)) {
        error_log("Template file not found: $templatePath");
        throw new Exception("Template dosyası bulunamadı: $templatePath");
    }
    
    // Auth sayfaları için layout kullanma
    $authPages = ['auth/login', 'auth/register'];
    $useLayout = !in_array($template, $authPages);
    
    if ($useLayout && isset($_SESSION['user_id'])) {
        // Layout kullan
        $content = Flight::view()->fetch($template, $data);
        
        // Layout dosyasını kontrol et
        if (!file_exists('views/layout.php')) {
            error_log("Layout file not found: views/layout.php");
            throw new Exception("Layout dosyası bulunamadı: views/layout.php");
        }
        
        // Layout için gerekli data'ları ekle
        $layoutData = array_merge($data, [
            'content' => $content,
            'bodyClass' => $data['bodyClass'] ?? '',
            'additionalCSS' => $data['additionalCSS'] ?? [],
            'additionalJS' => $data['additionalJS'] ?? [],
            'inlineJS' => $data['inlineJS'] ?? '',
            'breadcrumb' => $data['breadcrumb'] ?? []
        ]);
        
        echo Flight::view()->fetch('layout', $layoutData);
    } else {
        // Layout kullanmadan direkt render et
        echo Flight::view()->fetch($template, $data);
    }
});

// Error handling
Flight::map('error', function(Exception $ex) {
    // Log error
    error_log("Flight Error: " . $ex->getMessage() . " in " . $ex->getFile() . " on line " . $ex->getLine());
    error_log("Stack trace: " . $ex->getTraceAsString());
    
    // Buffer'ı temizle ve hata göster
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Debug mode'da hatayı göster
    echo "<h3>Hata Detayları:</h3>";
    echo "<p><strong>Mesaj:</strong> " . htmlspecialchars($ex->getMessage()) . "</p>";
    echo "<p><strong>Dosya:</strong> " . htmlspecialchars($ex->getFile()) . "</p>";
    echo "<p><strong>Satır:</strong> " . $ex->getLine() . "</p>";
    echo "<p><strong>Stack Trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($ex->getTraceAsString()) . "</pre>";
});

// 404 handling
Flight::map('notFound', function() {
    error_log("404 Not Found: " . $_SERVER['REQUEST_URI']);
    if (ob_get_level()) {
        ob_end_clean();
    }
    echo "<h1>404 - Sayfa Bulunamadı</h1>";
    echo "<p>Aranan sayfa: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
});

// Authentication middleware
Flight::before('start', function() {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    error_log("Request path: $path");
    
    $protected_routes = ['/dashboard', '/websites', '/analytics', '/logout'];
    
    foreach ($protected_routes as $route) {
        if (strpos($path, $route) === 0 && !isset($_SESSION['user_id'])) {
            error_log("Redirecting to login for protected route: $path");
            Flight::redirect('/login');
            return false;
        }
    }
});

error_log("Middleware configured");

// Routes
Flight::route('GET /', function() {
    error_log("Root route accessed");
    if (isset($_SESSION['user_id'])) {
        error_log("User logged in, redirecting to dashboard");
        Flight::redirect('/dashboard');
    } else {
        error_log("User not logged in, redirecting to login");
        Flight::redirect('/login');
    }
});

// Test route - basit test için
Flight::route('GET /test', function() {
    error_log("Test route accessed");
    if (ob_get_level()) {
        ob_end_clean();
    }
    echo "<h1>Test Başarılı!</h1>";
    echo "<p>Flight çalışıyor.</p>";
    echo "<p>Session durumu: " . (isset($_SESSION['user_id']) ? 'Giriş yapılmış' : 'Giriş yapılmamış') . "</p>";
    echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
});

// Auth Routes
Flight::route('GET /login', function() {
    error_log("Login route accessed");
    try {
        $controller = new AuthController();
        $controller->loginForm();
    } catch (Exception $e) {
        error_log("Login route error: " . $e->getMessage());
        throw $e;
    }
});

Flight::route('POST /login', function() {
    error_log("Login POST route accessed");
    try {
        $controller = new AuthController();
        $controller->login();
    } catch (Exception $e) {
        error_log("Login POST route error: " . $e->getMessage());
        throw $e;
    }
});

// API Routes - CORS desteği ile
Flight::route('GET /api', function() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode([
        'status' => 'ok',
        'message' => 'Network Analytics API',
        'version' => '1.0',
        'endpoints' => [
            'POST /api/track/pageview' => 'Track page views',
            'POST /api/track/event' => 'Track custom events'
        ]
    ]);
});
Flight::route('GET /login', [AuthController::class, 'loginForm']);
Flight::route('POST /login', [AuthController::class, 'login']);
Flight::route('GET /register', [AuthController::class, 'registerForm']);
Flight::route('POST /register', [AuthController::class, 'register']);
Flight::route('GET /logout', [AuthController::class, 'logout']);
// Dashboard
Flight::route('GET /dashboard', [AnalyticsController::class, 'dashboard']);

// Website Management Routes
Flight::route('GET /websites', [WebsiteController::class, 'index']);
Flight::route('GET /websites/create', [WebsiteController::class, 'create']);
Flight::route('POST /websites/create', [WebsiteController::class, 'store']);
Flight::route('GET /websites/@id/edit', [WebsiteController::class, 'edit']);
Flight::route('POST /websites/@id/edit', [WebsiteController::class, 'update']);
Flight::route('GET /websites/@id/delete', [WebsiteController::class, 'delete']);
Flight::route('GET /websites/@id/analytics', [AnalyticsController::class, 'websiteAnalytics']);
Flight::route('GET /api/status', function() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    
    try {
        // Database bağlantısını test et
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as count FROM websites");
        $result = $stmt->fetch();
        
        echo json_encode([
            'status' => 'ok',
            'database' => 'connected',
            'websites' => $result['count'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'database' => 'disconnected',
            'error' => $e->getMessage()
        ]);
    }
});
Flight::route('GET /api/debug', function() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    
    echo json_encode([
        'request_uri' => $_SERVER['REQUEST_URI'],
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'get_params' => $_GET,
        'post_params' => $_POST,
        'headers' => getallheaders(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});
// API Routes
Flight::route('POST /api/track/@action', [ApiController::class, 'track']);
Flight::route('GET /api/track/@action', [ApiController::class, 'track']);
Flight::route('OPTIONS /api/track/@action', [ApiController::class, 'track']);

// Static file serving (development only)
Flight::route('GET /assets/*', function($path) {
    $file = __DIR__ . '/assets/' . $path;
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon'
        ];
        
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
            header('Cache-Control: max-age=31536000'); // 1 year cache
            readfile($file);
        }
    } else {
        Flight::notFound();
    }
});

error_log("All routes configured, starting Flight");

try {
    Flight::start();
    error_log("Flight started successfully");
} catch (Exception $e) {
    error_log("Flight start error: " . $e->getMessage());
    if (ob_get_level()) {
        ob_end_clean();
    }
    echo "<h1>Flight Başlatma Hatası</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} finally {
    // Buffer'ı bitir
    if (ob_get_level()) {
        ob_end_flush();
    }
}