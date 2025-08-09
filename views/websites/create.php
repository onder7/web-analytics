
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Yeni Site Ekle</h5>
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
                
                <form method="POST" action="/websites/create">
                    <div class="mb-3">
                        <label for="domain" class="form-label">Domain</label>
                        <input type="text" class="form-control" id="domain" name="domain" 
                               placeholder="example.com" value="<?= htmlspecialchars($domain ?? '') ?>" required>
                        <div class="form-text">Protokol olmadan domain adını girin (örn: example.com)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Site Adı</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Site adını girin" value="<?= htmlspecialchars($name ?? '') ?>" required>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/websites" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
