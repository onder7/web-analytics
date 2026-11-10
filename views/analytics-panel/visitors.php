<?php
require_once __DIR__ . '/helpers.php';
$visitorTypeMap = [];
foreach ($visitorTypes as $type) $visitorTypeMap[$type['label']] = (int) $type['count'];
$countryCount = (int) ($summary['countries'] ?? 0);
$deviceIcons = ['desktop' => 'monitor', 'mobile' => 'smartphone', 'tablet' => 'tablet', 'other' => 'monitor'];
$deviceLabels = ['desktop' => __('visitors.device_desktop'), 'mobile' => __('visitors.device_mobile'), 'tablet' => __('visitors.device_tablet'), 'other' => __('visitors.device_other')];
?>
<?php if (!$website): ?>
    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-6 text-sm text-blue-800"><?= panel_h(__('visitors.no_website')) ?></div>
<?php else: ?>
    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <?php foreach ([
            [__('visitors.total'), $summary['visitors'] ?? 0, 'users', 'blue'],
            [__('visitors.new'), $visitorTypeMap['Yeni'] ?? 0, 'user-plus', 'emerald'],
            [__('visitors.returning'), $visitorTypeMap['Geri dönen'] ?? 0, 'user-check', 'amber'],
            [__('visitors.countries'), $countryCount, 'globe-2', 'rose']
        ] as $index => $stat): ?>
            <?php $color = ['blue' => 'bg-blue-50 text-blue-600', 'emerald' => 'bg-emerald-50 text-emerald-600', 'amber' => 'bg-amber-50 text-amber-600', 'rose' => 'bg-rose-50 text-rose-600'][$stat[3]]; ?>
            <article class="animate-slide-up rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" style="animation-delay:<?= $index * 80 ?>ms">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl <?= $color ?>"><i data-lucide="<?= panel_h($stat[2]) ?>" class="h-5 w-5"></i></div>
                <p class="mt-3 text-2xl font-bold text-slate-800"><?= panel_number($stat[1]) ?></p>
                <p class="text-sm text-slate-500"><?= panel_h($stat[0]) ?></p>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-3 xl:gap-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <?php panel_card_header(__('visitors.geo_title'), __('visitors.geo_subtitle')); ?>
            <?php if (empty($geoStats)): ?>
                <?php panel_empty(__('visitors.no_geo')); ?>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($geoStats, 0, 8) as $country): ?>
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i data-lucide="map-pin" class="h-4 w-4"></i></span>
                            <div class="min-w-0 flex-1">
                                <div class="mb-1 flex items-center justify-between"><span class="truncate text-sm font-medium text-slate-700"><?= panel_h($country['label']) ?></span><span class="text-xs font-semibold text-slate-600"><?= panel_percent($country['value']) ?></span></div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-500" style="width:<?= (float) $country['value'] ?>%"></div></div>
                            </div>
                            <span class="shrink-0 text-xs font-medium text-slate-500"><?= panel_number($country['count']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php foreach ([
            [
                __('visitors.type_title'),
                __('visitors.type_subtitle'),
                $visitorTypes,
                'Ziyaretçi',
                panel_number($summary['visitors'] ?? 0),
            ],
            [__('visitors.device_title'), __('visitors.device_subtitle'), $deviceStats, __('visitors.device_label'), (string) count($deviceStats)]
        ] as $group): ?>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <?php panel_card_header($group[0], $group[1]); ?>
                <?php if (empty($group[2])): ?>
                    <?php panel_empty(__('visitors.no_distribution')); ?>
                <?php else: ?>
                    <div class="flex flex-col items-center gap-4">
                        <?= panel_donut($group[2], $group[3], $group[4]) ?>
                        <div class="w-full space-y-2">
                            <?php foreach ($group[2] as $item): ?>
                                <div>
                                    <div class="mb-1 flex items-center justify-between"><div class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full" style="background:<?= panel_h($item['color']) ?>"></span><span class="text-sm text-slate-600"><?= panel_h($item['label']) ?></span></div><span class="text-sm font-semibold text-slate-700"><?= panel_percent($item['value']) ?></span></div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full transition-all duration-700" style="width:<?= (float) $item['value'] ?>%;background:<?= panel_h($item['color']) ?>"></div></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
        <div class="mb-5 flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <div><h2 class="text-base font-semibold text-slate-800"><?= panel_h(__('visitors.list_title')) ?></h2><p class="mt-0.5 text-xs text-slate-500"><?= panel_h($dateRange['label'] ?? __('topbar.period_30d')) ?> <?= panel_h(__('visitors.list_subtitle')) ?></p></div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label class="relative"><span class="sr-only"><?= panel_h(__('visitors.search_placeholder')) ?></span><i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i><input type="search" data-filter-input="#visitors-table" placeholder="<?= panel_h(__('visitors.search_placeholder')) ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 sm:w-64"></label>
                <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1">
                    <button type="button" data-visitor-filter="all" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm"><?= panel_h(__('visitors.filter_all')) ?></button>
                    <button type="button" data-visitor-filter="Yeni" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-500"><?= panel_h(__('visitors.filter_new')) ?> (<?= panel_number($visitorTypeMap['Yeni'] ?? 0) ?>)</button>
                    <button type="button" data-visitor-filter="Geri dönen" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-500"><?= panel_h(__('visitors.filter_returning')) ?> (<?= panel_number($visitorTypeMap['Geri dönen'] ?? 0) ?>)</button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table id="visitors-table" class="w-full min-w-[760px] text-sm">
                <thead><tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><th class="pb-3 pr-4"><?= panel_h(__('visitors.table_visitor')) ?></th><th class="pb-3 pr-4"><?= panel_h(__('visitors.table_type')) ?></th><th class="pb-3 pr-4"><?= panel_h(__('visitors.table_location')) ?></th><th class="pb-3 pr-4"><?= panel_h(__('visitors.table_device')) ?></th><th class="pb-3 pr-4"><?= panel_h(__('visitors.table_last_page')) ?></th><th class="pb-3 text-right"><?= panel_h(__('visitors.table_last_visit')) ?></th></tr></thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($visitors)): ?>
                        <tr><td colspan="6" class="py-8 text-center text-sm text-slate-500"><?= panel_h(__('visitors.no_visitors')) ?></td></tr>
                    <?php else: ?>
                        <?php foreach ($visitors as $visitor): ?>
                            <?php
                            $location = trim(implode(', ', array_filter([$visitor['city'] ?? '', $visitor['country'] ?? ''])));
                            $device = $deviceLabels[$visitor['device_type'] ?? ''] ?? __('visitors.unknown');
                            ?>
                            <tr data-visitor-type="<?= panel_h($visitor['visitor_type']) ?>" class="transition-colors hover:bg-slate-50/60">
                                <td class="py-3 pr-4"><span class="font-mono text-xs font-medium text-slate-700"><?= panel_h($visitor['visitor_id']) ?></span></td>
                                <td class="py-3 pr-4"><span class="rounded-lg px-2 py-0.5 text-xs font-medium <?= $visitor['visitor_type'] === 'Yeni' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' ?>"><?= panel_h($visitor['visitor_type']) ?></span></td>
                                <td class="py-3 pr-4 text-slate-600"><?= panel_h($location ?: __('visitors.unknown')) ?></td>
                                <td class="py-3 pr-4"><span class="inline-flex items-center gap-1.5 text-slate-600"><i data-lucide="<?= panel_h($deviceIcons[$visitor['device_type'] ?? ''] ?? 'monitor') ?>" class="h-3.5 w-3.5"></i><?= panel_h($device) ?></span></td>
                                <td class="max-w-56 truncate py-3 pr-4 text-slate-600" title="<?= panel_h($visitor['last_page']) ?>"><?= panel_h(panel_path($visitor['last_page'])) ?></td>
                                <td class="py-3 text-right text-xs text-slate-500"><?= panel_h(panel_datetime($visitor['last_visit'], $website['timezone'] ?? 'UTC')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
