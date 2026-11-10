<?php
require_once __DIR__ . '/../analytics-panel/helpers.php';
$websiteCount = count($websites);
$totalViews = array_sum(array_map(function($item) {
    return (int) ($item['page_views'] ?? 0);
}, $websites));
$totalVisitors = array_sum(array_map(function($item) {
    return (int) ($item['unique_visitors'] ?? 0);
}, $websites));
$analyticsScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$analyticsOrigin = $analyticsScheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
?>
<div class="mx-auto w-full max-w-7xl">
    <section class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[.16em] text-blue-600"><?= panel_h(__('websites.management')) ?></p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl"><?= panel_h(__('websites.page_title')) ?></h1>
            <p class="mt-1 text-sm text-slate-500"><?= panel_h(__('websites.page_subtitle')) ?></p>
        </div>
        <a href="/websites/create" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md">
            <i data-lucide="plus" class="h-4 w-4"></i> <?= panel_h(__('websites.add_new')) ?>
        </a>
    </section>

    <?php if (isset($_GET['success'])): ?>
        <div role="status" class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i data-lucide="circle-check" class="h-5 w-5 shrink-0"></i><span><?= panel_h($_GET['success']) ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div role="alert" class="mb-5 flex items-center gap-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i data-lucide="triangle-alert" class="h-5 w-5 shrink-0"></i><span><?= panel_h($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <?php foreach ([
            [__('websites.tracked_sites'), $websiteCount, 'globe-2', 'blue'],
            [__('websites.page_views'), $totalViews, 'eye', 'emerald'],
            [__('websites.unique_visitors'), $totalVisitors, 'users', 'amber']
        ] as $index => $stat): ?>
            <?php $color = ['blue' => 'bg-blue-50 text-blue-600', 'emerald' => 'bg-emerald-50 text-emerald-600', 'amber' => 'bg-amber-50 text-amber-600'][$stat[3]]; ?>
            <article class="animate-slide-up flex items-center gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" style="animation-delay:<?= $index * 80 ?>ms">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl <?= $color ?>"><i data-lucide="<?= panel_h($stat[2]) ?>" class="h-5 w-5"></i></div>
                <div class="min-w-0"><p class="text-2xl font-bold text-slate-800"><?= panel_number($stat[1]) ?></p><p class="truncate text-sm text-slate-500"><?= panel_h($stat[0]) ?></p></div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-800"><?= panel_h(__('websites.all_sites')) ?></h2>
                <p class="mt-0.5 text-xs text-slate-500"><?= panel_number($websiteCount) ?> <?= panel_h(__('websites.sites_count')) ?></p>
            </div>
            <?php if (!empty($websites)): ?>
                <label class="relative">
                    <span class="sr-only"><?= panel_h(__('websites.search_label')) ?></span>
                    <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input type="search" data-website-search placeholder="<?= panel_h(__('websites.search_placeholder')) ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 sm:w-64">
                </label>
            <?php endif; ?>
        </div>

        <?php if (empty($websites)): ?>
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 px-5 py-14 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><i data-lucide="globe-2" class="h-7 w-7"></i></div>
                <h3 class="mt-4 text-base font-semibold text-slate-800"><?= panel_h(__('websites.empty_title')) ?></h3>
                <p class="mt-1 text-sm text-slate-500"><?= panel_h(__('websites.empty_subtitle')) ?></p>
                <a href="/websites/create" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"><i data-lucide="plus" class="h-4 w-4"></i> <?= panel_h(__('websites.add_site')) ?></a>
            </div>
        <?php else: ?>
            <div id="website-grid" class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <?php foreach ($websites as $website): ?>
                    <?php
                    $websiteName = (string) ($website['name'] ?? '');
                    $initial = strtoupper(substr($websiteName !== '' ? $websiteName : ($website['domain'] ?? 'W'), 0, 1));
                    $integrationScriptUrl = json_encode($analyticsOrigin . '/assets/js/tracker.js', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
                    $integrationApiUrl = json_encode($analyticsOrigin . '/api', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
                    $integrationApiKey = json_encode((string) ($website['api_key'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
                    $integrationSnippet = !empty($website['api_key'])
                        ? "<script>\n(function () {\n    var script = document.createElement('script');\n    script.src = {$integrationScriptUrl};\n    script.setAttribute('data-api-key', {$integrationApiKey});\n    script.setAttribute('data-api-url', {$integrationApiUrl});\n    script.async = true;\n    document.head.appendChild(script);\n})();\n</script>"
                        : '';
                    ?>
                    <article class="website-card group rounded-2xl border border-slate-100 bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-blue-100 hover:shadow-md sm:p-5"
                             data-search="<?= panel_h($websiteName . ' ' . ($website['domain'] ?? '')) ?>">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-base font-bold text-white shadow-sm"><?= panel_h($initial) ?></div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-base font-semibold text-slate-800"><?= panel_h($websiteName) ?></h3>
                                        <a href="https://<?= panel_h($website['domain']) ?>" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex max-w-full items-center gap-1.5 truncate text-sm text-slate-500 transition hover:text-blue-600">
                                            <i data-lucide="globe-2" class="h-3.5 w-3.5 shrink-0"></i><span class="truncate"><?= panel_h($website['domain']) ?></span><i data-lucide="arrow-up-right" class="h-3 w-3 shrink-0 opacity-60"></i>
                                        </a>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>İzleme hazır
                                    </span>
                                    <?php if (($website['access_role'] ?? '') !== 'owner'): ?><span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700"><?= $website['access_role'] === 'write' ? 'Ekip · Düzenleme' : 'Ekip · Salt okunur' ?></span><?php endif; ?>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3 rounded-xl bg-slate-50/80 p-3">
                                    <div>
                                        <p class="text-[11px] font-medium text-slate-400">Görüntülenme</p>
                                        <p class="mt-0.5 text-lg font-bold text-slate-800"><?= panel_number($website['page_views'] ?? 0) ?></p>
                                    </div>
                                    <div class="border-l border-slate-200 pl-4">
                                        <p class="text-[11px] font-medium text-slate-400">Benzersiz ziyaretçi</p>
                                        <p class="mt-0.5 text-lg font-bold text-slate-800"><?= panel_number($website['unique_visitors'] ?? 0) ?></p>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <?php if ($integrationSnippet !== ''): ?>
                                        <button type="button" data-copy-text="<?= panel_h($integrationSnippet) ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100" aria-label="<?= panel_h($websiteName) ?> izleme kodunu kopyala">
                                            <i data-lucide="copy" class="h-3.5 w-3.5"></i><span data-copy-label>İzleme Kodunu Kopyala</span>
                                        </button>
                                    <?php endif; ?>
                                    <a href="/analytics/overview?website_id=<?= (int) $website['id'] ?>" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                        <i data-lucide="chart-no-axes-combined" class="h-3.5 w-3.5"></i> İstatistikler
                                    </a>
                                    <a href="/websites/<?= (int) $website['id'] ?>/edit" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                        <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Düzenle
                                    </a>
                                    <?php if (($website['access_role'] ?? '') === 'owner'): ?>
                                        <form method="POST" action="/websites/<?= (int) $website['id'] ?>/delete" class="ml-auto" onsubmit="return confirm('<?= panel_h($websiteName) ?> sitesini silmek istediğinizden emin misiniz?')">
                                            <input type="hidden" name="csrf_token" value="<?= panel_h($websiteCsrf ?? '') ?>">
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Sil
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p id="website-empty-search" class="mt-5 hidden rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500"><?= panel_h(__('websites.no_search_results')) ?></p>
        <?php endif; ?>
    </section>
</div>
