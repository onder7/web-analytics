<?php
$projectName = basename(dirname(__DIR__));
$configFileName = '.' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $projectName) . '-database.php';
$configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $configFileName;
if (is_file($configPath)) {
    $databaseConfig = require $configPath;
    if (!is_array($databaseConfig)) {
        throw new RuntimeException('Veritabanı yapılandırma dosyası geçersiz.');
    }
} else {
    $databaseConfig = [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: 3306,
        'name' => getenv('DB_NAME') ?: 'web-analytics',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASSWORD') ?: ''
    ];
}

if (class_exists('Flight')) {
    foreach (['host', 'port', 'name', 'user', 'pass'] as $setting) {
        if (array_key_exists($setting, $databaseConfig)) {
            Flight::set('db.' . $setting, $databaseConfig[$setting]);
        }
    }
}

class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $host = Flight::get('db.host') ?: 'localhost';
                $port = (int) (Flight::get('db.port') ?: 3306);
                $dbname = Flight::get('db.name') ?: 'web-analytics';
                $username = Flight::get('db.user') ?: 'root';
                $password = Flight::get('db.pass') ?: '';
                
                self::$connection = new PDO(
                    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_TIMEOUT => 10
                    ]
                );
                self::$connection->exec("SET time_zone = '+00:00'");
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new RuntimeException('Veritabanı bağlantısı kurulamadı.', 0, $e);
            }
        }
        
        return self::$connection;
    }
}