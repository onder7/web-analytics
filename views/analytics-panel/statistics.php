<?php
require_once __DIR__ . '/helpers.php';
$liveCards = [
    ['label' => __('stats.active_now'),   'value' => $summary['active_now'] ?? 0,              'icon' => 'radio',    'color' => 'emerald', 'attr' => 'data-active-now-count'],
    ['label' => __('stats.last_hour'),    'value' => $summary['views_hour'] ?? 0,               'icon' => 'eye',      'color' => 'blue',    'attr' => ''],
    ['label' => __('stats.last_24h'),     'value' => $summary['views_day'] ?? 0,                'icon' => 'users',    'color' => 'amber',   'attr' => ''],
    ['label' => __('stats.avg_session'),  'value' => panel_duration($sessionSummary['avg_duration'] ?? 0), 'icon' => 'clock-3', 'color' => 'rose', 'attr' => ''],
];
$liveColors = [
    'blue' => 'bg-blue-50 text-blue-600',
    'emerald' => 'bg-emerald-50 text-emerald-600',
    'amber' => 'bg-amber-50 text-amber-600',
    'rose' => 'bg-rose-50 text-rose-600'
];
$maxHourly = !empty($hourlyStats) ? max(array_column($hourlyStats, 'views')) : 0;
$maxHeat = 0;
foreach ($heatmap as $day) {
    if (!empty($day['hours'])) $maxHeat = max($maxHeat, max($day['hours']));
}
$heatColors = ['#f0f9ff', '#bae6fd', '#7dd3fc', '#38bdf8', '#0ea5e9', '#0284c7'];
?>
<?php if (!$website): ?>
    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-6 text-sm text-blue-800"><?= panel_h(__('stats.no_website')) ?></div>
