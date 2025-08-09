<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Web Analytics' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        .auth-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar {
            background: #2c3e50;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }
        
        .content-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .navbar-custom {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .sidebar-nav a {
            color: #bdc3c7;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: #34495e;
            color: #fff;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .content-wrapper {
                margin-left: 0;
            }
        }
    </style>
    
    <?php if (!empty($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">

<?php 
// Kullanıcı giriş yapmış mı kontrol et
$isLoggedIn = isset($_SESSION['user_id']);
$isAuthPage = strpos($_SERVER['REQUEST_URI'], '/login') !== false || 
              strpos($_SERVER['REQUEST_URI'], '/register') !== false;
?>

<?php if ($isAuthPage && !$isLoggedIn): ?>
    <!-- AUTH PAGES LAYOUT -->
    <div class="auth-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="auth-card p-4">
                        <div class="text-center mb-4">
                            <h2><i class="fas fa-chart-line text-primary"></i> Web Analytics</h2>
                        </div>
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <!-- DASHBOARD LAYOUT -->
    <?php if ($isLoggedIn): ?>
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="p-3 text-white">
                <h4><i class="fas fa-chart-line"></i> Analytics</h4>
                <hr class="text-light">
            </div>
            <nav class="sidebar-nav">
                <a href="/dashboard" class="<?= ($_SERVER['REQUEST_URI'] == '/dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="/websites" class="<?= (strpos($_SERVER['REQUEST_URI'], '/websites') === 0) ? 'active' : '' ?>">
                    <i class="fas fa-globe me-2"></i> Web Siteleri
                </a>
                <a href="/logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Çıkış
                </a>
            </nav>
        </div>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <?php if (!empty($breadcrumb)): ?>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="/dashboard"><i class="fas fa-home"></i> Ana Sayfa</a>
                                </li>
                                <?php foreach ($breadcrumb as $item): ?>
                                    <?php if (isset($item['url'])): ?>
                                        <li class="breadcrumb-item">
                                            <a href="<?= $item['url'] ?>">
                                                <?php if (isset($item['icon'])): ?>
                                                    <i class="<?= $item['icon'] ?> me-1"></i>
                                                <?php endif; ?>
                                                <?= $item['title'] ?>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item active">
                                            <?php if (isset($item['icon'])): ?>
                                                <i class="<?= $item['icon'] ?> me-1"></i>
                                            <?php endif; ?>
                                            <?= $item['title'] ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    <?php endif; ?>
                    
                    <div class="ms-auto">
                        <span class="navbar-text">
                            <i class="fas fa-user me-1"></i> 
                            <?= htmlspecialchars($_SESSION['username'] ?? 'Kullanıcı') ?>
                        </span>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <div class="container-fluid p-4">
                <?= $content ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Giriş yapılmamış kullanıcılar için basit layout -->
        <div class="container-fluid">
            <?= $content ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($additionalJS)): ?>
    <?php foreach ($additionalJS as $js): ?>
        <script src="<?= $js ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($inlineJS)): ?>
    <script>
        <?= $inlineJS ?>
    </script>
<?php endif; ?>

</body>
</html>