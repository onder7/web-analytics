<?php
$baseWebsiteQuery = $websiteId ? '?website_id=' . rawurlencode((string) $websiteId) : '';
$analyticsQuery = array_filter([
    'website_id' => $websiteId,
    'period' => $dateRange['period'] ?? null,
    'start_date' => ($dateRange['period'] ?? '') === 'custom' ? ($dateRange['start_date'] ?? null) : null,
    'end_date' => ($dateRange['period'] ?? '') === 'custom' ? ($dateRange['end_date'] ?? null) : null
], function($value) {
    return $value !== null && $value !== '';
});
$baseAnalyticsQuery = '?' . http_build_query($analyticsQuery, '', '&', PHP_QUERY_RFC3986);
$panelNav = [
    'overview' => ['sidebar.dashboard', 'layout-dashboard'],
    'statistics' => ['sidebar.statistics', 'bar-chart-3'],
    'traffic' => ['sidebar.traffic', 'activity'],
    'visitors' => ['sidebar.visitors', 'users'],
    'pages' => ['sidebar.pages', 'file-text'],
    'sources' => ['sidebar.sources', 'globe-2']
];
?>
<div id="sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-slate-950/50 backdrop-blur-sm lg:hidden" data-sidebar-close></div>
<aside id="analytics-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-slate-950 transition-all duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
    <div class="flex items-center gap-3 border-b border-slate-700/70 px-4 py-5">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-500/30">
            <i data-lucide="trending-up" class="h-5 w-5"></i>
        </div>
        <div class="sidebar-label min-w-0">
            <h1 class="text-sm font-bold leading-tight text-white">WebStats</h1>
            <p class="text-[11px] text-slate-400">Analytics Panel</p>
        </div>
        <button type="button" class="ml-auto rounded-lg p-2 text-slate-400 hover:bg-slate-800 lg:hidden" aria-label="<?= panel_h(__('sidebar.close')) ?>" data-sidebar-close>
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    </div>

    <?php if (!empty($websites)): ?>
        <div class="border-b border-slate-700/70 p-3 lg:hidden">
            <label for="mobile-website-select" class="mb-1 block text-[10px] font-semibold uppercase tracking-wider text-slate-400"><?= panel_h(__('sidebar.websites')) ?></label>
            <select id="mobile-website-select" class="w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-sm text-white" data-website-select>
                <?php foreach ($websites as $site): ?>
                    <option value="<?= (int) $site['id'] ?>" <?= $websiteId === (int) $site['id'] ? 'selected' : '' ?>><?= htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <nav class="scrollbar-thin flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">
        <p class="sidebar-label px-3.5 pb-1 pt-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400"><?= panel_h(__('sidebar.section_menu')) ?></p>
        <?php foreach ($panelNav as $navPage => $navItem): ?>
            <a href="/analytics/<?= rawurlencode($navPage) . $baseAnalyticsQuery ?>"
               <?= $page === $navPage ? 'aria-current="page"' : '' ?>
               class="group relative flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all duration-200 <?= $page === $navPage ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <?php if ($page === $navPage): ?><span class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full bg-cyan-200"></span><?php endif; ?>
                <i data-lucide="<?= htmlspecialchars($navItem[1], ENT_QUOTES, 'UTF-8') ?>" class="h-5 w-5 shrink-0"></i>
                <span class="sidebar-label truncate"><?= panel_h(__($navItem[0])) ?></span>
                <?php if ($navPage === 'statistics'): ?><span class="sidebar-label ml-auto rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-semibold text-emerald-400"><?= panel_h(__('sidebar.live')) ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>

        <div class="my-3 border-t border-slate-700/70"></div>
        <p class="sidebar-label px-3.5 pb-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400"><?= panel_h(__('sidebar.section_system')) ?></p>
        <a href="/analytics/settings<?= htmlspecialchars($baseWebsiteQuery, ENT_QUOTES, 'UTF-8') ?>"
           <?= $page === 'settings' ? 'aria-current="page"' : '' ?>
           class="group relative flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all <?= $page === 'settings' ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
            <i data-lucide="settings" class="h-5 w-5 shrink-0"></i><span class="sidebar-label"><?= panel_h(__('sidebar.settings')) ?></span>
        </a>
        <a href="/analytics/notifications<?= htmlspecialchars($baseWebsiteQuery, ENT_QUOTES, 'UTF-8') ?>" <?= $page === 'notifications' ? 'aria-current="page"' : '' ?> class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-colors <?= $page === 'notifications' ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
            <i data-lucide="bell" class="h-5 w-5 shrink-0"></i><span class="sidebar-label"><?= panel_h(__('sidebar.notifications')) ?></span>
        </a>
        <a href="/analytics/system" <?= $page === 'system' ? 'aria-current="page"' : '' ?> class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-colors <?= $page === 'system' ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
            <i data-lucide="shield-check" class="h-5 w-5 shrink-0"></i><span class="sidebar-label"><?= panel_h(__('sidebar.system')) ?></span>
        </a>
        <a href="/websites" class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-colors text-slate-300 hover:bg-slate-800 hover:text-white">
            <i data-lucide="globe" class="h-5 w-5 shrink-0"></i><span class="sidebar-label"><?= panel_h(__('sidebar.websites')) ?></span>
        </a>
        <a href="/logout" class="mt-2 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-300 transition-colors hover:bg-rose-950 hover:text-rose-200">
            <i data-lucide="log-out" class="h-5 w-5 shrink-0"></i><span class="sidebar-label"><?= panel_h(__('sidebar.logout')) ?></span>
        </a>
    </nav>

    <div class="border-t border-slate-700/70 p-3">
        <p class="sidebar-label mb-2 px-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400"><?= panel_h(__('sidebar.language')) ?></p>
        <div class="mb-2 flex gap-2">
            <?php foreach (['tr' => ['lang.tr', '🇹🇷'], 'en' => ['lang.en', '🇬🇧']] as $languageCode => $language): ?>
                <form method="post" action="/change-language" class="min-w-0 flex-1">
                    <input type="hidden" name="locale" value="<?= panel_h($languageCode) ?>">
                    <button type="submit"
                            aria-label="<?= panel_h(__('lang.switch') . ': ' . __($language[0])) ?>"
                            aria-pressed="<?= Lang::locale() === $languageCode ? 'true' : 'false' ?>"
                            title="<?= panel_h(__($language[0])) ?>"
                            class="flex w-full items-center justify-center gap-1.5 rounded-lg border px-2 py-2 text-xs font-medium transition-colors <?= Lang::locale() === $languageCode ? 'border-blue-500/40 bg-blue-500/15 text-blue-200' : 'border-slate-700 text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                        <span aria-hidden="true"><?= $language[1] ?></span>
                        <span class="sidebar-label truncate"><?= panel_h(__($language[0])) ?></span>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
        <button type="button" id="sidebar-collapse" class="hidden w-full items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-medium text-slate-300 transition-colors hover:bg-slate-800 hover:text-white lg:flex" aria-label="<?= panel_h(__('sidebar.collapse')) ?>">
            <i data-lucide="chevron-left" class="sidebar-chevron h-4 w-4 transition-transform duration-300"></i>
            <span class="sidebar-label"><?= panel_h(__('sidebar.collapse')) ?></span>
        </button>
    </div>
</aside>
