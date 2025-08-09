
<?php
class WebsiteController {
    private $websiteModel;
    private $analyticsModel;
    
    public function __construct() {
        $this->websiteModel = new Website();
        $this->analyticsModel = new Analytics();
    }
    
    public function index() {
        $websites = $this->analyticsModel->getWebsiteStats();
        
        Flight::render('websites/index', [
            'title' => 'Web Siteleri',
            'websites' => $websites
        ]);
    }
    
    public function create() {
        Flight::render('websites/create', ['title' => 'Yeni Site Ekle']);
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
            Flight::render('websites/create', [
                'title' => 'Yeni Site Ekle',
                'errors' => $errors,
                'domain' => $domain,
                'name' => $name
            ]);
            return;
        }
        
        if ($this->websiteModel->create($domain, $name)) {
            Flight::redirect('/websites?success=Site başarıyla eklendi.');
        } else {
            Flight::render('websites/create', [
                'title' => 'Yeni Site Ekle',
                'error' => 'Site eklenirken bir hata oluştu.',
                'domain' => $domain,
                'name' => $name
            ]);
        }
    }
    
    public function edit($id) {
        $website = $this->websiteModel->findById($id);
        
        if (!$website) {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        
        Flight::render('websites/edit', [
            'title' => 'Site Düzenle',
            'website' => $website
        ]);
    }
    
    public function update($id) {
        $website = $this->websiteModel->findById($id);
        
        if (!$website) {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        
        $domain = trim($_POST['domain'] ?? '');
        $name = trim($_POST['name'] ?? '');
        
        $errors = [];
        
        if (empty($domain)) {
            $errors[] = 'Domain alanı zorunludur.';
        } elseif ($this->websiteModel->domainExists($domain, $id)) {
            $errors[] = 'Bu domain zaten kayıtlı.';
        }
        
        if (empty($name)) {
            $errors[] = 'Site adı zorunludur.';
        }
        
        if (!empty($errors)) {
            Flight::render('websites/edit', [
                'title' => 'Site Düzenle',
                'errors' => $errors,
                'website' => array_merge($website, ['domain' => $domain, 'name' => $name])
            ]);
            return;
        }
        
        if ($this->websiteModel->update($id, $domain, $name)) {
            Flight::redirect('/websites?success=Site başarıyla güncellendi.');
        } else {
            Flight::render('websites/edit', [
                'title' => 'Site Düzenle',
                'error' => 'Site güncellenirken bir hata oluştu.',
                'website' => array_merge($website, ['domain' => $domain, 'name' => $name])
            ]);
        }
    }
    
    public function delete($id) {
        $website = $this->websiteModel->findById($id);
        
        if (!$website) {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        
        if ($this->websiteModel->delete($id)) {
            Flight::redirect('/websites?success=Site başarıyla silindi.');
        } else {
            Flight::redirect('/websites?error=Site silinirken bir hata oluştu.');
        }
    }
}