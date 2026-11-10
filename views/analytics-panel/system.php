<?php
require_once __DIR__ . '/helpers.php';
$bytesToMb = function($bytes) {
    return number_format(((float) $bytes) / 1048576, 2, ',', '.') . ' MB';
};
$totalData = 0;
$totalIndex = 0;
foreach ($tables as $tableInfo) {
    $totalData += (int) $tableInfo['data_length'];
    $totalIndex += (int) $tableInfo['index_length'];
}
?>
<?php if (!empty($flash)): ?>
    <div class="mb-5 rounded-xl border <?= ($flash['type'] ?? '') === 'error' ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' ?> px-4 py-3 text-sm" role="status"><?= htmlspecialchars($flash['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <?php foreach ([
        ['Veritabanı', $database['name'] ?: 'Bilinmiyor', 'database'],
        ['Sunucu sürümü', $database['version'] ?? 'Bilinmiyor', 'server'],
        ['Tablo sayısı', count($tables), 'table'],
        ['Veri / indeks', $bytesToMb($totalData) . ' / ' . $bytesToMb($totalIndex), 'hard-drive']
    ] as $card): ?>
        <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i data-lucide="<?= htmlspecialchars($card[2], ENT_QUOTES, 'UTF-8') ?>" class="h-5 w-5"></i></span><div class="min-w-0"><p class="text-xs font-medium text-slate-500"><?= htmlspecialchars((string) $card[0], ENT_QUOTES, 'UTF-8') ?></p><p class="truncate text-sm font-bold text-slate-800"><?= htmlspecialchars((string) $card[1], ENT_QUOTES, 'UTF-8') ?></p></div></div>
        </article>
    <?php endforeach; ?>
</section>

<section class="mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div><h3 class="font-bold text-slate-800">Saklama ve temizlik</h3><p class="mt-1 text-sm text-slate-500">Analitik kayıtları 12 ay, uygulama ve sistem kayıtları 30 gün tutulur.</p></div>
        <code class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs text-slate-600">php bin/run-maintenance.php --dry-run</code>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[560px] text-left text-sm">
            <thead class="text-xs uppercase tracking-wide text-slate-400"><tr><th class="pb-3">Veri</th><th class="pb-3">Saklama</th><th class="pb-3 text-right">Süresi geçen</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($expiredCounts as $item): ?>
                    <tr><td class="py-3 font-medium text-slate-700"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></td><td class="py-3 text-slate-500"><?= htmlspecialchars($item['retention'], ENT_QUOTES, 'UTF-8') ?></td><td class="py-3 text-right font-semibold text-slate-700"><?= number_format((int) $item['count'], 0, ',', '.') ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-800">Temizleme işlemi web isteğiyle değil, zamanlanmış CLI göreviyle çalışır. Her tablo için tek çalıştırmada en fazla 500 kayıt silinir; sınırı değiştirmek için <code>--batch-size=...</code> kullanın.</p>
</section>

