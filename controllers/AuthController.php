<?php
class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function loginForm() {
        if (isset($_SESSION['user_id'])) {
            Flight::redirect('/dashboard');
            return;
        }

        if ($this->hasPendingTwoFactorLogin()) {
            $this->renderTwoFactorChallenge();
            return;
        }
        
        Flight::render('auth/login', [
            'title' => 'Giriş Yap',
            'returnTo' => $this->safeReturnTo($_GET['return_to'] ?? null)
        ]);
    }
    
    public function login() {
        $email = isset($_POST['email']) && is_string($_POST['email']) ? substr(trim($_POST['email']), 0, 254) : '';
        $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $rateLimiter = new LoginRateLimiter();
        $retryAfter = $rateLimiter->getRetryAfter($ip, $email);

        if ($retryAfter > 0) {
            Flight::response()->status(429);
            header('Retry-After: ' . $retryAfter);
            Flight::render('auth/login', [
                'title' => 'Giriş Yap',
                'email' => $email,
                'returnTo' => $this->safeReturnTo($_POST['return_to'] ?? null),
                'error' => 'Çok fazla başarısız giriş denemesi yapıldı. Lütfen ' . (int) ceil($retryAfter / 60) . ' dakika sonra tekrar deneyin.'
            ]);
            return;
        }

        if (empty($email) || empty($password)) {
            $rateLimiter->recordFailure($ip, $email);
            Flight::render('auth/login', [
                'title' => 'Giriş Yap',
                'email' => $email,
                'returnTo' => $this->safeReturnTo($_POST['return_to'] ?? null),
                'error' => 'Email ve şifre alanları zorunludur.'
            ]);
            return;
        }
        
        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            if (!empty($user['totp_enabled']) && !empty($user['totp_secret_encrypted'])) {
                session_regenerate_id(true);
                $_SESSION['pending_two_factor_login'] = [
                    'user_id' => (int) $user['id'],
                    'email' => $email,
                    'return_to' => $this->safeReturnTo($_POST['return_to'] ?? null),
                    'expires_at' => time() + 300
                ];
                $this->renderTwoFactorChallenge();
                return;
            }

            $this->completeLogin(
                $user,
                $rateLimiter,
                $ip,
                $email,
                $this->safeReturnTo($_POST['return_to'] ?? null)
            );
        } else {
            $rateLimiter->recordFailure($ip, $email);
            $retryAfter = $rateLimiter->getRetryAfter($ip, $email);
            if ($retryAfter > 0) {
                Flight::response()->status(429);
                header('Retry-After: ' . $retryAfter);
            }
            Flight::render('auth/login', [
                'title' => 'Giriş Yap',
                'email' => $email,
                'returnTo' => $this->safeReturnTo($_POST['return_to'] ?? null),
                'error' => $retryAfter > 0
                    ? 'Çok fazla başarısız giriş denemesi yapıldı. Lütfen ' . (int) ceil($retryAfter / 60) . ' dakika sonra tekrar deneyin.'
                    : 'Email veya şifre hatalı.'
            ]);
        }
    }

    public function completeTwoFactorLogin() {
        if (!$this->hasPendingTwoFactorLogin()) {
            unset($_SESSION['pending_two_factor_login']);
            Flight::redirect('/login');
            return;
        }

        $pending = $_SESSION['pending_two_factor_login'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $email = (string) $pending['email'];
        $rateLimiter = new LoginRateLimiter();
        $retryAfter = $rateLimiter->getRetryAfter($ip, $email);
        if ($retryAfter > 0) {
            Flight::response()->status(429);
            header('Retry-After: ' . $retryAfter);
            $this->renderTwoFactorChallenge(
                'Çok fazla başarısız deneme yapıldı. Lütfen ' . (int) ceil($retryAfter / 60) . ' dakika sonra tekrar deneyin.'
            );
            return;
        }

        $code = isset($_POST['code']) && is_string($_POST['code'])
            ? trim($_POST['code'])
            : '';
        $user = $this->userModel->findById((int) $pending['user_id']);
        $verified = false;
        if ($user && !empty($user['totp_enabled']) && !empty($user['totp_secret_encrypted'])) {
            try {
                $authenticator = new TotpAuthenticator();
                $secret = $authenticator->decryptSecret($user['totp_secret_encrypted']);
                $step = $authenticator->matchingStep($secret, $code);
                if ($step !== null) {
                    $verified = $this->userModel->consumeTotpStep((int) $user['id'], $step);
                } elseif ($this->userModel->consumeRecoveryCode((int) $user['id'], strtoupper($code))) {
                    $verified = true;
                }
            } catch (Throwable $error) {
                AppLogger::error('two-factor-login', $error->getMessage());
            }
        }

        if (!$verified || !$user) {
            $rateLimiter->recordFailure($ip, $email);
            $retryAfter = $rateLimiter->getRetryAfter($ip, $email);
            if ($retryAfter > 0) {
                Flight::response()->status(429);
                header('Retry-After: ' . $retryAfter);
            }
            $this->renderTwoFactorChallenge(
                $retryAfter > 0
                    ? 'Çok fazla başarısız deneme yapıldı. Lütfen ' . (int) ceil($retryAfter / 60) . ' dakika sonra tekrar deneyin.'
                    : 'Doğrulama kodu geçersiz veya daha önce kullanılmış.'
            );
            return;
        }

        $this->completeLogin($user, $rateLimiter, $ip, $email, $pending['return_to']);
    }

    public function cancelTwoFactorLogin() {
        unset($_SESSION['pending_two_factor_login']);
        Flight::redirect('/login');
    }

    private function hasPendingTwoFactorLogin() {
        if (empty($_SESSION['pending_two_factor_login'])
            || !is_array($_SESSION['pending_two_factor_login'])
            || (int) ($_SESSION['pending_two_factor_login']['expires_at'] ?? 0) < time()) {
            unset($_SESSION['pending_two_factor_login']);
            return false;
        }

        return true;
    }

    private function renderTwoFactorChallenge($error = null) {
        $pending = $_SESSION['pending_two_factor_login'] ?? [];
        Flight::render('auth/login', [
            'title' => 'İki Faktörlü Doğrulama',
            'twoFactorChallenge' => true,
            'returnTo' => $pending['return_to'] ?? null,
            'error' => $error
        ]);
    }

    private function completeLogin($user, LoginRateLimiter $rateLimiter, $ip, $email, $returnTo = null) {
        $rateLimiter->clear($ip, $email);
        session_regenerate_id(true);
        unset($_SESSION['pending_two_factor_login']);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        Flight::redirect($this->safeReturnTo($returnTo) ?: '/dashboard');
    }
    
    public function registerForm() {
        if (isset($_SESSION['user_id'])) {
            Flight::redirect('/dashboard');
            return;
        }
        
        Flight::render('auth/register', ['title' => 'Kayıt Ol']);
    }
    
    public function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'Kullanıcı adı zorunludur.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email zorunludur.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir email adresi giriniz.';
        } elseif ($this->userModel->emailExists($email)) {
            $errors[] = 'Bu email adresi zaten kullanılmaktadır.';
        }
        
        if (empty($password)) {
            $errors[] = 'Şifre zorunludur.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Şifre en az 6 karakter olmalıdır.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Şifreler eşleşmiyor.';
        }
        
        if (!empty($errors)) {
            Flight::render('auth/register', [
                'title' => 'Kayıt Ol',
                'errors' => $errors,
                'username' => $username,
                'email' => $email
            ]);
            return;
        }
        
        if ($this->userModel->create($username, $email, $password)) {
            Flight::render('auth/login', [
                'title' => 'Giriş Yap',
                'success' => 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.'
            ]);
        } else {
            Flight::render('auth/register', [
                'title' => 'Kayıt Ol',
                'error' => 'Kayıt sırasında bir hata oluştu.',
                'username' => $username,
                'email' => $email
            ]);
        }
    }
    
    public function logout() {
        session_destroy();
        Flight::redirect('/login');
    }

    private function safeReturnTo($value) {
        if (!is_string($value) || $value === '' || $value[0] !== '/' || strpos($value, '//') === 0 ||
            strpos($value, "\r") !== false || strpos($value, "\n") !== false) {
            return null;
        }
        return $value;
    }
}
