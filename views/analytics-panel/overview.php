<?php
require_once __DIR__ . '/helpers.php';
$summary        = $summary ?? [];
$sessionSummary = $sessionSummary ?? [];
$cards = [
    ['title' => __('overview.pageviews'),    'value' => $summary['pageviews'] ?? 0,    'icon' => 'eye',            'color' => 'blue',    'key' => 'pageviews'],
    ['title' => __('overview.visitors'),     'value' => $summary['visitors'] ?? 0,     'icon' => 'users',          'color' => 'emerald', 'key' => 'visitors'],
    ['title' => __('overview.active_days'),  'value' => $summary['active_days'] ?? 0,  'icon' => 'calendar-check', 'color' => 'amber',   'key' => 'active_days'],
    ['title' => __('overview.unique_pages'), 'value' => $summary['unique_pages'] ?? 0, 'icon' => 'file-text',      'color' => 'rose',    'key' => 'unique_pages'],
];
$cardColors = [
    'blue'    => 'bg-blue-50 text-blue-600',
    'emerald' => 'bg-emerald-50 text-emerald-600',
    'amber'   => 'bg-amber-50 text-amber-600',
    'rose'    => 'bg-rose-50 text-rose-600',
];
$trend = array_map(function($row) {
    return [
        'label'    => date('d.m', strtotime($row['day'])),
        'views'    => (int) $row['views'],
        'visitors' => (int) $row['visitors'],
    ];
}, $trend);
$pagesQuery = ['period' => $dateRange['period'] ?? '30d'];
if ($websiteId) {
    $pagesQuery['website_id'] = $websiteId;
}
if (($dateRange['period'] ?? '') === 'custom') {
    $pagesQuery['start_date'] = $dateRange['start_date'];
    $pagesQuery['end_date']   = $dateRange['end_date'];
}
$maxPopularViews = !empty($popularPages)
    ? max(array_map(fn($r) => (int) $r['views'], $popularPages))
    : 0;
?>
<?php if (!$website): ?>
    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-6 text-sm text-blue-800">
        <p class="font-semibold"><?= panel_h(__('overview.no_website')) ?></p>
        <p class="mt-1"><?= panel_h(__('overview.no_website_hint')) ?></p>
        <a href="/websites/create" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">
            <i data-lucide="plus" class="h-4 w-4"></i> <?= panel_h(__('overview.add_website')) ?>
        </a>
    </div>