<section class="mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
    <div class="mb-4"><h3 class="font-bold text-slate-800">Tablo ve indeks durumu</h3><p class="mt-1 text-sm text-slate-500">Boyutlar yaklaşık sunucu değerleridir; MyISAM optimizasyonu tablo kilidi oluşturabilir.</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[680px] text-left text-sm">
            <thead class="text-xs uppercase tracking-wide text-slate-400"><tr><th class="pb-3">Tablo</th><th class="pb-3">Motor</th><th class="pb-3 text-right">Satır (yaklaşık)</th><th class="pb-3 text-right">Veri / indeks</th><th class="pb-3 text-right">İşlem</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($tables as $tableInfo): ?>
                    <tr>
                        <td class="py-3 font-medium text-slate-700"><?= htmlspecialchars($tableInfo['table_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-3 text-slate-500"><?= htmlspecialchars($tableInfo['engine'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="py-3 text-right text-slate-600"><?= number_format((int) $tableInfo['table_rows'], 0, ',', '.') ?></td>
                        <td class="py-3 text-right text-slate-600"><?= $bytesToMb($tableInfo['data_length']) ?> / <?= $bytesToMb($tableInfo['index_length']) ?></td>
                        <td class="py-3 text-right">
                            <?php if (!empty($isSystemAdmin) && strtoupper((string) $tableInfo['engine']) === 'MYISAM' && in_array($tableInfo['table_name'], ['api_requests', 'daily_stats', 'events', 'page_views', 'sessions', 'visitors'], true)): ?>
                                <form method="post" action="/analytics/maintenance/optimize" onsubmit="return confirm('<?= htmlspecialchars($tableInfo['table_name'], ENT_QUOTES, 'UTF-8') ?> tablosu optimize edilecek. MyISAM tablo geçici olarak kilitlenebilir. Devam edilsin mi?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="table" value="<?= htmlspecialchars($tableInfo['table_name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50">Optimize et</button>
                                </form>
                            <?php elseif (empty($isSystemAdmin) && strtoupper((string) $tableInfo['engine']) === 'MYISAM' && in_array($tableInfo['table_name'], ['api_requests', 'daily_stats', 'events', 'page_views', 'sessions', 'visitors'], true)): ?>
                                <span class="text-xs text-slate-400"><?= panel_h(__('system.admin_only_operation')) ?></span>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-5 border-t border-slate-100 pt-4">
        <h4 class="text-sm font-bold text-slate-700">Önerilen sorgu indeksleri</h4>
        <ul class="mt-2 grid gap-2 sm:grid-cols-2">
            <?php foreach ($requiredIndexes as $index): ?>
                <li class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2 text-xs">
                    <span class="text-slate-600"><code><?= htmlspecialchars($index['table'], ENT_QUOTES, 'UTF-8') ?></code>: <?= htmlspecialchars($index['columns'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="font-semibold <?= $index['available'] ? 'text-emerald-600' : 'text-amber-600' ?>"><?= $index['available'] ? 'Var' : 'Migration gerekli' ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div><h3 class="font-bold text-slate-800">Sistem günlükleri</h3><p class="mt-1 text-sm text-slate-500">Son 30 gün · Uygulama günlükleri: <code><?= htmlspecialchars($logDirectory, ENT_QUOTES, 'UTF-8') ?></code></p><p class="mt-1 text-xs text-slate-400">PHP hata günlüğü: <?= $phpLogName ? htmlspecialchars($phpLogName, ENT_QUOTES, 'UTF-8') . ' (son 100 satır)' : 'Yapılandırılmamış veya okunamıyor' ?></p></div>
        <form method="get" action="/analytics/system">
            <label class="sr-only" for="log-severity">Günlük seviyesi</label>
            <select id="log-severity" name="severity" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600">
                <option value="" <?= $logFilter === '' ? 'selected' : '' ?>>Tüm seviyeler</option>
                <?php foreach (['error' => 'Hata', 'warning' => 'Uyarı', 'info' => 'Bilgi'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $logFilter === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <?php if (!$logTableAvailable): ?>
        <p class="mb-4 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">Veritabanı günlük tablosu henüz oluşturulmamış. Migration uygulandıktan sonra günlükler hem tabloda hem dosyada tutulur.</p>
    <?php endif; ?>
    <?php if (empty($logs)): ?>
        <div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-400">Gösterilecek sistem kaydı bulunamadı.</div>
    <?php else: ?>
        <div class="max-h-[34rem] space-y-2 overflow-y-auto">
            <?php foreach ($logs as $log): ?>
                <?php $severity = $log['severity'] ?? 'info'; ?>
                <article class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase <?= $severity === 'error' ? 'bg-rose-100 text-rose-700' : ($severity === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') ?>"><?= htmlspecialchars($severity, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="text-xs font-semibold text-slate-700"><?= htmlspecialchars($log['source'] ?? 'uygulama', ENT_QUOTES, 'UTF-8') ?></span>
                        <time class="ml-auto text-[11px] text-slate-400"><?= htmlspecialchars($log['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?> UTC</time>
                    </div>
                    <p class="mt-2 break-words font-mono text-xs leading-5 text-slate-600"><?= htmlspecialchars($log['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