<?php else: ?>
    <div class="mb-5 flex items-center gap-2.5 rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-3">
        <span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span></span>
        <p class="text-sm font-medium text-emerald-700"><?= panel_h(__('stats.live_label')) ?> — <?= panel_h(__('stats.live_time')) ?>: <?= panel_h((new DateTimeImmutable('now', new DateTimeZone($website['timezone'] ?? 'UTC')))->format('H:i:s')) ?></p>
    </div>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4" data-realtime-active-url="/analytics/realtime?website_id=<?= (int) $websiteId ?>">
        <?php foreach ($liveCards as $index => $item): ?>
            <article class="animate-slide-up rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" style="animation-delay:<?= $index * 80 ?>ms">
                <div class="flex items-center justify-between"><div class="flex h-11 w-11 items-center justify-center rounded-xl <?= $liveColors[$item['color']] ?>"><i data-lucide="<?= panel_h($item['icon']) ?>" class="h-5 w-5"></i></div><span class="flex items-center gap-1 text-xs font-semibold text-emerald-600"><i data-lucide="radio" class="h-3.5 w-3.5"></i><?= panel_h(__('stats.live_badge')) ?></span></div>
                <p class="mt-3 text-2xl font-bold text-slate-800" <?= $item['attr'] ?>><?= is_numeric($item['value']) ? panel_number($item['value']) : panel_h($item['value']) ?></p>
                <p class="mt-0.5 text-sm text-slate-500"><?= panel_h($item['label']) ?></p>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
        <?php panel_card_header(__('stats.hourly_title'), __('stats.hourly_subtitle')); ?>
        <?php if ($maxHourly <= 0): ?>
            <?php panel_empty(__('stats.no_hourly')); ?>
        <?php else: ?>
            <div class="flex h-64 items-end justify-between gap-1 overflow-x-auto pt-5">
                <?php foreach ($hourlyStats as $hour): ?>
                    <?php $height = $maxHourly > 0 ? max(2, (int) round($hour['views'] * 100 / $maxHourly)) : 0; ?>
                    <div class="group relative flex h-full min-w-5 flex-1 flex-col items-center justify-end">
                        <div class="pointer-events-none absolute bottom-full z-10 mb-2 hidden whitespace-nowrap rounded-lg bg-slate-800 px-2.5 py-1.5 text-xs font-semibold text-white shadow-lg group-hover:block"><?= (int) $hour['hour'] ?>:00 — <?= panel_number($hour['views']) ?></div>
                        <div class="w-full max-w-7 rounded-t-lg bg-gradient-to-t from-blue-600 to-blue-400 transition-all duration-300 group-hover:opacity-80" style="height:<?= $height ?>%;animation:analytics-slide-up .4s ease-out both;animation-delay:<?= (int) $hour['hour'] * 20 ?>ms"></div>
                        <span class="mt-2 text-[10px] font-medium text-slate-400"><?= sprintf('%02d', $hour['hour']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-5 xl:gap-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm xl:col-span-3 lg:p-6">
            <?php panel_card_header(__('stats.heatmap_title'), __('stats.heatmap_subtitle')); ?>
            <?php if ($maxHeat <= 0): ?>
                <?php panel_empty(__('stats.no_heatmap')); ?>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <div class="min-w-[600px]">
                        <div class="mb-1 flex gap-0.5 pl-12"><?php for ($hour = 0; $hour < 24; $hour++): ?><div class="flex-1 text-center text-[8px] font-medium text-slate-400"><?= $hour % 3 === 0 ? $hour : '' ?></div><?php endfor; ?></div>
                        <?php foreach ($heatmap as $day): ?>
                            <div class="mb-0.5 flex items-center gap-0.5"><span class="w-10 shrink-0 text-xs font-semibold text-slate-500"><?= panel_h($day['day']) ?></span>
                                <?php foreach ($day['hours'] as $hour => $value): ?>
                                    <?php $level = $maxHeat > 0 ? min(5, (int) floor($value / $maxHeat * 6)) : 0; ?>
                                    <div class="group relative aspect-square flex-1 rounded-sm transition-transform hover:scale-125 hover:ring-2 hover:ring-slate-300" style="background:<?= $heatColors[$level] ?>" title="<?= panel_h($day['day']) ?> <?= (int) $hour ?>:00 — <?= panel_number($value) ?>" aria-label="<?= panel_h($day['day']) ?> <?= (int) $hour ?>:00 — <?= panel_number($value) ?>"></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="mt-3 flex items-center justify-end gap-1.5"><span class="text-[10px] text-slate-400"><?= panel_h(__('stats.heatmap_low')) ?></span><?php foreach ($heatColors as $color): ?><span class="h-3 w-3 rounded-sm" style="background:<?= $color ?>"></span><?php endforeach; ?><span class="text-[10px] text-slate-400"><?= panel_h(__('stats.heatmap_high')) ?></span></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm xl:col-span-2 lg:p-6">
            <?php panel_card_header(__('stats.activity_title'), __('stats.activity_subtitle')); ?>
            <?php if (empty($activity)): ?>
                <?php panel_empty(__('stats.no_activity')); ?>
            <?php else: ?>
                <div class="space-y-1">
                    <?php foreach ($activity as $item): ?>
                        <?php
                        $location   = trim(implode(', ', array_filter([$item['city'] ?? '', $item['country'] ?? ''])));
                        $minutes    = max(0, (int) floor((time() - strtotime($item['created_at'])) / 60));
                        $timeLabel  = $minutes < 1
                            ? __('stats.just_now')
                            : ($minutes < 60 ? $minutes . ' ' . __('stats.minutes_ago') : panel_datetime($item['created_at'], $website['timezone'] ?? 'UTC', 'H:i'));
                        ?>
                        <div class="flex items-center gap-3 rounded-xl px-2 py-2.5 transition-colors hover:bg-slate-50">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i data-lucide="eye" class="h-4 w-4"></i></div>
                            <div class="min-w-0 flex-1"><p class="truncate text-sm font-medium text-slate-700"><?= panel_h(__('stats.page_viewed')) ?></p><p class="truncate text-xs text-slate-400"><?= panel_h(panel_path($item['page_url'])) ?><?= $location !== '' ? ' — ' . panel_h($location) : '' ?></p></div>
                            <div class="shrink-0 text-right"><p class="text-xs font-medium text-slate-500"><?= panel_h($item['device_type'] ?: __('stats.unknown_device')) ?></p><p class="text-[10px] text-slate-400"><?= panel_h($timeLabel) ?></p></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
