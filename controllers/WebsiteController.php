
<?php
class WebsiteController {
    private $websiteModel;
    private $analyticsModel;
    
    public function __construct() {
        $this->websiteModel = new Website();
        $this->analyticsModel = new Analytics();
    }
    
    public function index() {
        $websites = $this->analyticsModel->getWebsiteStats((int) $_SESSION['user_id'], true);
        if (!isset($_SESSION['website_csrf'])) {
            $_SESSION['website_csrf'] = bin2hex(random_bytes(32));
        }
        
        Flight::render('websites/index', $this->panelData([
            'title' => 'Web Siteleri',
            'websites' => $websites,
            'websiteCsrf' => $_SESSION['website_csrf']
        ]));
    }
    
    public function create() {
        Flight::render('websites/create', $this->panelData([
            'title' => 'Yeni Site Ekle'
        ]));
    }
    
    public function store() {
        $domain = trim($_POST['domain'] ?? '');
        $name = trim($_POST['name'] ?? '');
        
        $errors = [];
        
        if (empty($domain)) {
            $errors[] = 'Domain alanı zorunludur.';
        } elseif ($this->websiteModel->domainExists($domain)) {
            $errors[] = 'Bu domain zaten kayıtlı.';
        }
        
        if (empty($name)) {
            $errors[] = 'Site adı zorunludur.';
        }
        
        if (!empty($errors)) {
            Flight::render('websites/create', $this->panelData([
                'title' => 'Yeni Site Ekle',
                'errors' => $errors,
                'domain' => $domain,
                'name' => $name
            ]));
            return;
        }
        
        if ($this->websiteModel->create((int) $_SESSION['user_id'], $domain, $name)) {
            Flight::redirect('/websites?success=Site başarıyla eklendi.');
        } else {
            Flight::render('websites/create', $this->panelData([
                'title' => 'Yeni Site Ekle',
                'error' => 'Site eklenirken bir hata oluştu.',
                'domain' => $domain,
                'name' => $name
            ]));
        }
    }
    
