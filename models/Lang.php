<?php
/**
 * Lang — Hafif çoklu dil yöneticisi
 *
 * Kullanım:
 *   Lang::init('tr');           // bootstrap sırasında
 *   __('auth.login_button')     // 'Giriş Yap'
 *   __('overview.vs_prev', ['%d' => 12])  // basit yer tutucu
 *   Lang::locale()              // 'tr' veya 'en'
 */
class Lang {
    private static string $locale   = 'tr';
    private static array  $strings  = [];
    private static array  $fallback = [];

    /** Desteklenen diller */
    public const SUPPORTED = ['tr', 'en'];
    public const DEFAULT   = 'tr';

    /**
     * Dil sistemini başlat.
     * @param string|null $locale  null → session/cookie/Accept-Language'dan otomatik tespit
     */
    public static function init(?string $locale = null): void {
        self::$locale = self::resolve($locale);

        $file = dirname(__DIR__) . '/lang/' . self::$locale . '.php';
        self::$strings = file_exists($file) ? (require $file) : [];

        // Fallback: varsayılan dil (tr) her zaman yüklenir
        if (self::$locale !== self::DEFAULT) {
            $fallbackFile = dirname(__DIR__) . '/lang/' . self::DEFAULT . '.php';
            self::$fallback = file_exists($fallbackFile) ? (require $fallbackFile) : [];
        }
    }

    /** Aktif locale'i döndür */
    public static function locale(): string {
        return self::$locale;
    }

    /**
     * Çeviri anahtarını çöz.
     * $params içindeki :key veya {key} yer tutucularını değiştir.
     */
    public static function get(string $key, array $params = []): string {
        $text = self::$strings[$key]
            ?? self::$fallback[$key]
            ?? $key;  // anahtar bulunamazsa kendisini döndür

        if (empty($params)) {
            return $text;
        }

        foreach ($params as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, (string) $value, $text);
            $text = str_replace('{' . $placeholder . '}', (string) $value, $text);
        }

        return $text;
    }

    /**
     * Dili değiştir ve session'a kaydet.
     * Sayfa yenilemesi gerekmez — çağıran yönlendirme yapar.
     */
    public static function switchTo(string $locale): void {
        if (!in_array($locale, self::SUPPORTED, true)) {
            return;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['lang'] = $locale;
        setcookie('lang', $locale, [
            'expires'  => time() + 60 * 60 * 24 * 365,
            'path'     => '/',
            'samesite' => 'Lax',
            'httponly' => true,
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private static function resolve(?string $requested): string {
        // 1. Açıkça istek yapılmışsa
        if ($requested !== null && in_array($requested, self::SUPPORTED, true)) {
            return $requested;
        }

        // 2. Session
        if (!empty($_SESSION['lang']) && in_array($_SESSION['lang'], self::SUPPORTED, true)) {
            return $_SESSION['lang'];
        }

        // 3. Cookie
        if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], self::SUPPORTED, true)) {
            return $_COOKIE['lang'];
        }

        // 4. Accept-Language header (sadece birincil dil etiketi)
        $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($acceptLang !== '') {
            preg_match_all('/([a-z]{2})(?:-[A-Za-z]{2})?(?:;q=[\d.]+)?/', strtolower($acceptLang), $matches);
            foreach ($matches[1] as $tag) {
                if (in_array($tag, self::SUPPORTED, true)) {
                    return $tag;
                }
            }
        }

        return self::DEFAULT;
    }
}

/**
 * Global kısayol: view'larda __('key') şeklinde kullan
 */
if (!function_exists('__')) {
    function __(string $key, array $params = []): string {
        return Lang::get($key, $params);
    }
}
