<?php
class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                // cPanel'de genellikle farklı bilgiler kullanılır
                $host = Flight::get('db.host') ?: 'localhost';
                $dbname = Flight::get('db.name') ?: 'networktoolkit_networktoolkit';
                $username = Flight::get('db.user') ?: 'networktoolkit_networktoolkit';
                $password = Flight::get('db.pass') ?: '=~@iNd,gJX$H{,*1';
                
                // Debug için bağlantı bilgilerini logla
                error_log("Database connection attempt - Host: $host, DB: $dbname, User: $username");
                
                self::$connection = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_TIMEOUT => 10 // Timeout ekle
                    ]
                );
                
                error_log("Database connection successful");
                
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                die("Database connection failed: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}