<?php
$isLoggedIn = isset($_SESSION['user_id']);
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isAuthPage = in_array($requestPath, ['/login', '/register'], true);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Web Analytics', ENT_QUOTES, 'UTF-8') ?></title>
    <?php if (!empty($analyticsPanel) || $isAuthPage): ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <script>window.tailwind = window.tailwind || {}; window.tailwind.config = { theme: { extend: { colors: { 'sidebar-900': '#0b1220' } } } };</script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/assets/css/analytics-panel.css">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
    
    <!-- Custom CSS -->
    <style>
        .sidebar {
            background: linear-gradient(180deg, #111c31 0%, #080e1a 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
            border-right: 1px solid #25334b;
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
            color: #cbd5e1;
            text-decoration: none;
            margin: 3px 10px;
            padding: 12px 14px;
            display: block;
            border-radius: 10px;
            transition: all 0.2s;
        }
        
        .sidebar-nav a:hover {
            background: #26364f;
            color: #fff;
        }

        .sidebar-nav a.active {
            background: linear-gradient(105deg, #2563eb 0%, #0891b2 100%);
            color: #fff;
            box-shadow: 0 8px 18px rgba(8, 47, 73, .35);
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
    
    <?php if (empty($analyticsPanel) && !empty($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '', ENT_QUOTES, 'UTF-8') ?> <?= !empty($analyticsPanel) || $isAuthPage ? 'analytics-panel-body' : '' ?> <?= $isAuthPage ? 'auth-page' : '' ?>">

<?php if (!empty($analyticsPanel)): ?>
    <div id="analytics-panel" class="flex min-h-screen bg-slate-50 text-slate-800">
        <?php require __DIR__ . '/analytics-panel/partials/sidebar.php'; ?>
        <div class="flex min-w-0 flex-1 flex-col">
            <?php require __DIR__ . '/analytics-panel/partials/topbar.php'; ?>
            <main class="flex-1 px-4 py-6 lg:px-8 lg:py-7">
                <?= $content ?>
            </main>
        </div>
    </div>
<?php elseif ($isAuthPage && !$isLoggedIn): ?>
    <main class="auth-shell grid min-h-screen lg:grid-cols-[minmax(0,1.05fr)_minmax(460px,.95fr)]">
        <aside class="relative hidden overflow-hidden bg-slate-950 px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
            <div class="absolute -left-36 -top-32 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute -bottom-36 -right-20 h-96 w-96 rounded-full bg-cyan-500/15 blur-3xl"></div>
            <div class="relative">
                <a href="/login" class="inline-flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 shadow-lg shadow-blue-500/25"><i class="fa-solid fa-arrow-trend-up text-lg"></i></span>
                    <span><span class="block text-base font-bold tracking-wide">WebStats</span><span class="block text-xs text-slate-400">Analytics Panel</span></span>
                </a>
            </div>
            <div class="relative max-w-xl py-12">
                <span class="inline-flex items-center gap-2 rounded-full border border-blue-400/20 bg-blue-400/10 px-3 py-1.5 text-xs font-medium text-blue-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Verileriniz, kontrolünüzde
                </span>
                <h1 class="mt-7 text-4xl font-bold leading-tight tracking-tight xl:text-5xl">Web sitenizin performansını <span class="bg-gradient-to-r from-blue-300 to-cyan-300 bg-clip-text text-transparent">anlayın.</span></h1>
                <p class="mt-5 max-w-lg text-base leading-7 text-slate-400">Ziyaretçi hareketlerini takip edin, trafik kaynaklarını keşfedin ve kararlarınızı gerçek verilere dayandırın.</p>
                <div class="mt-10 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur"><i class="fa-solid fa-chart-line text-blue-300"></i><p class="mt-3 text-sm font-semibold">Canlı istatistikler</p><p class="mt-1 text-xs leading-5 text-slate-400">Trafik ve sayfa performansınızı izleyin.</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur"><i class="fa-solid fa-shield-halved text-emerald-300"></i><p class="mt-3 text-sm font-semibold">Kendi verileriniz</p><p class="mt-1 text-xs leading-5 text-slate-400">Analitik altyapınız kendi kontrolünüzde.</p></div>
                </div>
            </div>
            <p class="relative text-xs text-slate-500">© <?= date('Y') ?> WebStats Analytics</p>
        </aside>
        <section class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-8 sm:px-8">
            <div class="w-full max-w-md">
                <a href="/login" class="mb-8 inline-flex items-center gap-2 lg:hidden">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 text-white shadow-md shadow-blue-500/20"><i class="fa-solid fa-arrow-trend-up"></i></span>
                    <span><span class="block text-sm font-bold text-slate-800">WebStats</span><span class="block text-[11px] text-slate-400">Analytics Panel</span></span>
                </a>
                <?= $content ?>
                <p class="mt-8 text-center text-xs text-slate-400">Güvenli analitik paneli · © <?= date('Y') ?> WebStats</p>
            </div>
        </section>
    </main>
    
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

<?php if (!empty($analyticsPanel)): ?>
    <script src="/assets/js/analytics-panel.js"></script>
<?php elseif ($isAuthPage): ?>
    <script src="/assets/js/auth.js"></script>
<?php else: ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php endif; ?>

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