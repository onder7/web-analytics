

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1><i class="fas fa-chart-bar"></i> <?= htmlspecialchars($website['name']) ?></h1>
        <small class="text-muted"><?= htmlspecialchars($website['domain']) ?></small>
    </div>
    <a href="/websites" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-eye fa-2x mb-2"></i>
                <h4><?= number_format($totalPageViews) ?></h4>
                <p class="mb-0">Sayfa Görüntüleme</p>
                <small>Son 30 gün</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4><?= number_format($totalVisitors) ?></h4>
                <p class="mb-0">Benzersiz Ziyaretçi</p>
                <small>Son 30 gün</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h4><?= count($dailyStats) ?></h4>
                <p class="mb-0">Aktif Gün</p>
                <small>Son 30 gün</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-file-alt fa-2x mb-2"></i>
                <h4><?= count($popularPages) ?></h4>
                <p class="mb-0">Farklı Sayfa</p>
                <small>Son 30 gün</small>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Son 30 Günlük Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-fire"></i> Popüler Sayfalar</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($popularPages)): ?>
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-info-circle"></i> Henüz veri yok
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <?php foreach ($popularPages as $page): ?>
                                <tr>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($page['page_title'] ?: $page['page_url']) ?>">
                                            <strong><?= htmlspecialchars($page['page_title'] ?: 'Başlık Yok') ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($page['page_url']) ?></small>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary"><?= number_format($page['views']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-globe"></i> Tarayıcı İstatistikleri</h5>
            </div>
            <div class="card-body">
                <?php if (empty($browserStats)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> Henüz veri yok
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-8">
                            <?php foreach ($browserStats as $browser): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?= htmlspecialchars($browser['browser']) ?></span>
                                <span class="text-muted"><?= $browser['percentage'] ?>% (<?= number_format($browser['count']) ?>)</span>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar" style="width: <?= $browser['percentage'] ?>%"></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-4">
                            <canvas id="browserChart"></canvas>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-desktop"></i> İşletim Sistemi İstatistikleri</h5>
            </div>
            <div class="card-body">
                <?php if (empty($osStats)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> Henüz veri yok
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-8">
                            <?php foreach ($osStats as $os): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?= htmlspecialchars($os['os']) ?></span>
                                <span class="text-muted"><?= $os['percentage'] ?>% (<?= number_format($os['count']) ?>)</span>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?= $os['percentage'] ?>%"></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-4">
                            <canvas id="osChart"></canvas>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Trend chart
const trendData = <?= json_encode($dailyStats) ?>;
const trendCtx = document.getElementById('trendChart').getContext('2d');

new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: trendData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit' });
        }).reverse(),
        datasets: [
            {
                label: 'Sayfa Görüntüleme',
                data: trendData.map(item => item.page_views).reverse(),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Benzersiz Ziyaretçi',
                data: trendData.map(item => item.unique_visitors).reverse(),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Browser chart
<?php if (!empty($browserStats)): ?>
const browserData = <?= json_encode($browserStats) ?>;
const browserCtx = document.getElementById('browserChart').getContext('2d');

new Chart(browserCtx, {
    type: 'doughnut',
    data: {
        labels: browserData.map(item => item.browser),
        datasets: [{
            data: browserData.map(item => item.percentage),
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
<?php endif; ?>

// OS chart
<?php if (!empty($osStats)): ?>
const osData = <?= json_encode($osStats) ?>;
const osCtx = document.getElementById('osChart').getContext('2d');

new Chart(osCtx, {
    type: 'doughnut',
    data: {
        labels: osData.map(item => item.os),
        datasets: [{
            data: osData.map(item => item.percentage),
            backgroundColor: [
                '#4BC0C0', '#36A2EB', '#FFCE56', '#FF6384', 
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
<?php endif; ?>
</script>
