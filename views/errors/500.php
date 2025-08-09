<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunucu Hatası - Network Analytics</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div class="card border-0 shadow">
                        <div class="card-body p-5">
                            <div class="text-danger mb-4">
                                <i class="fas fa-exclamation-triangle fa-4x"></i>
                            </div>
                            <h1 class="h3 mb-3">Sunucu Hatası</h1>
                            <p class="text-muted mb-4">
                                <?= htmlspecialchars($message ?? 'Beklenmeyen bir hata oluştu.') ?>
                            </p>
                            
                            <?php if (isset($error) && $error): ?>
                                <div class="alert alert-warning text-start">
                                    <small><strong>Hata Detayı:</strong> <?= htmlspecialchars($error) ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="/dashboard" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Ana Sayfa
                                </a>
                                <button onclick="history.back()" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Geri Dön
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>