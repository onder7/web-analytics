# Web Analytics Tracking System / Web İstatistikleri Takip Sistemi

DEMO : https://networktoolkit.site/
user: onder7@gmail.com
pass.: asdasd123

[English](#english) | [Türkçe](#turkish)

<img width="1912" height="924" alt="msedge_las3RNaZ4T" src="https://github.com/user-attachments/assets/ed7d6fb6-238e-4527-9397-5ed185ffd27c" />

<img width="1912" height="924" alt="msedge_ol2wL3lSCb" src="https://github.com/user-attachments/assets/c0dfc115-fe5d-46c7-bc5d-259ae25baa95" />

<img width="1912" height="924" alt="msedge_YW600xafUZ" src="https://github.com/user-attachments/assets/afbf800d-7e3a-4d23-ac53-95e34ff57211" />




<a name="english"></a>
## 🇬🇧 English

### 📊 Overview

A lightweight, self-hosted web analytics system built with FlightPHP framework. Track your website visitors, page views, events, and get detailed statistics without relying on third-party services.

### ✨ Features

- **Real-time Analytics**: Track visitors and page views in real-time
- **Event Tracking**: Monitor custom events, clicks, forms, and user interactions
- **Performance Metrics**: Measure page load times and performance indicators
- **Privacy-Focused**: Self-hosted solution, full control over your data
- **RESTful API**: Easy integration with any website or application
- **Lightweight**: Built with FlightPHP micro-framework
- **Multi-site Support**: Track multiple websites with a single installation

### 🚀 Quick Start

#### Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- Composer

#### Installation

1. Clone the repository:
```bash
git clone https://github.com/onder7/web-analytics.git
cd web-analytics
```

2. Install dependencies:
```bash
composer install
```

3. Create database and import schema:
```bash
mysql -u root -p -e "CREATE DATABASE web_analytics"
mysql -u root -p web_analytics < database/schema.sql
```

4. Configure database connection in `public/index.php`:
```php
Flight::set('db.host', 'localhost');
Flight::set('db.name', 'web_analytics');
Flight::set('db.user', 'your_username');
Flight::set('db.pass', 'your_password');
```

5. Set up virtual host pointing to `/public` directory

6. Generate API key for your website:
```sql
INSERT INTO websites (domain, name, api_key) 
VALUES ('example.com', 'My Website', SHA2(CONCAT(NOW(), RAND()), 256));
```

### 📝 Usage

Add the tracking code to your website:

```html
<script src="https://your-analytics-domain.com/tracker/analytics-tracker.js"></script>
<script>
    WebAnalytics.init('YOUR_API_KEY', {
        apiEndpoint: 'https://your-analytics-domain.com/api',
        debug: false
    });
</script>
```

### 📡 API Endpoints

- `POST /api/track/pageview` - Track page views
- `POST /api/track/event` - Track custom events
- `GET /api/stats/overview` - Get statistics overview
- `GET /api/stats/realtime` - Get real-time visitor count

### 🛠️ Configuration

#### Apache Configuration

Ensure your `.htaccess` files are properly configured. The system includes:
- Root `.htaccess` - Redirects to public directory
- `public/.htaccess` - Handles routing
- `public/assets/.htaccess` - Optimizes static files
- `public/tracker/.htaccess` - Configures tracker access

#### Nginx Configuration

If using Nginx, see the included `nginx.conf` example.

### 📊 Dashboard

Access the analytics dashboard at `https://your-analytics-domain.com/dashboard.html`

### 🔒 Security

- API key authentication for all tracking requests
- Rate limiting to prevent abuse
- CORS headers for cross-origin requests
- SQL injection protection with prepared statements

### 📄 License

MIT License - see LICENSE file for details

---

<a name="turkish"></a>
## 🇹🇷 Türkçe

### 📊 Genel Bakış

FlightPHP framework ile geliştirilmiş, hafif ve kendi sunucunuzda barındırabileceğiniz bir web analitik sistemi. Üçüncü taraf servislere bağımlı olmadan web sitenizin ziyaretçilerini, sayfa görüntülemelerini, olayları takip edin ve detaylı istatistikler alın.

### ✨ Özellikler

- **Gerçek Zamanlı Analitik**: Ziyaretçileri ve sayfa görüntülemelerini gerçek zamanlı takip edin
- **Olay Takibi**: Özel olayları, tıklamaları, formları ve kullanıcı etkileşimlerini izleyin
- **Performans Metrikleri**: Sayfa yükleme sürelerini ve performans göstergelerini ölçün
- **Gizlilik Odaklı**: Kendi sunucunuzda barındırın, verilerinizin tam kontrolü sizde
- **RESTful API**: Her web sitesi veya uygulama ile kolay entegrasyon
- **Hafif**: FlightPHP micro-framework ile geliştirilmiş
- **Çoklu Site Desteği**: Tek kurulum ile birden fazla web sitesini takip edin

### 🚀 Hızlı Başlangıç

#### Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- mod_rewrite aktif Apache
- Composer

#### Kurulum

1. Repository'yi klonlayın:
```bash
git clone https://github.com/kullaniciadi/web-analytics.git
cd web-analytics
```

2. Bağımlılıkları yükleyin:
```bash
composer install
```

3. Veritabanı oluşturun ve şemayı içe aktarın:
```bash
mysql -u root -p -e "CREATE DATABASE web_analytics"
mysql -u root -p web_analytics < database/schema.sql
```

4. `public/index.php` dosyasında veritabanı bağlantısını yapılandırın:
```php
Flight::set('db.host', 'localhost');
Flight::set('db.name', 'web_analytics');
Flight::set('db.user', 'kullanici_adi');
Flight::set('db.pass', 'sifre');
```

5. Virtual host'u `/public` dizinine yönlendirin

6. Web siteniz için API anahtarı oluşturun:
```sql
INSERT INTO websites (domain, name, api_key) 
VALUES ('example.com', 'Benim Sitem', SHA2(CONCAT(NOW(), RAND()), 256));
```

### 📝 Kullanım

Web sitenize takip kodunu ekleyin:

```html
<script src="https://analytics-domain.com/tracker/analytics-tracker.js"></script>
<script>
    WebAnalytics.init('API_ANAHTARINIZ', {
        apiEndpoint: 'https://analytics-domain.com/api',
        debug: false
    });
</script>
```

### 📡 API Endpoint'leri

- `POST /api/track/pageview` - Sayfa görüntülemelerini takip et
- `POST /api/track/event` - Özel olayları takip et
- `GET /api/stats/overview` - İstatistik özetini al
- `GET /api/stats/realtime` - Gerçek zamanlı ziyaretçi sayısını al

### 🛠️ Yapılandırma

#### Apache Yapılandırması

`.htaccess` dosyalarınızın doğru yapılandırıldığından emin olun. Sistem şunları içerir:
- Ana dizin `.htaccess` - Public dizinine yönlendirir
- `public/.htaccess` - Routing işlemlerini yönetir
- `public/assets/.htaccess` - Statik dosyaları optimize eder
- `public/tracker/.htaccess` - Tracker erişimini yapılandırır

#### Nginx Yapılandırması

Nginx kullanıyorsanız, dahil edilen `nginx.conf` örneğine bakın.

### 📊 Kontrol Paneli

Analitik kontrol paneline `https://analytics-domain.com/dashboard.html` adresinden erişin

### 🔒 Güvenlik

- Tüm takip istekleri için API anahtarı doğrulaması
- Kötüye kullanımı önlemek için rate limiting
- Cross-origin istekleri için CORS başlıkları
- Prepared statements ile SQL injection koruması

### 📄 Lisans

MIT Lisansı - detaylar için LICENSE dosyasına bakın

---

## 🚀 GitHub'a Gönderme / Push to GitHub

### Türkçe Adımlar:

1. **GitHub'da yeni repository oluşturun:**
   - GitHub.com'a gidin
   - "New repository" butonuna tıklayın
   - Repository adını girin (örn: `web-analytics`)
   - Public veya Private seçin
   - README eklemeden oluşturun

2. **Lokal projenizi hazırlayın:**
```bash
# Proje dizinine gidin
cd /path/to/web-analytics

# Git'i başlatın
git init

# .gitignore dosyası oluşturun
echo "vendor/
.env
.idea/
.vscode/
*.log
.DS_Store" > .gitignore

# Tüm dosyaları ekleyin
git add .

# İlk commit
git commit -m "İlk commit: Web Analytics Takip Sistemi"

# GitHub repository'nizi remote olarak ekleyin
git remote add origin https://github.com/kullaniciadi/web-analytics.git

# Main branch'e geçin
git branch -M main

# GitHub'a gönderin
git push -u origin main
```

3. **Sonraki güncellemeler için:**
```bash
# Değişiklikleri ekleyin
git add .

# Commit yapın
git commit -m "Açıklayıcı commit mesajı"

# GitHub'a gönderin
git push
```

### English Steps:

1. **Create new repository on GitHub:**
   - Go to GitHub.com
   - Click "New repository" button
   - Enter repository name (e.g., `web-analytics`)
   - Choose Public or Private
   - Create without README

2. **Prepare your local project:**
```bash
# Navigate to project directory
cd /path/to/web-analytics

# Initialize Git
git init

# Create .gitignore file
echo "vendor/
.env
.idea/
.vscode/
*.log
.DS_Store" > .gitignore

# Add all files
git add .

# Initial commit
git commit -m "Initial commit: Web Analytics Tracking System"

# Add GitHub repository as remote
git remote add origin https://github.com/onder7/web-analytics.git

# Switch to main branch
git branch -M main

# Push to GitHub
git push -u origin main
```

3. **For future updates:**
```bash
# Add changes
git add .

# Commit changes
git commit -m "Descriptive commit message"

# Push to GitHub
git push
```

### 💡 İpuçları / Tips

**Türkçe:**
- Hassas bilgileri (şifreler, API anahtarları) asla commit etmeyin
- `.env` dosyası kullanın ve `.gitignore`'a ekleyin
- Anlamlı commit mesajları yazın
- Düzenli olarak push yapın
- Branch kullanarak yeni özellikler geliştirin

**English:**
- Never commit sensitive information (passwords, API keys)
- Use `.env` file and add it to `.gitignore`
- Write meaningful commit messages
- Push regularly
- Use branches for new features

### 🏷️ Önerilen Etiketler / Suggested Tags

`analytics` `php` `flightphp` `web-analytics` `tracking` `self-hosted` `privacy` `mysql` `rest-api` `real-time`
