<?php
class AnalyticsController {
    private $analyticsModel;
    private $websiteModel;
    private $userModel;
    
    public function __construct() {
        $this->analyticsModel = new Analytics();
        $this->websiteModel = new Website();
        $this->userModel = new User();
    }
    
    public function dashboard() {
        $this->analyticsPanel('overview');
    }

    public function realtimeActiveVisitors() {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Oturum açmanız gerekiyor.']);
            return;
        }

        $websiteId = filter_var($_GET['website_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$websiteId || $websiteId < 1) {
            http_response_code(400);
            echo json_encode(['error' => 'Geçerli bir web sitesi seçilmelidir.']);
            return;
        }

        $activeVisitors = (new AnalyticsDashboard())->getActiveVisitors(
            $websiteId,
            (int) $_SESSION['user_id']
        );
        if ($activeVisitors === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Web sitesi bulunamadı.']);
            return;
        }

        echo json_encode(['active_now' => $activeVisitors]);
    }

    public function analyticsPanel($page = 'overview') {
        if (!isset($_SESSION['user_id'])) {
            $returnTo = $_SERVER['REQUEST_URI'] ?? '/dashboard';
            Flight::redirect('/login?return_to=' . rawurlencode($returnTo));
            return;
        }

        $pageTitles = [
            'overview' => ['Genel Bakış', 'Web sitenizin performans özeti'],
            'statistics' => ['İstatistikler', 'Gerçek zamanlı canlı veri akışı'],
            'traffic' => ['Trafik Analizi', 'Trafik kaynakları ve dönüşüm analizi'],
            'visitors' => ['Ziyaretçiler', 'Ziyaretçi detayları ve coğrafi dağılım'],
            'pages' => ['Sayfalar', 'Sayfa bazlı performans analizi'],
            'sources' => ['Kaynaklar', 'Trafik kaynakları ve arama kelimeleri'],
            'settings' => ['Ayarlar', 'Hesap ve izleme ayarları'],
            'notifications' => ['Bildirimler', 'E-posta bildirimleri ve SMTP sunucu ayarları'],
            'system' => ['Sistem Durumu', 'Veritabanı bakımı ve sistem günlükleri']
        ];

        if (!isset($pageTitles[$page])) {
            Flight::notFound();
            return;
        }

        $userId = (int) $_SESSION['user_id'];
        $isSystemAdmin = SystemDiagnostics::isAdmin($userId);

        $dashboard = new AnalyticsDashboard();
        $websites = $dashboard->getWebsites($userId);
        $requestedWebsiteId = filter_var($_GET['website_id'] ?? null, FILTER_VALIDATE_INT);
        $selectedWebsite = null;

        foreach ($websites as $website) {
            if ($requestedWebsiteId && (int) $website['id'] === $requestedWebsiteId) {
                $selectedWebsite = $website;
                break;
            }
        }

        if ($selectedWebsite === null && !empty($websites)) {
            $selectedWebsite = $websites[0];
        }

        $websiteId = $selectedWebsite ? (int) $selectedWebsite['id'] : null;
        $selectedWebsite = $websiteId ? $dashboard->getWebsite($websiteId, $userId) : null;
        $timezoneName = $selectedWebsite['timezone'] ?? 'UTC';
        [$dateRange, $rangeError] = $this->resolveDateRange($_GET, new DateTimeZone($timezoneName));
        $db = Database::getConnection();
        $userStatement = $db->prepare('SELECT username, email FROM users WHERE id = ?');
        $userStatement->execute([(int) ($_SESSION['user_id'] ?? 0)]);
        $profile = $userStatement->fetch() ?: [
            'username' => $_SESSION['username'] ?? 'Kullanıcı',
            'email' => ''
        ];
        $profileFlash = $_SESSION['profile_flash'] ?? null;
        unset($_SESSION['profile_flash']);
        $twoFactorStatus = null;
        $twoFactorAvailable = false;
        $twoFactorSetup = null;
        $recoveryCodes = $_SESSION['two_factor_recovery_codes'] ?? null;
        unset($_SESSION['two_factor_recovery_codes']);
        if ($page === 'settings') {
            $twoFactorAvailable = $this->userModel->isTwoFactorAvailable();
            if ($twoFactorAvailable) {
                $twoFactorStatus = $this->userModel->getTwoFactorStatus($userId);
            }
            if ($twoFactorAvailable && !empty($_SESSION['two_factor_setup_expires_at'])) {
                if ((int) $_SESSION['two_factor_setup_expires_at'] < time()) {
                    $this->userModel->clearPendingTotpSecret($userId);
                    unset($_SESSION['two_factor_setup_expires_at']);
                } elseif (!empty($twoFactorStatus['totp_pending_secret_encrypted'])) {
                    $twoFactorSetup = (new TotpAuthenticator())->decryptSecret(
                        $twoFactorStatus['totp_pending_secret_encrypted']
                    );
                }
            }
        }
        if (!isset($_SESSION['profile_csrf'])) {
            $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));
        }

        $data = $page === 'system' ? [] : $dashboard->load($websiteId, $userId, $dateRange);
        $systemData = [];
        if ($page === 'system') {
            $systemData = (new SystemDiagnostics())->getOverview($_GET['severity'] ?? '');
            unset($_SESSION['maintenance_flash']);
        }
        $notificationSettings = null;
        $notificationFlash = $_SESSION['notification_flash'] ?? null;
        unset($_SESSION['notification_flash']);
        $notificationForm = $_SESSION['notification_form'] ?? null;
        unset($_SESSION['notification_form']);

        if ($page === 'notifications') {
            $notificationSettings = (new NotificationSettings())->getForUser((int) $_SESSION['user_id']);
            if (is_array($notificationForm)) {
                $notificationSettings = array_merge($notificationSettings, $notificationForm);
            }
            if (!isset($_SESSION['notification_csrf'])) {
                $_SESSION['notification_csrf'] = bin2hex(random_bytes(32));
            }
        }

        Flight::render('analytics-panel/' . $page, array_merge($data, $systemData, [
            'title' => $pageTitles[$page][0],
            'subtitle' => $pageTitles[$page][1],
            'page' => $page,
            'pageTitles' => $pageTitles,
            'websites' => $websites,
            'website' => $selectedWebsite,
            'websiteId' => $websiteId,
            'dateRange' => $dateRange,
            'dateRangeError' => $rangeError,
            'profile' => $profile,
            'profileFlash' => $profileFlash,
            'profileCsrf' => $_SESSION['profile_csrf'],
            'twoFactorStatus' => $twoFactorStatus,
            'twoFactorAvailable' => $twoFactorAvailable,
            'twoFactorSetup' => $twoFactorSetup,
            'twoFactorRecoveryCodes' => $recoveryCodes,
            'isSystemAdmin' => $isSystemAdmin,
            'notificationSettings' => $notificationSettings,
            'notificationFlash' => $notificationFlash,
            'notificationCsrf' => $_SESSION['notification_csrf'] ?? '',
            'analyticsPanel' => true
        ]));
    }

    public function saveProfile() {
        $this->assertProfileCsrf('profile');

        $userId = (int) $_SESSION['user_id'];
        $username = isset($_POST['username']) && is_string($_POST['username'])
            ? trim($_POST['username'])
            : '';
        $email = isset($_POST['email']) && is_string($_POST['email'])
            ? trim($_POST['email'])
            : '';

        if ($username === '' || (function_exists('mb_strlen') ? mb_strlen($username) : strlen($username)) > 50) {
            $this->setProfileFlash('error', __('profile.username_invalid'), 'profile');
            Flight::redirect('/analytics/settings?tab=profile');
            return;
        }
        if ($email === '' || strlen($email) > 191 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setProfileFlash('error', __('profile.email_invalid'), 'profile');
            Flight::redirect('/analytics/settings?tab=profile');
            return;
        }

        try {
            if ($this->userModel->emailExistsForAnotherUser($email, $userId)) {
                $this->setProfileFlash('error', __('profile.email_in_use'), 'profile');
                Flight::redirect('/analytics/settings?tab=profile');
                return;
            }
            if ($this->userModel->usernameExistsForAnotherUser($username, $userId)) {
                $this->setProfileFlash('error', __('profile.username_in_use'), 'profile');
                Flight::redirect('/analytics/settings?tab=profile');
                return;
            }

            $this->userModel->updateProfile($userId, $username, $email);
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $this->setProfileFlash('success', __('profile.saved'), 'profile');
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                $this->setProfileFlash('error', __('profile.account_value_in_use'), 'profile');
            } else {
                AppLogger::error('profile-update', $error->getMessage());
                $this->setProfileFlash('error', __('profile.save_failed'), 'profile');
            }
        } catch (Throwable $error) {
            AppLogger::error('profile-update', $error->getMessage());
            $this->setProfileFlash('error', __('profile.save_failed'), 'profile');
        }

        Flight::redirect('/analytics/settings?tab=profile');
    }

    public function changeAccountPassword() {
        $this->assertProfileCsrf('security');

        $currentPassword = isset($_POST['current_password']) && is_string($_POST['current_password'])
            ? $_POST['current_password']
            : '';
        $newPassword = isset($_POST['new_password']) && is_string($_POST['new_password'])
            ? $_POST['new_password']
            : '';
        $confirmPassword = isset($_POST['confirm_password']) && is_string($_POST['confirm_password'])
            ? $_POST['confirm_password']
            : '';

        if ($currentPassword === '') {
            $this->setProfileFlash('error', __('profile.current_password_required'), 'security');
        } elseif (strlen($newPassword) < 8 || strlen($newPassword) > 72) {
            $this->setProfileFlash('error', __('profile.password_length'), 'security');
        } elseif ($newPassword !== $confirmPassword) {
            $this->setProfileFlash('error', __('profile.password_mismatch'), 'security');
        } elseif ($newPassword === $currentPassword) {
            $this->setProfileFlash('error', __('profile.password_unchanged'), 'security');
        } else {
            try {
                if (!$this->userModel->changePassword(
                    (int) $_SESSION['user_id'],
                    $currentPassword,
                    $newPassword
                )) {
                    $this->setProfileFlash('error', __('profile.current_password_invalid'), 'security');
                } else {
                    session_regenerate_id(true);
                    $this->setProfileFlash('success', __('profile.password_changed'), 'security');
                }
            } catch (Throwable $error) {
                AppLogger::error('password-change', $error->getMessage());
                $this->setProfileFlash('error', __('profile.password_change_failed'), 'security');
            }
        }

        Flight::redirect('/analytics/settings?tab=security');
    }

    public function beginTwoFactorSetup() {
        $this->assertProfileCsrf('security');
        if (!$this->userModel->isTwoFactorAvailable()) {
            $this->setProfileFlash('error', __('profile.2fa_migration_required'), 'security');
            Flight::redirect('/analytics/settings?tab=security');
            return;
        }
        $userId = (int) $_SESSION['user_id'];
        $currentPassword = isset($_POST['current_password']) && is_string($_POST['current_password'])
            ? $_POST['current_password']
            : '';

        try {
            $status = $this->userModel->getTwoFactorStatus($userId);
            if ((int) $status['totp_enabled'] === 1) {
                $this->setProfileFlash('error', __('profile.2fa_already_enabled'), 'security');
            } elseif (!$this->userModel->verifyPassword($userId, $currentPassword)) {
                $this->setProfileFlash('error', __('profile.current_password_invalid'), 'security');
            } else {
                $authenticator = new TotpAuthenticator();
                $secret = $authenticator->generateSecret();
                if (!$this->userModel->savePendingTotpSecret(
                    $userId,
                    $authenticator->encryptSecret($secret)
                )) {
                    throw new RuntimeException('2FA kurulumu başlatılamadı.');
                }
                $_SESSION['two_factor_setup_expires_at'] = time() + 600;
                $this->setProfileFlash('success', __('profile.2fa_setup_started'), 'security');
            }
        } catch (Throwable $error) {
            AppLogger::error('two-factor-setup', $error->getMessage());
            $this->setProfileFlash('error', __('profile.2fa_setup_failed'), 'security');
        }

        Flight::redirect('/analytics/settings?tab=security');
    }

    public function confirmTwoFactorSetup() {
        $this->assertProfileCsrf('security');
        if (!$this->userModel->isTwoFactorAvailable()) {
            $this->setProfileFlash('error', __('profile.2fa_migration_required'), 'security');
            Flight::redirect('/analytics/settings?tab=security');
            return;
        }
        $userId = (int) $_SESSION['user_id'];

        if ((int) ($_SESSION['two_factor_setup_expires_at'] ?? 0) < time()) {
            $this->userModel->clearPendingTotpSecret($userId);
            unset($_SESSION['two_factor_setup_expires_at']);
            $this->setProfileFlash('error', __('profile.2fa_setup_expired'), 'security');
            Flight::redirect('/analytics/settings?tab=security');
            return;
        }

        $code = isset($_POST['code']) && is_string($_POST['code']) ? trim($_POST['code']) : '';
        try {
            $status = $this->userModel->getTwoFactorStatus($userId);
            if (empty($status['totp_pending_secret_encrypted'])) {
                throw new RuntimeException('Bekleyen 2FA kurulumu bulunamadı.');
            }
            $authenticator = new TotpAuthenticator();
            $secret = $authenticator->decryptSecret($status['totp_pending_secret_encrypted']);
            $step = $authenticator->matchingStep($secret, $code);
            if ($step === null) {
                $this->setProfileFlash('error', __('profile.2fa_code_invalid'), 'security');
                Flight::redirect('/analytics/settings?tab=security');
                return;
            }

            $recoveryCodes = $authenticator->recoveryCodes();
            $recoveryHashes = array_map(function($recoveryCode) {
                return password_hash($recoveryCode, PASSWORD_DEFAULT);
            }, $recoveryCodes);
            if (!$this->userModel->enableTotp($userId, $recoveryHashes, $step)) {
                throw new RuntimeException('2FA etkinleştirilemedi.');
            }

            session_regenerate_id(true);
            unset($_SESSION['two_factor_setup_expires_at']);
            $_SESSION['two_factor_recovery_codes'] = $recoveryCodes;
            $this->setProfileFlash('success', __('profile.2fa_enabled'), 'security');
        } catch (Throwable $error) {
            AppLogger::error('two-factor-enable', $error->getMessage());
            $this->setProfileFlash('error', __('profile.2fa_setup_failed'), 'security');
        }

        Flight::redirect('/analytics/settings?tab=security');
    }

    public function disableTwoFactor() {
        $this->assertProfileCsrf('security');
        if (!$this->userModel->isTwoFactorAvailable()) {
            $this->setProfileFlash('error', __('profile.2fa_migration_required'), 'security');
            Flight::redirect('/analytics/settings?tab=security');
            return;
        }
        $userId = (int) $_SESSION['user_id'];
        $password = isset($_POST['current_password']) && is_string($_POST['current_password'])
            ? $_POST['current_password']
            : '';
        $code = isset($_POST['code']) && is_string($_POST['code']) ? trim($_POST['code']) : '';

        try {
            if (!$this->userModel->verifyPassword($userId, $password)) {
                $this->setProfileFlash('error', __('profile.current_password_invalid'), 'security');
                Flight::redirect('/analytics/settings?tab=security');
                return;
            }

            $status = $this->userModel->getTwoFactorStatus($userId);
            if ((int) $status['totp_enabled'] !== 1 || empty($status['totp_secret_encrypted'])) {
                $this->setProfileFlash('error', __('profile.2fa_not_enabled'), 'security');
                Flight::redirect('/analytics/settings?tab=security');
                return;
            }

            $authenticator = new TotpAuthenticator();
            $secret = $authenticator->decryptSecret($status['totp_secret_encrypted']);
            $step = $authenticator->matchingStep($secret, $code);
            $verified = $step !== null
                ? $this->userModel->consumeTotpStep($userId, $step)
                : $this->userModel->consumeRecoveryCode($userId, strtoupper($code));
            if (!$verified) {
                $this->setProfileFlash('error', __('profile.2fa_code_invalid'), 'security');
                Flight::redirect('/analytics/settings?tab=security');
                return;
            }

            if (!$this->userModel->disableTotp($userId)) {
                throw new RuntimeException('2FA devre dışı bırakılamadı.');
            }
            unset($_SESSION['two_factor_setup_expires_at'], $_SESSION['two_factor_recovery_codes']);
            $this->setProfileFlash('success', __('profile.2fa_disabled'), 'security');
        } catch (Throwable $error) {
            AppLogger::error('two-factor-disable', $error->getMessage());
            $this->setProfileFlash('error', __('profile.2fa_disable_failed'), 'security');
        }

        Flight::redirect('/analytics/settings?tab=security');
    }

    public function cancelTwoFactorSetup() {
        $this->assertProfileCsrf('security');
        if (!$this->userModel->isTwoFactorAvailable()) {
            $this->setProfileFlash('error', __('profile.2fa_migration_required'), 'security');
            Flight::redirect('/analytics/settings?tab=security');
            return;
        }
        $this->userModel->clearPendingTotpSecret((int) $_SESSION['user_id']);
        unset($_SESSION['two_factor_setup_expires_at']);
        $this->setProfileFlash('success', __('profile.2fa_setup_cancelled'), 'security');
        Flight::redirect('/analytics/settings?tab=security');
    }

    private function assertProfileCsrf($tab) {
        if (!isset($_SESSION['user_id'])) {
            Flight::redirect('/login');
            exit;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $expectedToken = $_SESSION['profile_csrf'] ?? '';
        if (!is_string($submittedToken) || !is_string($expectedToken)
            || strlen($expectedToken) !== 64 || !hash_equals($expectedToken, $submittedToken)) {
            $this->setProfileFlash('error', __('profile.session_expired'), $tab);
            Flight::redirect('/analytics/settings?tab=' . rawurlencode($tab));
            exit;
        }
    }

    private function setProfileFlash($type, $message, $tab) {
        $_SESSION['profile_flash'] = [
            'type' => $type,
            'message' => $message,
            'tab' => $tab
        ];
    }

    public function optimizeDatabaseTable() {
        if (!isset($_SESSION['user_id'])) {
            Flight::redirect('/login');
            return;
        }
        if (!SystemDiagnostics::isAdmin((int) $_SESSION['user_id'])) {
            http_response_code(403);
            echo 'Bu işlem yalnızca sistem yöneticilerine açıktır.';
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $expectedToken = $_SESSION['maintenance_csrf'] ?? '';
        if (!is_string($submittedToken) || !is_string($expectedToken)
            || strlen($expectedToken) !== 64 || !hash_equals($expectedToken, $submittedToken)) {
            $_SESSION['maintenance_flash'] = [
                'type' => 'error',
                'message' => 'Oturum doğrulaması başarısız oldu. Sayfayı yenileyip tekrar deneyin.'
            ];
            Flight::redirect('/analytics/system');
            return;
        }

        try {
            $table = is_string($_POST['table'] ?? null) ? $_POST['table'] : '';
            $result = (new SystemDiagnostics())->optimizeTable($table);
            AppLogger::info('database-maintenance', "{$table} tablosu optimize edildi: {$result}");
            $_SESSION['maintenance_flash'] = [
                'type' => 'success',
                'message' => "{$table} tablosu optimize edildi."
            ];
        } catch (Throwable $error) {
            AppLogger::error('database-maintenance', $error->getMessage());
            $_SESSION['maintenance_flash'] = [
                'type' => 'error',
                'message' => 'Tablo optimize edilemedi: ' . $error->getMessage()
            ];
        }

        Flight::redirect('/analytics/system');
    }

    public function saveNotifications() {
        if (!isset($_SESSION['user_id'])) {
            Flight::redirect('/login');
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $expectedToken = $_SESSION['notification_csrf'] ?? '';
        if (!is_string($submittedToken) || !is_string($expectedToken) || strlen($expectedToken) !== 64 || !hash_equals($expectedToken, $submittedToken)) {
            $_SESSION['notification_flash'] = ['type' => 'error', 'message' => 'Oturum doğrulaması başarısız oldu. Sayfayı yenileyip tekrar deneyin.'];
            Flight::redirect('/analytics/notifications');
            return;
        }

        $postString = function($key, $default = '') {
            $value = $_POST[$key] ?? $default;
            return is_string($value) || is_numeric($value) ? trim((string) $value) : $default;
        };
        $settings = [
            'recipient_email' => $postString('recipient_email'),
            'enabled' => isset($_POST['enabled']) ? 1 : 0,
            'daily_summary' => isset($_POST['daily_summary']) ? 1 : 0,
            'weekly_summary' => isset($_POST['weekly_summary']) ? 1 : 0,
            'monthly_summary' => isset($_POST['monthly_summary']) ? 1 : 0,
            'traffic_anomaly' => isset($_POST['traffic_anomaly']) ? 1 : 0,
            'tracking_errors' => isset($_POST['tracking_errors']) ? 1 : 0,
            'smtp_host' => $postString('smtp_host'),
            'smtp_port' => filter_var($postString('smtp_port', '587'), FILTER_VALIDATE_INT),
            'smtp_encryption' => $postString('smtp_encryption', 'tls'),
            'smtp_username' => $postString('smtp_username'),
            'from_email' => $postString('from_email'),
            'from_name' => $postString('from_name')
        ];
        $smtpPassword = isset($_POST['smtp_password']) && is_string($_POST['smtp_password']) ? $_POST['smtp_password'] : '';
        $clearSmtpPassword = isset($_POST['clear_smtp_password']);
        $formValues = $settings;
        $errors = [];

        if ($settings['recipient_email'] !== '' && !filter_var($settings['recipient_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Bildirim alıcısı için geçerli bir e-posta adresi girin.';
        }
        if ($settings['enabled'] && $settings['recipient_email'] === '') {
            $errors[] = 'E-posta bildirimlerini açmak için alıcı e-posta adresi gereklidir.';
        }
        if ($settings['smtp_host'] !== '' && preg_match('/[\r\n\s]/', $settings['smtp_host'])) {
            $errors[] = 'SMTP sunucu adresi boşluk veya satır sonu içeremez.';
        }
        if ($settings['smtp_port'] === false || $settings['smtp_port'] < 1 || $settings['smtp_port'] > 65535) {
            $errors[] = 'SMTP portu 1 ile 65535 arasında olmalıdır.';
        }
        if (!in_array($settings['smtp_encryption'], ['tls', 'ssl', 'none'], true)) {
            $errors[] = 'SMTP şifreleme seçeneği geçersiz.';
        }
        if ($settings['from_email'] !== '' && !filter_var($settings['from_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Gönderen adresi için geçerli bir e-posta adresi girin.';
        }
        foreach ([
            'smtp_host' => 255,
            'recipient_email' => 191,
            'smtp_username' => 191,
            'from_email' => 191,
            'from_name' => 191
        ] as $field => $maxLength) {
            if (strlen($settings[$field]) > $maxLength) {
                $errors[] = 'Bir veya daha fazla SMTP alanı izin verilen uzunluğu aşıyor.';
                break;
            }
        }
        if ($settings['enabled'] && $settings['smtp_host'] === '') {
            $errors[] = 'E-posta bildirimlerini açmak için SMTP sunucu adresi gereklidir.';
        }
        if ($settings['enabled'] && $settings['from_email'] === '') {
            $errors[] = 'E-posta bildirimlerini açmak için gönderen adresi gereklidir.';
        }
        if ($clearSmtpPassword && $smtpPassword !== '') {
            $errors[] = 'Şifreyi değiştirmek veya silmek için iki işlemi aynı anda seçmeyin.';
        }

        if ($errors) {
            $_SESSION['notification_flash'] = ['type' => 'error', 'message' => implode(' ', $errors)];
            $_SESSION['notification_form'] = $formValues;
            Flight::redirect('/analytics/notifications');
            return;
        }

        (new NotificationSettings())->saveForUser(
            (int) $_SESSION['user_id'],
            $settings,
            $smtpPassword,
            $clearSmtpPassword
        );
        $_SESSION['notification_flash'] = ['type' => 'success', 'message' => 'Bildirim ve SMTP ayarları kaydedildi.'];
        Flight::redirect('/analytics/notifications');
    }

    public function testSmtp() {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

        if (!isset($_SESSION['user_id'])) {
            $this->respondSmtpTest(401, ['message' => __('notif.smtp_test_session_expired')]);
            return;
        }

        $submittedToken = $_POST['csrf_token'] ?? '';
        $expectedToken = $_SESSION['notification_csrf'] ?? '';
        if (!is_string($submittedToken) || !is_string($expectedToken) ||
            strlen($expectedToken) !== 64 || !hash_equals($expectedToken, $submittedToken)) {
            $this->respondSmtpTest(403, ['message' => __('notif.smtp_test_csrf_failed')]);
            return;
        }

        if (isset($_POST['clear_smtp_password'])) {
            $this->respondSmtpTest(422, ['message' => __('notif.smtp_test_save_before_clear')]);
            return;
        }

        $postString = function($key) {
            return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
        };
        $port = filter_var($postString('smtp_port'), FILTER_VALIDATE_INT);
        $settings = [
            'recipient_email' => $postString('recipient_email'),
            'smtp_host' => $postString('smtp_host'),
            'smtp_port' => $port,
            'smtp_encryption' => $postString('smtp_encryption'),
            'smtp_username' => $postString('smtp_username'),
            'from_email' => $postString('from_email'),
            'from_name' => $postString('from_name')
        ];
        $smtpPassword = isset($_POST['smtp_password']) && is_string($_POST['smtp_password'])
            ? $_POST['smtp_password']
            : '';

        if (!filter_var($settings['recipient_email'], FILTER_VALIDATE_EMAIL) ||
            !filter_var($settings['from_email'], FILTER_VALIDATE_EMAIL) ||
            $settings['smtp_host'] === '' ||
            $port === false || $port < 1 || $port > 65535 ||
            !in_array($settings['smtp_encryption'], ['tls', 'ssl', 'none'], true) ||
            strlen($settings['smtp_host']) > 255 ||
            strlen($settings['smtp_username']) > 191 ||
            strlen($settings['from_name']) > 191 ||
            strlen($smtpPassword) > 4096) {
            $this->respondSmtpTest(422, ['message' => __('notif.smtp_test_invalid_settings')]);
            return;
        }

        try {
            if ($smtpPassword === '') {
                $savedSettings = (new NotificationSettings())->getForSending((int) $_SESSION['user_id']);
                $smtpPassword = $savedSettings['smtp_password'] ?? '';
            }
            $settings['smtp_password'] = $smtpPassword;
            (new SmtpMailer())->send(
                $settings,
                __('notif.smtp_test_subject'),
                __('notif.smtp_test_body')
            );
            $this->respondSmtpTest(200, ['message' => __('notif.smtp_test_success')]);
        } catch (Throwable $error) {
            AppLogger::error('smtp-test', $error->getMessage());
            $detail = preg_replace('/[\r\n]+/', ' ', $error->getMessage());
            $this->respondSmtpTest(502, [
                'message' => __('notif.smtp_test_failed') . ' ' . substr((string) $detail, 0, 400)
            ]);
        }
    }

    private function respondSmtpTest($status, array $payload) {
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function exportAnalyticsCsv() {
        $type = is_string($_GET['type'] ?? null) ? $_GET['type'] : '';
        $headers = [
            'pages' => ['Sayfa URL', 'Sayfa başlığı', 'Görüntülenme', 'Benzersiz görüntülenme'],
            'visitors' => ['Ziyaretçi ID', 'Ziyaretçi tipi', 'İlk ziyaret', 'Son ziyaret', 'Görüntülenme', 'Son sayfa', 'Şehir', 'Ülke', 'Cihaz'],
            'traffic' => ['Kaynak', 'Kategori', 'Görüntülenme', 'Pay (%)']
        ];
        if (!isset($headers[$type])) {
            http_response_code(400);
            echo 'Geçersiz dışa aktarma türü.';
            return;
        }

        $websiteId = filter_var($_GET['website_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$websiteId || $websiteId < 1) {
            http_response_code(400);
            echo 'Geçerli bir web sitesi seçilmelidir.';
            return;
        }

        $dashboard = new AnalyticsDashboard();
        $website = $dashboard->getWebsite($websiteId, (int) $_SESSION['user_id']);
        if (!$website) {
            http_response_code(404);
            echo 'Web sitesi bulunamadı.';
            return;
        }
        [$dateRange, $rangeError] = $this->resolveDateRange($_GET, new DateTimeZone($website['timezone'] ?: 'UTC'));
        if ($rangeError !== null) {
            http_response_code(400);
            echo $rangeError;
            return;
        }

        $rows = $dashboard->getExportRows(
            $websiteId,
            (int) $_SESSION['user_id'],
            $type,
            $dateRange
        );
        if ($rows === null) {
            http_response_code(404);
            echo 'Web sitesi bulunamadı.';
            return;
        }

        $filename = 'webstats-' . $type . '-' . $dateRange['start_date'] . '-' . $dateRange['end_date'] . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store');
        $output = fopen('php://output', 'w');
        if ($output === false) {
            throw new RuntimeException('CSV çıktısı açılamadı.');
        }
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers[$type], ';', '"', '\\');
        foreach ($rows as $row) {
            if ($type === 'pages') {
                $values = [$row['page_url'], $row['page_title'], $row['views'], $row['unique_views']];
            } elseif ($type === 'visitors') {
                $values = [
                    $row['visitor_id'], $row['visitor_type'], $row['first_visit'], $row['last_visit'],
                    $row['pageviews'], $row['last_page'], $row['city'], $row['country'], $row['device_type']
                ];
            } else {
                $values = [$row['source'], $row['category_label'], $row['visits'], $row['percentage']];
            }
            fputcsv($output, array_map([$this, 'protectCsvCell'], $values), ';', '"', '\\');
        }
        fclose($output);
    }

    private function protectCsvCell($value) {
        $value = (string) $value;
        if ($value !== '' && preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $value)) {
            return "'" . $value;
        }
        return $value;
    }

    private function resolveDateRange($query, DateTimeZone $timezone) {
        $period = is_string($query['period'] ?? null) ? $query['period'] : '30d';
        $today = new DateTimeImmutable('today', $timezone);
        $periodDays = ['7d' => 7, '30d' => 30, '90d' => 90];
        $error = null;
        if (isset($periodDays[$period])) {
            $rangeStart = $today->modify('-' . ($periodDays[$period] - 1) . ' days');
            $rangeEnd = $today;
            $label = 'Son ' . $periodDays[$period] . ' gün';
        } elseif ($period === 'custom') {
            $startInput = is_string($query['start_date'] ?? null) ? $query['start_date'] : '';
            $endInput = is_string($query['end_date'] ?? null) ? $query['end_date'] : '';
            $rangeStart = DateTimeImmutable::createFromFormat('!Y-m-d', $startInput, $timezone);
            $rangeEnd = DateTimeImmutable::createFromFormat('!Y-m-d', $endInput, $timezone);
            $validStart = $rangeStart && $rangeStart->format('Y-m-d') === $startInput;
            $validEnd = $rangeEnd && $rangeEnd->format('Y-m-d') === $endInput;
            if (!$validStart || !$validEnd || $rangeStart > $rangeEnd) {
                $error = 'Özel tarih aralığı geçersiz.';
                $period = '30d';
                $rangeStart = $today->modify('-29 days');
                $rangeEnd = $today;
                $label = 'Son 30 gün';
            } else {
                $label = 'Özel tarih aralığı';
            }
        } else {
            $error = 'Geçersiz tarih aralığı seçimi.';
            $period = '30d';
            $rangeStart = $today->modify('-29 days');
            $rangeEnd = $today;
            $label = 'Son 30 gün';
        }
        return [[
            'period' => $period,
            'start_date' => $rangeStart->format('Y-m-d'),
            'end_date' => $rangeEnd->format('Y-m-d'),
            'label' => $label
        ], $error];
    }

        public function websiteAnalytics($id) {
        $website = $this->websiteModel->findById($id, (int) $_SESSION['user_id']);
        
        if (!$website) {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        
        try {
            $totalVisitors = $this->analyticsModel->getTotalVisitors($id);
            $totalPageViews = $this->analyticsModel->getTotalPageViews($id);
            $dailyStats = $this->analyticsModel->getDailyStats($id, 30);
            $popularPages = $this->analyticsModel->getPopularPages($id, 10);
            $browserStats = $this->analyticsModel->getBrowserStats($id);
            $osStats = $this->analyticsModel->getOSStats($id);
            
            // Ek istatistikler
            $deviceStats = $this->analyticsModel->getDeviceStats($id);
            $topReferrers = $this->analyticsModel->getTopReferrers($id, 5);
            $bounceRate = $this->analyticsModel->getBounceRate($id);
            $avgSessionDuration = $this->analyticsModel->getAverageSessionDuration($id);
            
            // Breadcrumb
            $breadcrumb = [
                ['title' => 'Web Siteleri', 'url' => '/websites', 'icon' => 'fas fa-globe'],
                ['title' => $website['name'], 'icon' => 'fas fa-chart-bar']
            ];
            
            Flight::render('analytics/website', [
                'title' => $website['name'] . ' - Analytics',
                'breadcrumb' => $breadcrumb,
                'website' => $website,
                'totalVisitors' => $totalVisitors,
                'totalPageViews' => $totalPageViews,
                'dailyStats' => $dailyStats,
                'popularPages' => $popularPages,
                'browserStats' => $browserStats,
                'osStats' => $osStats,
                'deviceStats' => $deviceStats,
                'topReferrers' => $topReferrers,
                'bounceRate' => $bounceRate,
                'avgSessionDuration' => $avgSessionDuration
            ]);
            
        } catch (Exception $e) {
            // Hata durumunda
            Flight::redirect('/websites?error=Analytics verilerinde hata oluştu: ' . $e->getMessage());
        }
    }
   
}
