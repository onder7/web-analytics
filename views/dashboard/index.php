<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-home"></i> Dashboard</h1>
    <div class="text-muted">
        Hoş geldin, <?= htmlspecialchars($_SESSION['username']) ?>!
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= number_format($totalWebsites) ?></h4>
                        <p class="mb-0">Toplam Site</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-globe fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= number_format($totalVisitors) ?></h4>
                        <p class="mb-0">Toplam Ziyaretçi (30 gün)</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= number_format($totalPageViews) ?></h4>
                        <p class="mb-0">Toplam Sayfa Görüntüleme (30 gün)</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-eye fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Son 7 Günlük Aktivite</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyStatsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-globe"></i> Site Performansı</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Site</th>
                                <th>Görüntüleme</th>
                                <th>Ziyaretçi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($websiteStats, 0, 5) as $site): ?>
                            <tr>
                                <td>
                                    <a href="/websites/<?= $site['id'] ?>/analytics" class="text-decoration-none">
                                        <?= htmlspecialchars($site['name']) ?>
                                    </a>
                                </td>
                                <td><?= number_format($site['page_views']) ?></td>
                                <td><?= number_format($site['unique_visitors']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (count($websiteStats) > 5): ?>
                <div class="card-footer text-center">
                    <a href="/websites" class="text-decoration-none">Tümünü Gör</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Daily stats chart
const dailyStatsData = <?= json_encode($dailyStats) ?>;
const ctx = document.getElementById('dailyStatsChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: dailyStatsData.map(item => item.date).reverse(),
        datasets: [
            {
                label: 'Sayfa Görüntüleme',
                data: dailyStatsData.map(item => item.page_views).reverse(),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            },
            {
                label: 'Benzersiz Ziyaretçi',
                data: dailyStatsData.map(item => item.unique_visitors).reverse(),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
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
                beginAtZero: true
            }
        }
    }
});
</script>