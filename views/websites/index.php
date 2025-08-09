
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-globe"></i> Web Siteleri</h1>
    <a href="/websites/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Site Ekle
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($websites)): ?>
            <div class="text-center py-5">
                <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                <h4>Henüz site eklenmemiş</h4>
                <p class="text-muted">İlk sitenizi ekleyerek başlayın.</p>
                <a href="/websites/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Site Ekle
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Site Adı</th>
                            <th>Domain</th>
                            <th>Son 30 Gün</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($websites as $website): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($website['name']) ?></strong>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($website['domain']) ?></code>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <small class="text-muted">Görüntüleme:</small><br>
                                        <strong><?= number_format($website['page_views']) ?></strong>
                                    </div>
                                    <div>
                                        <small class="text-muted">Ziyaretçi:</small><br>
                                        <strong><?= number_format($website['unique_visitors']) ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/websites/<?= $website['id'] ?>/analytics" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-chart-bar"></i> İstatistikler
                                    </a>
                                    <a href="/websites/<?= $website['id'] ?>/edit" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>
                                    <a href="/websites/<?= $website['id'] ?>/delete" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Bu siteyi silmek istediğinizden emin misiniz?')">
                                        <i class="fas fa-trash"></i> Sil
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
