<header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/80 backdrop-blur-md">
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3.5 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button type="button" id="mobile-menu-button" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="<?= panel_h(__('topbar.open_menu')) ?>">
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>
            <div class="hidden min-w-0 sm:block">
                <h2 class="truncate text-lg font-bold text-slate-800"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="truncate text-xs text-slate-500"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <div class="ml-auto flex items-center gap-2 lg:gap-3">
            <label class="relative hidden md:block">
                <span class="sr-only"><?= panel_h(__('topbar.search_placeholder')) ?></span>
                <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input type="search" data-global-search placeholder="<?= panel_h(__('topbar.search_placeholder')) ?>" class="w-44 rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder-slate-400 transition-all focus:w-56 focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>
            <?php if (!empty($websites) && !in_array($page, ['websites', 'system'], true)): ?>
                <form method="get" action="/analytics/<?= rawurlencode($page) ?>" class="hidden sm:block">
                    <label class="sr-only" for="website-select"><?= panel_h(__('topbar.select_website')) ?></label>
                    <select id="website-select" name="website_id" data-website-select class="max-w-52 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <?php foreach ($websites as $site): ?>
                            <option value="<?= (int) $site['id'] ?>" <?= $websiteId === (int) $site['id'] ? 'selected' : '' ?>><?= htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
            <?php if (!in_array($page, ['settings', 'notifications', 'invitation', 'system'], true)): ?>
                <form method="get" action="/analytics/<?= rawurlencode($page) ?>" class="flex flex-wrap items-center gap-2">
                    <?php if ($websiteId): ?>
                        <input type="hidden" name="website_id" value="<?= (int) $websiteId ?>">
                    <?php endif; ?>
                    <label class="sr-only" for="analytics-period"><?= panel_h(__('topbar.date_range')) ?></label>
                    <select id="analytics-period" name="period" data-period-select class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <?php foreach ([
                            '7d'     => __('topbar.period_7d'),
                            '30d'    => __('topbar.period_30d'),
                            '90d'    => __('topbar.period_90d'),
                            'custom' => __('topbar.period_custom'),
                        ] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($dateRange['period'] ?? '30d') === $value ? 'selected' : '' ?>><?= panel_h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <fieldset data-custom-date-fields class="<?= ($dateRange['period'] ?? '') === 'custom' ? 'flex' : 'hidden' ?> flex-wrap items-center gap-2">
                        <legend class="sr-only"><?= panel_h(__('topbar.custom_range')) ?></legend>
                        <label class="sr-only" for="analytics-start-date"><?= panel_h(__('topbar.start_date')) ?></label>
                        <input id="analytics-start-date" type="date" name="start_date" value="<?= htmlspecialchars($dateRange['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= ($dateRange['period'] ?? '') === 'custom' ? 'required' : '' ?> class="rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-sm text-slate-600 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <label class="sr-only" for="analytics-end-date"><?= panel_h(__('topbar.end_date')) ?></label>
                        <input id="analytics-end-date" type="date" name="end_date" value="<?= htmlspecialchars($dateRange['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= ($dateRange['period'] ?? '') === 'custom' ? 'required' : '' ?> class="rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-sm text-slate-600 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </fieldset>
                    <button type="submit" class="rounded-xl bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700"><?= panel_h(__('apply')) ?></button>
                </form>
            <?php elseif ($page !== 'system'): ?>
                <div class="hidden items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 sm:flex">
                    <i data-lucide="calendar-days" class="h-4 w-4 text-slate-400"></i>
                    <span><?= htmlspecialchars($dateRange['label'] ?? __('topbar.period_30d'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>
            <?php if (in_array($page, ['pages', 'visitors', 'traffic'], true)): ?>
                <?php
                $exportQuery = ['type' => $page, 'website_id' => $websiteId, 'period' => $dateRange['period'] ?? '30d'];
                if (($dateRange['period'] ?? '') === 'custom') {
                    $exportQuery['start_date'] = $dateRange['start_date'];
                    $exportQuery['end_date'] = $dateRange['end_date'];
                }
                ?>
                <a href="/analytics/export?<?= htmlspecialchars(http_build_query($exportQuery, '', '&', PHP_QUERY_RFC3986), ENT_QUOTES, 'UTF-8') ?>" class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50" aria-label="<?= panel_h(__('topbar.download_csv')) ?>" title="<?= panel_h(__('topbar.download_csv')) ?>">
                    <i data-lucide="download" class="h-4 w-4"></i>
                </a>
            <?php elseif ($page !== 'system'): ?>
                <button type="button" data-export-table class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50" aria-label="<?= panel_h(__('topbar.download_table_csv')) ?>" title="<?= panel_h(__('topbar.download_table_csv')) ?>">
                    <i data-lucide="download" class="h-4 w-4"></i>
                </button>
            <?php endif; ?>
            <a href="/analytics/notifications<?= htmlspecialchars($websiteId ? '?website_id=' . rawurlencode((string) $websiteId) : '', ENT_QUOTES, 'UTF-8') ?>" class="relative flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50" aria-label="<?= panel_h(__('topbar.notification_settings')) ?>" title="<?= panel_h(__('topbar.notification_settings')) ?>">
                <i data-lucide="bell" class="h-4 w-4"></i>
            </a>

            <!-- Dil değiştirici -->
            <div class="relative" data-lang-switcher>
                <button type="button" class="flex h-9 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-50" aria-label="<?= panel_h(__('lang.switch')) ?>" data-lang-toggle>
                    <i data-lucide="languages" class="h-4 w-4 text-slate-400"></i>
                    <span class="uppercase"><?= panel_h(Lang::locale()) ?></span>
                    <i data-lucide="chevron-down" class="h-3 w-3 text-slate-400"></i>
                </button>
                <div class="absolute right-0 top-full z-50 mt-1 hidden w-36 overflow-hidden rounded-xl border border-slate-100 bg-white shadow-lg" data-lang-menu>
                    <?php foreach (['tr' => __('lang.tr'), 'en' => __('lang.en')] as $code => $label): ?>
                        <form method="POST" action="/change-language">
                            <input type="hidden" name="locale" value="<?= panel_h($code) ?>">
                            <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 transition-colors hover:bg-slate-50 <?= Lang::locale() === $code ? 'font-semibold text-blue-600' : '' ?>">
                                <?= $code === 'tr' ? '🇹🇷' : '🇬🇧' ?>
                                <?= panel_h($label) ?>
                                <?php if (Lang::locale() === $code): ?><i data-lucide="check" class="ml-auto h-3.5 w-3.5"></i><?php endif; ?>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="/analytics/settings?tab=profile<?= $websiteId ? '&amp;website_id=' . rawurlencode((string) $websiteId) : '' ?>" aria-label="<?= panel_h(__('settings.profile')) ?>" title="<?= panel_h(__('settings.profile')) ?>" class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 transition-colors hover:bg-slate-50">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 text-xs font-bold uppercase text-white">
                    <?= htmlspecialchars(substr((string) ($profile['username'] ?? 'K'), 0, 1), ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="hidden text-left sm:block">
                    <p class="max-w-32 truncate text-xs font-semibold leading-tight text-slate-700"><?= htmlspecialchars($profile['username'] ?? 'Kullanıcı', ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-[10px] text-slate-400"><?= panel_h(__('admin')) ?></p>
                </div>
            </a>
        </div>
    </div>
    <?php if (!empty($dateRangeError)): ?>
        <p class="px-4 pb-3 text-sm font-medium text-rose-600 lg:px-8" role="alert"><?= htmlspecialchars($dateRangeError, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</header>

<script>
// Dil değiştirici dropdown toggle
(function () {
    const switcher = document.querySelector('[data-lang-switcher]');
    if (!switcher) return;
    const toggle = switcher.querySelector('[data-lang-toggle]');
    const menu   = switcher.querySelector('[data-lang-menu]');
    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('hidden');
    });
    document.addEventListener('click', function () {
        menu.classList.add('hidden');
    });
})();
</script>
