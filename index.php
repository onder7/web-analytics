<?php
// Output buffering başlat - bu header problemlerini önler
ob_start();

// Hata raporlamayı aktifleştir
error_reporting(E_ALL);
ini_set('display_errors', getenv('APP_ENV') === 'development' ? '1' : '0');
ini_set('log_errors', 1);
date_default_timezone_set('UTC');

function app_debug_log($message) {
    if (getenv('APP_ENV') === 'development') {
        error_log($message);
    }
}

// Session'ı güvenli başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
    'models/Lang.php',
    'models/User.php',
    'models/Website.php',
    'models/Analytics.php',
    'models/AnalyticsDashboard.php',
    'models/NotificationSettings.php',
    'models/SmtpMailer.php',
    'models/NotificationService.php',
    'models/LoginRateLimiter.php',
    'models/GeoIpLookup.php',
    'models/AppLogger.php',
    'models/InstallWizard.php',
    'models/SystemDiagnostics.php',
    'models/TotpAuthenticator.php',
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

// Gerekli dosyaları yükle
try {
    require 'vendor/autoload.php';
    require 'models/AppLogger.php';
    require 'config/database.php';
    require 'models/InstallWizard.php';
    require 'models/Lang.php';
    require 'models/User.php';
    require 'models/Website.php';
    require 'models/Analytics.php';
    require 'models/AnalyticsDashboard.php';
    require 'models/NotificationSettings.php';
    require 'models/SmtpMailer.php';
    require 'models/NotificationService.php';
    require 'models/LoginRateLimiter.php';
    require 'models/GeoIpLookup.php';
    require 'models/SystemDiagnostics.php';
    require 'models/TotpAuthenticator.php';
    require 'controllers/AuthController.php';
    require 'controllers/WebsiteController.php';
    require 'controllers/AnalyticsController.php';
    require 'controllers/ApiController.php';
    
} catch (Throwable $e) {
    AppLogger::error('bootstrap', "Dosya yükleme hatası: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    die('Uygulama başlatılamadı. Lütfen sunucu günlüklerini kontrol edin.');
}

// Flight sınıfının varlığını kontrol et
if (!class_exists('Flight')) {
    ob_end_clean();
    die("Flight sınıfı bulunamadı. Composer install kontrolü yapın.");
}

// Flight konfigürasyonu
try {
Flight::set('flight.views.path', 'views');
} catch (Throwable $e) {
    AppLogger::error('bootstrap', "Flight yapılandırma hatası: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    die('Uygulama yapılandırma hatası. Lütfen sunucu günlüklerini kontrol edin.');
}

// Route unconfigured or empty databases to the isolated installation wizard.
$installer = new InstallWizard();
$installationStatus = $installer->status();
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($requestPath === '/install') {
    if ($installationStatus['installed']) {
        Flight::redirect('/login');
    } else {
        $installer->handleRequest();
    }
    exit;
}
if (!$installationStatus['installed']) {
    Flight::redirect('/install');
    exit;
}

// Database bağlantısını test et
try {
    $testConnection = Database::getConnection();
} catch (Throwable $e) {
    AppLogger::error('database', "Veritabanı bağlantı testi başarısız: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    die('Veritabanına bağlanılamadı. Lütfen sunucu günlüklerini kontrol edin.');
}

// Views klasörünü kontrol et
if (!is_dir('views')) {
    AppLogger::error('bootstrap', 'Views klasörü bulunamadı.');
    ob_end_clean();
    die("Views klasörü bulunamadı.");
}

// CUSTOM RENDER FUNCTION - Layout sistemi için
Flight::map('render', function($template, $data = [], $key = null) {
    app_debug_log("Render function called for template: $template");
    
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
        AppLogger::error('view', "Şablon dosyası bulunamadı: $templatePath");
        throw new Exception("Template dosyası bulunamadı: $templatePath");
    }
    
    $authPages = ['auth/login', 'auth/register'];
    $isAuthTemplate = in_array($template, $authPages, true);
    $useLayout = ($isAuthTemplate && !isset($_SESSION['user_id'])) ||
                 (!$isAuthTemplate && isset($_SESSION['user_id']));

    if ($useLayout) {
        // Layout kullan
        $content = Flight::view()->fetch($template, $data);
        
        // Layout dosyasını kontrol et
        if (!file_exists('views/layout.php')) {
            AppLogger::error('view', 'Layout dosyası bulunamadı: views/layout.php');
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
Flight::map('error', function($ex) {
    AppLogger::error(
        'flight',
        $ex->getMessage() . ' (' . basename($ex->getFile()) . ':' . $ex->getLine() . ')'
    );
    if (getenv('APP_ENV') === 'development') {
        app_debug_log("Stack trace: " . $ex->getTraceAsString());
    }
    
    // Keep exception details in server logs only.
    if (ob_get_level()) {
        ob_end_clean();
    }
    http_response_code(500);
    if (strpos(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/api/') === 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Beklenmeyen bir sunucu hatası oluştu.']);
        return;
    }
    echo '<h1>Sunucu hatası</h1><p>İstek işlenirken beklenmeyen bir hata oluştu.</p>';
});

// 404 handling
Flight::map('notFound', function() {
    app_debug_log("404 Not Found: " . ($_SERVER['REQUEST_URI'] ?? ''));
    if (ob_get_level()) {
        ob_end_clean();
    }
    http_response_code(404);
    echo "<h1>404 - Sayfa Bulunamadı</h1>";
    echo "<p>İstenen sayfa bulunamadı.</p>";
});

// Authentication middleware
Flight::before('start', function() {
    // Dil sistemini başlat (her istekte)
    Lang::init();

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    app_debug_log("Request path: $path");
    
    $protected_routes = ['/dashboard', '/websites', '/analytics', '/logout'];
    
    foreach ($protected_routes as $route) {
        if (strpos($path, $route) === 0 && !isset($_SESSION['user_id'])) {
            app_debug_log("Redirecting to login for protected route: $path");
            $returnTo = $_SERVER['REQUEST_URI'] ?? '/dashboard';
            Flight::redirect('/login?return_to=' . rawurlencode($returnTo));
            return false;
        }
    }
});

// Routes
Flight::route('GET /', function() {
    app_debug_log("Root route accessed");
    if (isset($_SESSION['user_id'])) {
        app_debug_log("User logged in, redirecting to dashboard");
        Flight::redirect('/dashboard');
    } else {
        app_debug_log("User not logged in, redirecting to login");
        Flight::redirect('/login');
    }
});

// Test route - basit test için
Flight::route('GET /test', function() {
    app_debug_log("Test route accessed");
    if (ob_get_level()) {
        ob_end_clean();
    }
    echo "<h1>Test Başarılı!</h1>";
    echo "<p>Flight çalışıyor.</p>";
    echo "<p>Session durumu: " . (isset($_SESSION['user_id']) ? 'Giriş yapılmış' : 'Giriş yapılmamış') . "</p>";
    echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
});

// API Routes - CORS desteği ile
Flight::route('GET /api', function() {
    header('Content-Type: application/json');
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
Flight::route('POST /login/2fa', [AuthController::class, 'completeTwoFactorLogin']);
Flight::route('GET /login/cancel-2fa', [AuthController::class, 'cancelTwoFactorLogin']);
Flight::route('GET /register', [AuthController::class, 'registerForm']);
Flight::route('POST /register', [AuthController::class, 'register']);
Flight::route('GET /logout', [AuthController::class, 'logout']);

// Dil değiştirme
Flight::route('POST /change-language', function() {
    $locale = is_string($_POST['locale'] ?? null) ? $_POST['locale'] : '';
    Lang::switchTo($locale);
    $back = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
    // Redirect only to a local path, preserving the current page filters.
    $backPath = parse_url($back, PHP_URL_PATH) ?: '/dashboard';
    if (!is_string($backPath) || $backPath[0] !== '/' || strncmp($backPath, '//', 2) === 0 || strpos($backPath, '\\') !== false) {
        $backPath = '/dashboard';
    }
    $backQuery = parse_url($back, PHP_URL_QUERY);
    $backUrl = $backPath . (is_string($backQuery) && $backQuery !== '' ? '?' . $backQuery : '');
    Flight::redirect($backUrl);
});
// Dashboard
Flight::route('GET /dashboard', [AnalyticsController::class, 'dashboard']);
Flight::route('GET /analytics', function() {
    (new AnalyticsController())->analyticsPanel('overview');
});
Flight::route('GET /analytics/realtime', [AnalyticsController::class, 'realtimeActiveVisitors']);
Flight::route('GET /analytics/export', [AnalyticsController::class, 'exportAnalyticsCsv']);
Flight::route('POST /analytics/maintenance/optimize', [AnalyticsController::class, 'optimizeDatabaseTable']);
Flight::route('GET /analytics/@page', function($page) {
    (new AnalyticsController())->analyticsPanel($page);
});
Flight::route('POST /analytics/profile', [AnalyticsController::class, 'saveProfile']);
Flight::route('POST /analytics/profile/password', [AnalyticsController::class, 'changeAccountPassword']);
Flight::route('POST /analytics/security/2fa/setup', [AnalyticsController::class, 'beginTwoFactorSetup']);
Flight::route('POST /analytics/security/2fa/confirm', [AnalyticsController::class, 'confirmTwoFactorSetup']);
Flight::route('POST /analytics/security/2fa/disable', [AnalyticsController::class, 'disableTwoFactor']);
Flight::route('POST /analytics/security/2fa/cancel', [AnalyticsController::class, 'cancelTwoFactorSetup']);
Flight::route('POST /analytics/notifications', [AnalyticsController::class, 'saveNotifications']);

// Website Management Routes
Flight::route('GET /websites', [WebsiteController::class, 'index']);
Flight::route('GET /websites/create', [WebsiteController::class, 'create']);
Flight::route('POST /websites/create', [WebsiteController::class, 'store']);
Flight::route('GET /websites/invitations/@token', [WebsiteController::class, 'invitation']);
Flight::route('POST /websites/invitations/@token', [WebsiteController::class, 'acceptInvitation']);
Flight::route('GET /websites/@id/edit', [WebsiteController::class, 'edit']);
Flight::route('POST /websites/@id/edit', [WebsiteController::class, 'update']);
Flight::route('POST /websites/@id/rotate-key', [WebsiteController::class, 'rotateApiKey']);
Flight::route('POST /websites/@id/invitations', [WebsiteController::class, 'inviteMember']);
Flight::route('POST /websites/@id/members/@memberId/delete', [WebsiteController::class, 'removeMember']);
Flight::route('POST /websites/@id/invitations/@invitationId/delete', [WebsiteController::class, 'cancelInvitation']);
Flight::route('POST /websites/@id/delete', [WebsiteController::class, 'delete']);
Flight::route('GET /websites/@id/analytics', [AnalyticsController::class, 'websiteAnalytics']);
Flight::route('GET /api/status', function() {
    header('Content-Type: application/json');
    
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
        AppLogger::error('api-status', $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'database' => 'unavailable'
        ]);
    }
});
// API Routes
Flight::route('POST /api/track/@action', [ApiController::class, 'track']);

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

try {
    Flight::start();
} catch (Exception $e) {
    AppLogger::error('flight-start', $e->getMessage());
    if (getenv('APP_ENV') === 'development') {
        app_debug_log("Flight start stack trace: " . $e->getTraceAsString());
    }
    if (ob_get_level()) {
        ob_end_clean();
    }
    http_response_code(500);
    echo '<h1>Sunucu hatası</h1><p>İstek işlenirken beklenmeyen bir hata oluştu.</p>';
} finally {
    // Buffer'ı bitir
    if (ob_get_level()) {
        ob_end_flush();
    }
}
