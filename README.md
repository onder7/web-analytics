# Web Analytics Tracking System / Web İstatistikleri Takip Sistemi

Public demo: https://networktoolkit.site/

[English](#english) | [Türkçe](#turkish)



https://github.com/user-attachments/assets/78b6f5a9-2dfa-46e7-a40c-b2721dce556c





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

On a new installation, open the application URL and follow the setup wizard to connect an **empty** MySQL/MariaDB database and create the first administrator. The wizard reads the table definitions from the root `db.sql` file and refuses to modify a database that already contains tables. `db.sql` contains schema only; it has no prefilled users, websites, or analytics records. Do not import it manually before running the wizard, or the database will no longer be empty. The wizard saves connection settings to a project-specific hidden PHP file in the parent directory of the web root (for this project, `.web-analytics.local-database.php`); keep that file private and back it up.

If setup stops partway through with a SQL error, MySQL does not automatically roll back table-creation statements. The wizard will not delete partially created tables; create a new empty database and run setup again against it. Do not manually drop tables until you have confirmed the database is dedicated to this failed setup and contains no data you need.

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

3. Create an empty database (the database user must have permission to create tables):
```bash
mysql -u root -p -e "CREATE DATABASE web_analytics"
```

4. Point the virtual host to the project root and open its URL. Enter the database and first administrator details in the setup wizard. The wizard reads only table definitions from `db.sql` and creates the administrator; do not import the file manually before setup.

5. Sign in with the administrator account and add your first website from the Websites page.

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

After signing in, access the analytics dashboard at `/dashboard` or `/analytics`. The dashboard includes overview, statistics, traffic analysis, visitors, pages, sources, and settings views backed by the existing MySQL analytics tables.

### 🔒 Security

- API key authentication for all tracking requests
- Rate limiting to prevent abuse
- CORS headers for cross-origin requests
- SQL injection protection with prepared statements
- Optional authenticator-app TOTP two-factor authentication with one-time recovery codes

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

Yeni bir kurulumda uygulama adresini açıp sihirbazı izleyin: **boş** MySQL/MariaDB veritabanı bağlantısını girin ve ilk yönetici hesabını oluşturun. Sihirbaz kök dizindeki `db.sql` dosyasından yalnızca tablo şemalarını okur; dosyada önceden eklenmiş kullanıcı, site veya analiz verisi yoktur. Sihirbaz tablo içeren veritabanlarına dokunmaz; bu nedenle `db.sql` dosyasını sihirbazdan önce elle içe aktarmayın. Bağlantı bilgileri web kök dizininin bir üst klasöründeki projeye özel gizli bir PHP dosyasına kaydedilir (`web-analytics.local` projesinde `.web-analytics.local-database.php`); bu dosyayı gizli tutup yedekleyin. Sihirbazı kullanmak için PHP PDO MySQL eklentisi ve proje üst klasörüne yazma izni gereklidir.

Kurulum SQL hatasıyla yarıda kalırsa MySQL tablo işlemlerini otomatik geri almaz. Sihirbazın güvenlik kontrolü kısmen oluşmuş tabloları silmez; kuruluma devam etmek için yeni ve boş bir veritabanı oluşturup sihirbazı o veritabanıyla yeniden çalıştırın. Mevcut tabloları elle silmeden önce veritabanının yalnızca bu yarım kurulum için ayrıldığını doğrulayın.

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

3. Boş bir veritabanı oluşturun (veritabanı kullanıcısının tablo oluşturma izni olmalıdır):
```bash
mysql -u root -p -e "CREATE DATABASE web_analytics"
```

4. Virtual host'u proje kök dizinine yönlendirin ve uygulama adresini açın. Kurulum sihirbazında veritabanı ve ilk yönetici bilgilerini girin. Sihirbaz `db.sql` dosyasından yalnızca tablo şemalarını kurar ve ilk yönetici hesabını oluşturur; dosyayı sihirbazdan önce elle içe aktarmayın.

5. Yönetici hesabıyla giriş yapın ve Web Siteleri sayfasından ilk sitenizi ekleyin.

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

Giriş yaptıktan sonra yenilenen analitik paneline `/dashboard` veya `/analytics` adresinden erişin. Panelde şu bölümler bulunur:

- `/analytics/overview` — Genel Bakış
- `/analytics/statistics` — İstatistikler
- `/analytics/traffic` — Trafik Analizi
- `/analytics/visitors` — Ziyaretçiler
- `/analytics/pages` — Sayfalar
- `/analytics/sources` — Kaynaklar
- `/analytics/settings` — Ayarlar ve izleme kodu
- `/analytics/notifications` — E-posta bildirim tercihleri ve SMTP ayarları
- `/analytics/system` — giriş yapmış kullanıcılar için veritabanı durumu ve günlükler; tablo optimizasyonu yalnızca sistem yöneticilerine açık

Panel verileri seçili site için MySQL'deki `page_views`, `visitors`, `sessions` ve `events` tablolarından okunur. Bildirim tercihleri ve SMTP sunucu bilgileri kullanıcı bazında `notification_settings` tablosunda saklanır. SMTP şifresi AES-256-GCM ile şifrelenir; anahtar web kök dizininin dışındaki üst dizinde `.web-analytics-smtp.key` adıyla oluşturulur. Uygulamanın bu dosyayı oluşturabilmesi için üst dizinin yazılabilir olması gerekir; sunucu taşınırken anahtar da güvenli biçimde taşınmalıdır. Günlük/haftalık/aylık raporlar, saatlik trafik anomalileri ve izleme hatası bildirimleri için aşağıdaki CLI zamanlayıcısını kurun. Tailwind CSS ve Font Awesome CDN kullanıldığı için panel tasarımının yüklenmesi sırasında internet erişimi gerekir.

Ayarlar sayfasındaki Profil sekmesinden kullanıcı adı ve e-posta güncellenebilir. Güvenlik sekmesinde şifre değişimi, mevcut şifrenin doğrulanmasını, en az 8 karakter ve onay alanını gerektirir. Her iki işlem de oturum CSRF belirteciyle korunur.

Analitik sayfalarının üst çubuğundan son 7, 30 veya 90 günü ya da başlangıç/bitiş tarihlerini belirleyerek özel bir aralığı seçebilirsiniz. Özetler, grafikler, ziyaretçi ve kaynak raporları seçilen aralığa göre yenilenir; canlı kartlar, son 24 saatlik saat grafiği ve haftalık aktivite haritası gerçek zamanlı kapsamlarını korur.

Her site için saat dilimi `/websites/{id}/edit` sayfasından seçilir. Tarih sınırları ve günlük/saatlik grafik grupları bu yerel saat dilimini kullanır; geçmiş verilerin zaman damgaları değiştirilmez. Adlandırılmış saat dilimlerinin MySQL'de çalışması için MySQL timezone tablolarının yüklü olması gerekir. Linux'ta genellikle `mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root -p mysql` komutuyla yüklenebilir; Windows'ta MySQL dağıtımınıza uygun timezone tablolarını `mysql` sistem veritabanına yükleyin.

İstatistikler sayfasındaki anlık aktif ziyaretçi sayısı `/analytics/realtime?website_id=SITE_ID` uç noktasından 30 saniyede bir yenilenir. Uç nokta yalnızca kullanıcının sahibi olduğu veya ekip erişimi bulunan siteler için sayı döndürür.

Genel Bakış kartları seçili aralığı, hemen önceki eş uzunluktaki dönemle yüzde olarak karşılaştırır. Sayfalar, ziyaretçiler ve trafik analizi tablolarındaki indirme düğmesi seçili site/tarih aralığı için sunucu tarafında Excel uyumlu UTF-8 CSV dosyası oluşturur.

### Uygulama güvenlik/günlük işleri migration'ı

Mevcut veritabanına `migrations/2026-11-08-high-priority-fixes.sql` dosyasını **bir kez** uygulayın. Migration, pageview/session alanları ve indeksleri, login deneme sayacı, Geo-IP cache/kuyruğu tablolarını oluşturur ve mevcut pageview kayıtlarından günlük istatistikleri doldurur. Migration öncesi yedek alın; `sessions` tablosunda aynı website/session anahtarına ait mükerrer satır varsa unique indeks eklenmeden önce bunları elle uzlaştırın. Yeni kurulumlarda aynı şema `db.sql` içindedir.

Çok kiracılı site sahipliği için `migrations/2026-11-08-multi-tenant-websites.sql` dosyasını ayrıca **bir kez** uygulayın. Migration `websites.user_id` alanını ekler ve kullanıcı-sahiplik ilişkisini kurar. Veritabanında yalnızca bir kullanıcı varsa eski siteler güvenle ona atanır. Birden fazla kullanıcı varsa sahipliği bilinmeyen eski siteler `user_id = NULL` kalır ve panelde hiçbir kullanıcıya gösterilmez; veriyi görmeden tahminle atama yapılmaz. Yedek aldıktan sonra eski siteleri gerçek sahiplerine atayın; örnek:

```sql
UPDATE websites SET user_id = 2 WHERE id IN (10, 11);
UPDATE websites SET user_id = 3 WHERE id IN (12);
```

Site listesi, panel seçicisi, analiz, düzenleme ve silme işlemleri oturum açmış kullanıcının `user_id` değeriyle sınırlandırılır. Yeni eklenen siteler otomatik olarak oturum sahibine bağlanır.

Site saat dilimi ve ekip erişimi için önce yukarıdaki çok kiracılı sahiplik migration'ını, ardından `migrations/2026-11-08-site-timezones-and-team-access.sql` dosyasını **bir kez** uygulayın. Site sahibi, mevcut kullanıcıları salt okunur veya düzenleme yetkisiyle davet edebilir. Davet bağlantısı 7 gün geçerlidir ve sadece davet edilen e-posta adresiyle giriş yapılmış hesap tarafından kabul edilir. Ekipteki düzenleme yetkisi site bilgilerini güncelleyebilir; API anahtarı yenileme, site silme ve ekip yönetimi yalnızca sahibindedir. API anahtarı yenilendiğinde eski takip kodu hemen geçersiz olacağından sitelerdeki snippet'leri de güncelleyin. Uygulama canonical adresi proxy veya ters proxy arkasında doğru algılanmıyorsa davet URL'leri için `APP_URL` ortam değişkenini (örn. `https://analytics.example.com`) tanımlayın.

Geo-IP lookup artık pageview isteğinde üçüncü taraf servisi beklemez. Worker hem bekleyen kuyruk işlerini işler hem de ülke bilgisi eksik geçmiş pageview IP'lerini bulup kuyruğa ekler. Her 5 dakikada bir çalıştırın:

```text
php /path/to/web-analytics/bin/process-geo-lookups.php 50
php /path/to/web-analytics/bin/process-notifications.php 50
```

Linux cron için iki satır ekleyebilirsiniz:

```cron
*/5 * * * * /usr/bin/php /path/to/web-analytics/bin/process-geo-lookups.php 50
*/5 * * * * /usr/bin/php /path/to/web-analytics/bin/process-notifications.php 50
```

Windows'ta PowerShell'i açıp görevleri geçerli kullanıcı için kurun:

```powershell
.\bin\install-windows-scheduled-tasks.ps1
```

Taşınabilir PHP otomatik bulunamazsa `-PhpPath "D:\path\to\php.exe"` verin. Kurulum, oturum açıkken her 5 dakikada çalışan Geo-IP ve bildirim görevlerini oluşturur; kaldırmak için `.\bin\install-windows-scheduled-tasks.ps1 -Uninstall` çalıştırın. Linux cron için yukarıdaki iki satırı kullanın. SMTP bildirimleri `notification_deliveries` tablosunda kuyruğa alınır; gönderim hataları beş denemeye kadar gecikmeli yeniden denenir. Eski kayıtların Geo-IP sorguları mevcut pageview IP adreslerini Geo-IP servislerine gönderir.

Yeni kurulumlarda bildirim kuyruğu `db.sql` içindedir. Var olan kurulumlarda `migrations/2026-11-08-notification-deliveries.sql` dosyasını bir kez uygulayın.

Veritabanı bakım ekranı, günlük saklama sayıları, tablo boyutları ve sorgular için gereken indeksleri gösterir. Mevcut veritabanına `migrations/2026-11-08-system-maintenance.sql` dosyasını uygulayın; bu migration `users.is_admin`, `system_logs` ve eksik sorgu indekslerini idempotent biçimde ekler. Yönetici yetkisi varsayılan olarak kapalıdır; yedek aldıktan sonra güvendiğiniz kullanıcıyı açıkça etkinleştirin:

```sql
UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com';
```

12 aydan eski pageview, event, session, günlük özet ve API kayıtları; 30 günden eski sistem logları ve `APP_LOG_DIRECTORY` içindeki `app-YYYY-MM-DD.log` dosyaları bakım worker'ı ile temizlenir. Önce güvenli önizleme alın; worker varsayılan olarak yalnızca dry-run yapar:

```text
php bin/run-maintenance.php
php bin/run-maintenance.php --execute --batch-size=500
```

Windows görev kurulum betiği temizliği her gün 03:15'te zamanlar. Linux cron için `15 3 * * * /usr/bin/php /path/to/web-analytics/bin/run-maintenance.php --execute --batch-size=500` satırını ekleyin. Her tabloda tek çalıştırmada en fazla belirtilen batch silinir; worker tekrarlandıkça temizleme sürer. Uygulama günlükleri varsayılan olarak web kök dizininin yanındaki `<proje-dizini-adı>-logs` klasörüne yazılır; gerekirse `APP_LOG_DIRECTORY` ile yalnızca bu uygulamaya ayrılmış bir klasör belirtin. PHP, web sunucusu ve MySQL'in kendi logları sunucu/işletim sistemi log rotasyonuyla ayrıca yönetilmelidir.

Sistem ekranındaki "Optimize et" işlemi yalnızca izin verilen MyISAM analitik tablolarında `OPTIMIZE TABLE` çalıştırır. MyISAM tablo işlem sırasında kilitlenebileceğinden düşük trafikte ve tablo başına ayrı çalıştırın.

`tracker.js`, ilk yüklemenin yanı sıra `history.pushState`, `history.replaceState`, `popstate` ve `hashchange` ile gerçekleşen SPA sayfa geçişlerini de kaydeder. Aynı URL'ye yapılan geçişler tekrar görüntüleme olarak sayılmaz.

Tracker, User-Agent içinde yaygın bot/crawler ve headless tarayıcı imzaları bulunan istekleri kaydetmez. Bu sezgisel filtre tüm otomasyonları tanımayı garanti etmez ve User-Agent değerinin değiştirilebileceği dikkate alınmalıdır.

Proxy arkasında çalışıyorsanız `TRUSTED_PROXIES` ortam değişkenine yalnızca güvendiğiniz proxy IP adreslerini virgülle ayırarak tanımlayın. Login denemeleri IP başına 15 dakikada 20, IP/e-posta çifti başına 5 denemeyle sınırlıdır. Hata ayrıntıları için `APP_ENV=development` ayarlanmadıkça tarayıcıya gösterilmez.

### 🔐 İki Faktörlü Doğrulama

Mevcut veritabanlarında `migrations/2026-11-08-user-two-factor-auth.sql` migration dosyasını uygulamadan önce veritabanınızı yedekleyin ve SQL dosyasını uygulama veritabanında çalıştırın. Yeni kurulumlarda aynı kolonlar `db.sql` içinde tanımlıdır. Migration tekrar çalıştırılabilir; var olan kullanıcıların 2FA'sı varsayılan olarak kapalı kalır.

Hesap Ayarları > Güvenlik bölümünden kurulumu başlatın ve authenticator uygulamanızla ekranda gösterilen QR kodunu tarayın. QR kodu taranamıyorsa kurulum anahtarını elle girebilirsiniz. Ardından doğrulama kodunu girip kurulumu tamamlayın. QR kodu yerel JavaScript ile üretilir; TOTP anahtarı harici bir servise gönderilmez. Etkinleştirme sonrasında gösterilen kurtarma kodlarını güvenli bir yerde saklayın; her kod yalnızca bir kez kullanılabilir ve sonradan tekrar gösterilmez.

İlk kurulumda `.web-analytics-2fa.key` anahtarı PHP tarafından uygulamanın web kökü dışındaki üst dizininde oluşturulur. Bu dosyayı web üzerinden erişilemeyen, yalnızca uygulamanın okuyabildiği izinlerle saklayın ve şifreli veritabanı yedekleriyle birlikte güvenli olarak yedekleyin. Anahtar kaybolursa mevcut kayıtlı TOTP sırları çözülemez; anahtarı değiştirmek mevcut 2FA kurulumlarını kullanılamaz hale getirir.

### 🔒 Güvenlik

- Tüm takip istekleri için API anahtarı doğrulaması
- Kötüye kullanımı önlemek için rate limiting
- Tracking API için kayıtlı site domainiyle eşleşen Origin kontrolü ve kısıtlı CORS yanıtı
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