<?php else: ?>
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($cards as $index => $card): ?>
            <?php
            $currentValue  = (int) ($summary[$card['key']] ?? 0);
            $previousValue = (int) ($comparison[$card['key']] ?? 0);
            $delta = $previousValue > 0 ? round(($currentValue - $previousValue) * 100 / $previousValue, 1) : null;
            ?>
            <article class="animate-slide-up rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" style="animation-delay:<?= $index * 80 ?>ms">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl <?= $cardColors[$card['color']] ?>">
                        <i data-lucide="<?= panel_h($card['icon']) ?>" class="h-5 w-5"></i>
                    </div>
                    <span class="rounded-full bg-slate-50 px-2.5 py-1 text-[10px] font-semibold text-slate-500"><?= panel_h($dateRange['label'] ?? __('topbar.period_30d')) ?></span>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-800"><?= panel_number($card['value']) ?></p>
                <p class="mt-0.5 text-sm text-slate-500"><?= panel_h($card['title']) ?></p>
                <p class="mt-2 text-xs font-semibold <?= $delta === null ? 'text-slate-400' : ($delta > 0 ? 'text-emerald-600' : ($delta < 0 ? 'text-rose-600' : 'text-slate-400')) ?>">
                    <?php if ($delta === null): ?>
                        <?= panel_h($currentValue > 0 ? __('overview.new_period') : __('overview.no_prev_data')) ?>
                    <?php else: ?>
                        <?= $delta > 0 ? '↑ +' : ($delta < 0 ? '↓ ' : '') ?><?= number_format(abs($delta), 1, ',', '.') ?><?= panel_h(__('overview.vs_prev')) ?>
                    <?php endif; ?>
                </p>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3 xl:gap-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:col-span-2 lg:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800"><?= panel_h($dateRange['label'] ?? __('topbar.period_30d')) ?> <?= panel_h(__('overview.trend_title')) ?></h2>
                    <p class="mt-0.5 text-sm text-slate-500"><?= panel_h(__('overview.trend_subtitle')) ?></p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-2 text-xs font-medium text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span><?= panel_h(__('overview.trend_views')) ?></span>
                    <span class="flex items-center gap-2 text-xs font-medium text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span><?= panel_h(__('overview.trend_visitors')) ?></span>
                </div>
            </div>
            <?php if (!empty($trend)): ?>
                <div class="mt-4 w-full overflow-hidden">
                    <svg id="trend-chart" viewBox="0 0 760 280" class="h-auto w-full" preserveAspectRatio="none" role="img" aria-label="<?= panel_h($dateRange['label'] ?? __('topbar.period_30d')) ?> <?= panel_h(__('overview.trend_subtitle')) ?>"></svg>
                    <script type="application/json" id="trend-data"><?= panel_json($trend) ?></script>
                </div>
            <?php else: ?>
                <div class="mt-5 rounded-xl border border-dashed border-slate-200 px-4 py-12 text-center text-sm text-slate-500"><?= panel_h(__('overview.no_trend_data')) ?></div>
            <?php endif; ?>
        </div>

        <div class="flex h-full flex-col rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800"><?= panel_h(__('overview.popular_pages')) ?></h2>
                    <p class="mt-0.5 text-sm text-slate-500"><?= panel_h(__('overview.popular_subtitle')) ?></p>
                </div>
                <a href="/analytics/pages?<?= htmlspecialchars(http_build_query($pagesQuery, '', '&', PHP_QUERY_RFC3986), ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700"><?= panel_h(__('overview.all')) ?> <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i></a>
            </div>
            <div class="mt-5 flex flex-1 flex-col gap-1.5">
                <?php if (empty($popularPages)): ?>
                    <?php panel_empty(__('overview.no_pages')); ?>
                <?php else: ?>
                    <?php foreach ($popularPages as $index => $item): ?>
                        <?php $width = $maxPopularViews > 0 ? (int) round((int) $item['views'] * 100 / $maxPopularViews) : 0; ?>
                        <div class="group flex items-center gap-3 rounded-xl px-2 py-2.5 transition-colors hover:bg-slate-50">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500"><?= $index + 1 ?></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-700"><?= panel_h($item['page_title'] ?: panel_path($item['page_url'])) ?></p>
                                <div class="mt-1 flex items-center gap-1.5"><i data-lucide="link-2" class="h-3 w-3 shrink-0 text-slate-400"></i><span class="truncate text-xs text-slate-400"><?= panel_h(panel_path($item['page_url'])) ?></span></div>
                                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 transition-all duration-700" style="width:<?= $width ?>%"></div></div>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-slate-800"><?= panel_number($item['views']) ?></p>
                                <p class="text-[10px] text-slate-400"><?= panel_h(__('overview.views')) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2 xl:gap-6">
        <?php foreach ([
            ['title' => __('overview.browser_dist'), 'subtitle' => __('overview.browser_subtitle'), 'data' => $browserStats, 'type' => 'browser'],
            ['title' => __('overview.os_dist'),      'subtitle' => __('overview.os_subtitle'),      'data' => $osStats,      'type' => 'os'],
        ] as $group): ?>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl <?= $group['type'] === 'browser' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' ?>">
                        <i data-lucide="<?= $group['type'] === 'browser' ? 'globe-2' : 'monitor' ?>" class="h-5 w-5"></i>
                    </div>
                    <div><h3 class="text-base font-semibold text-slate-800"><?= panel_h($group['title']) ?></h3><p class="text-xs text-slate-500"><?= panel_h($group['subtitle']) ?></p></div>
                </div>
                <?php if (empty($group['data'])): ?>
                    <div class="mt-5"><?php panel_empty(__('overview.no_dist_data')); ?></div>
                <?php else: ?>
                    <div class="mt-5 flex flex-col items-center gap-5 sm:flex-row">
                        <?= panel_donut($group['data'], __('overview.total'), panel_number(array_sum(array_column($group['data'], 'count')))) ?>
                        <div class="w-full flex-1 space-y-3">
                            <?php foreach (array_slice($group['data'], 0, 5) as $item): ?>
                                <div>
                                    <div class="mb-1 flex items-center justify-between"><span class="text-xs font-medium text-slate-600"><?= panel_h($item['label']) ?></span><span class="text-xs font-semibold text-slate-700"><?= panel_percent($item['value']) ?></span></div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full transition-all duration-700" style="width:<?= (float) $item['value'] ?>%;background:<?= panel_h($item['color']) ?>"></div></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <footer class="mt-8 flex flex-col items-center justify-between gap-2 border-t border-slate-200 pt-5 text-xs text-slate-400 sm:flex-row">
        <p>© <?= date('Y') ?> <?= panel_h(__('app.name')) ?> <?= panel_h(__('app.tagline')) ?> — <?= panel_h(__('all_rights_reserved')) ?></p>
        <p><?= panel_h(__('last_updated')) ?>: <?= date('d.m.Y H:i') ?></p>
    </footer>
<?php endif; ?>
