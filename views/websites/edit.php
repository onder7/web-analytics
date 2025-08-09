
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Site Düzenle</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/websites/<?= $website['id'] ?>/edit">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="domain" class="form-label">Domain</label>
                                <input type="text" class="form-control" id="domain" name="domain" 
                                       value="<?= htmlspecialchars($website['domain']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Site Adı</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($website['name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">API Anahtarı</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           value="<?= htmlspecialchars($website['api_key']) ?>" readonly>
                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="copyToClipboard('<?= $website['api_key'] ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="form-text">Bu anahtarı sitenizde kullanın</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Oluşturulma Tarihi</label>
                                <input type="text" class="form-control" 
                                       value="<?= date('d.m.Y H:i', strtotime($website['created_at'])) ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/websites" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-code"></i> Entegrasyon Kodu</h5>
            </div>
            <div class="card-body">
                <p>Bu kodu sitenizin <code>&lt;/body&gt;</code> etiketi öncesine ekleyin:</p>
                <pre class="bg-light p-3"><code id="integrationCode">&lt;script&gt;
(function() {
    var script = document.createElement('script');
    script.src = '<?= $_SERVER['HTTP_HOST'] ?>/assets/js/tracker.js';
    script.setAttribute('data-api-key', '<?= $website['api_key'] ?>');
    script.setAttribute('data-api-url', '<?= $_SERVER['HTTP_HOST'] ?>/api');
    document.head.appendChild(script);
})();
&lt;/script&gt;</code></pre>
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="copyToClipboard(document.getElementById('integrationCode').textContent)">
                    <i class="fas fa-copy"></i> Kodu Kopyala
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Başarı mesajı göster
        const toast = document.createElement('div');
        toast.className = 'toast position-fixed top-0 end-0 m-3';
        toast.innerHTML = `
            <div class="toast-body bg-success text-white">
                <i class="fas fa-check"></i> Panoya kopyalandı!
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 2000);
    });
}
</script>