    public function edit($id) {
        $website = $this->websiteModel->findById($id, (int) $_SESSION['user_id']);
        
        if (!$website) {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        
        if (!isset($_SESSION['website_csrf'])) {
            $_SESSION['website_csrf'] = bin2hex(random_bytes(32));
        }
        $team = ($website['access_role'] ?? '') === 'owner'
            ? $this->websiteModel->getTeam($id, (int) $_SESSION['user_id'])
            : ['members' => [], 'invitations' => []];
        $invitationLink = $_SESSION['website_invitation_link'] ?? null;
        unset($_SESSION['website_invitation_link']);
        $flash = $_SESSION['website_flash'] ?? null;
        unset($_SESSION['website_flash']);

        Flight::render('websites/edit', $this->panelData([
            'title' => 'Site Düzenle',
            'website' => $website,
            'team' => $team,
            'websiteCsrf' => $_SESSION['website_csrf'],
            'invitationLink' => $invitationLink,
            'websiteFlash' => $flash,
            'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC)
        ]));
    }
    
    public function update($id) {
        $website = $this->websiteModel->findById($id, (int) $_SESSION['user_id']);
        
        if (!$website) {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        if (!in_array($website['access_role'] ?? '', ['owner', 'write'], true)) {
            Flight::redirect('/websites?error=Bu site için düzenleme yetkiniz yok.');
            return;
        }
        if (!$this->validWebsiteCsrf()) {
            Flight::redirect('/websites/' . (int) $id . '/edit?error=Oturum doğrulaması başarısız.');
            return;
        }
        
        $domain = trim($_POST['domain'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $timezone = is_string($_POST['timezone'] ?? null) ? $_POST['timezone'] : '';
        
        $errors = [];
        
        if (empty($domain)) {
            $errors[] = 'Domain alanı zorunludur.';
        } elseif ($this->websiteModel->domainExists($domain, $id)) {
            $errors[] = 'Bu domain zaten kayıtlı.';
        }
        
        if (empty($name)) {
            $errors[] = 'Site adı zorunludur.';
        }
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC);
        $timezones[] = 'UTC';
        $team = ($website['access_role'] ?? '') === 'owner'
            ? $this->websiteModel->getTeam($id, (int) $_SESSION['user_id'])
            : ['members' => [], 'invitations' => []];
        if (!in_array($timezone, $timezones, true)) {
            $errors[] = 'Geçerli bir saat dilimi seçin.';
        }
        
        if (!empty($errors)) {
            Flight::render('websites/edit', $this->panelData([
                'title' => 'Site Düzenle',
                'errors' => $errors,
                'website' => array_merge($website, ['domain' => $domain, 'name' => $name, 'timezone' => $timezone]),
                'websiteCsrf' => $_SESSION['website_csrf'],
                'team' => $team,
                'timezones' => $timezones
            ]));
            return;
        }
        
        if ($this->websiteModel->update($id, (int) $_SESSION['user_id'], $domain, $name, $timezone)) {
            Flight::redirect('/websites?success=Site başarıyla güncellendi.');
        } else {
            Flight::render('websites/edit', $this->panelData([
                'title' => 'Site Düzenle',
                'error' => 'Site güncellenirken bir hata oluştu.',
                'website' => array_merge($website, ['domain' => $domain, 'name' => $name, 'timezone' => $timezone]),
                'websiteCsrf' => $_SESSION['website_csrf'],
                'team' => $team,
                'timezones' => $timezones
            ]));
        }
    }
    
    public function delete($id) {
        $website = $this->websiteModel->findById($id, (int) $_SESSION['user_id']);
        if (!$website || ($website['access_role'] ?? '') !== 'owner') {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        if (!$this->validWebsiteCsrf()) {
            Flight::redirect('/websites?error=Oturum doğrulaması başarısız.');
            return;
        }
        if ($this->websiteModel->delete($id, (int) $_SESSION['user_id'])) {
            Flight::redirect('/websites?success=Site başarıyla silindi.');
        } else {
            Flight::redirect('/websites?error=Site silinirken bir hata oluştu.');
        }
    }

    public function rotateApiKey($id) {
            $userId = (int) $_SESSION['user_id'];
            $website = $this->websiteModel->findById($id, $userId);
            if (!$website || ($website['access_role'] ?? '') !== 'owner') {
                Flight::redirect('/websites?error=API anahtarını yalnızca site sahibi yenileyebilir.');
                return;
            }
            if (!$this->validWebsiteCsrf()) {
                Flight::redirect('/websites/' . (int) $id . '/edit?error=Oturum doğrulaması başarısız.');
                return;
            }
            if (!$this->websiteModel->rotateApiKey($id, $userId)) {
                throw new RuntimeException('API anahtarı yenilenemedi.');
            }
            $_SESSION['website_flash'] = 'API anahtarı yenilendi; eski anahtar artık geçersiz.';
            Flight::redirect('/websites/' . (int) $id . '/edit');
    }

    public function inviteMember($id) {
            $website = $this->websiteModel->findById($id, (int) $_SESSION['user_id']);
            if (!$website || ($website['access_role'] ?? '') !== 'owner') {
                Flight::redirect('/websites?error=Takım erişimini yalnızca site sahibi yönetebilir.');
                return;
            }
            if (!$this->validWebsiteCsrf()) {
                Flight::redirect('/websites/' . (int) $id . '/edit?error=Oturum doğrulaması başarısız.');
                return;
            }
            $email = isset($_POST['email']) && is_string($_POST['email']) ? $_POST['email'] : '';
            $role = isset($_POST['role']) && is_string($_POST['role']) ? $_POST['role'] : 'read';
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $baseUrl = rtrim((string) (getenv('APP_URL') ?: $scheme . '://' . ($_SERVER['SERVER_NAME'] ?? 'localhost')), '/');
            $baseUrlParts = parse_url($baseUrl);
            if (!filter_var($baseUrl, FILTER_VALIDATE_URL) || !is_array($baseUrlParts) ||
                !in_array(strtolower($baseUrlParts['scheme'] ?? ''), ['http', 'https'], true) ||
                empty($baseUrlParts['host'])) {
                $_SESSION['website_flash'] = 'Davet bağlantısı oluşturulamadı; APP_URL ayarını kontrol edin.';
                Flight::redirect('/websites/' . (int) $id . '/edit');
                return;
            }
            try {
                $token = $this->websiteModel->createInvitation($id, (int) $_SESSION['user_id'], $email, $role);
                $_SESSION['website_invitation_link'] = $baseUrl . '/websites/invitations/' . $token;
            } catch (InvalidArgumentException $error) {
                $_SESSION['website_flash'] = $error->getMessage();
            }
            Flight::redirect('/websites/' . (int) $id . '/edit');
    }

    public function removeMember($id, $memberId) {
            $this->mutateTeamAccess($id, function() use ($id, $memberId) {
                $this->websiteModel->removeMember($id, (int) $_SESSION['user_id'], $memberId);
            });
    }

    public function cancelInvitation($id, $invitationId) {
            $this->mutateTeamAccess($id, function() use ($id, $invitationId) {
                $this->websiteModel->cancelInvitation($id, (int) $_SESSION['user_id'], $invitationId);
            });
    }

    private function mutateTeamAccess($id, $mutation) {
            $website = $this->websiteModel->findById($id, (int) $_SESSION['user_id']);
            if (!$website || ($website['access_role'] ?? '') !== 'owner') {
                Flight::redirect('/websites?error=Takım erişimini yalnızca site sahibi yönetebilir.');
                return;
            }
            if (!$this->validWebsiteCsrf()) {
                Flight::redirect('/websites/' . (int) $id . '/edit?error=Oturum doğrulaması başarısız.');
                return;
            }
            $mutation();
            Flight::redirect('/websites/' . (int) $id . '/edit');
    }

    public function invitation($token) {
            $invitation = $this->websiteModel->getInvitation($token);
            if (!$invitation) {
                Flight::notFound();
                return;
            }
            if (!isset($_SESSION['website_csrf'])) {
                $_SESSION['website_csrf'] = bin2hex(random_bytes(32));
            }
            Flight::render('websites/accept_invitation', [
                'title' => 'Site Davetini Kabul Et',
                'subtitle' => 'Bir web sitesi için ekip erişimi',
                'page' => 'invitation',
                'pageTitles' => ['invitation' => ['Site Daveti', 'Bir web sitesi için ekip erişimi']],
                'analyticsPanel' => true,
                'websites' => (new AnalyticsDashboard())->getWebsites((int) $_SESSION['user_id']),
                'websiteId' => null,
                'website' => null,
                'dateRange' => ['period' => '30d', 'label' => 'Davet'],
                'invitation' => $invitation,
                'token' => $token,
                'csrfToken' => $_SESSION['website_csrf'],
                'profile' => [
                    'username' => $_SESSION['username'] ?? 'Kullanıcı',
                    'email' => $_SESSION['email'] ?? ''
                ]
            ]);
    }

    public function acceptInvitation($token) {
            if (!$this->validWebsiteCsrf()) {
                Flight::redirect('/websites/invitations/' . rawurlencode($token) . '?error=Oturum doğrulaması başarısız.');
                return;
            }
            $db = Database::getConnection();
            $statement = $db->prepare('SELECT email FROM users WHERE id = ?');
            $statement->execute([(int) $_SESSION['user_id']]);
            $email = $statement->fetchColumn();
            if (!$email || !$this->websiteModel->acceptInvitation($token, (int) $_SESSION['user_id'], $email)) {
                Flight::redirect('/websites?error=Davet geçersiz, süresi dolmuş veya farklı bir hesaba gönderilmiş.');
                return;
            }
            Flight::redirect('/websites?success=Site daveti kabul edildi.');
    }

    private function validWebsiteCsrf() {
            $submitted = $_POST['csrf_token'] ?? '';
            $expected = $_SESSION['website_csrf'] ?? '';
            return is_string($submitted) && is_string($expected) &&
                strlen($expected) === 64 && hash_equals($expected, $submitted);
    }

    private function panelData($data) {
        $db = Database::getConnection();
        $statement = $db->prepare('SELECT username, email FROM users WHERE id = ?');
        $statement->execute([(int) ($_SESSION['user_id'] ?? 0)]);
        $profile = $statement->fetch() ?: [
            'username' => $_SESSION['username'] ?? 'Kullanıcı',
            'email' => ''
        ];
        $websites = $data['websites'] ?? $this->analyticsModel->getWebsiteStats((int) $_SESSION['user_id']);
        $currentWebsite = $data['website'] ?? null;

        return array_merge($data, [
            'analyticsPanel' => true,
            'page' => 'websites',
            'subtitle' => 'Takip kodlarını ve site performansını yönetin',
            'pageTitles' => ['websites' => ['Web Siteleri', 'Takip kodlarını ve site performansını yönetin']],
            'websites' => $websites,
            'websiteId' => $currentWebsite['id'] ?? null,
            'profile' => $profile
        ]);
    }
}