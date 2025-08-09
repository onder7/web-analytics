<?php
/**
 * Session Configuration
 * Bu dosyayı index.php'den önce include edebilirsiniz
 */

// Session ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Session lifetime (24 saat)
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime', 86400);

// Session name
session_name('NETWORK_ANALYTICS_SESSION');

// Session start function with error handling
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        if (!session_start()) {
            error_log('Failed to start session');
            return false;
        }
    }
    return true;
}