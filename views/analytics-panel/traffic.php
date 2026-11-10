<?php
require_once __DIR__ . '/helpers.php';
$totalTraffic = (int) ($summary['visitors'] ?? 0);
$maxFunnel = !empty($funnel) ? max(array_column($funnel, 'count')) : 0;
?>
<?php if (!$website): ?>
    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-6 text-sm text-blue-800"><?= panel_h(__('traffic.no_website')) ?></div>
<?php else: ?>
    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3 xl:gap-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <?php panel_card_header(__('traffic.sources_title'), __('traffic.sources_subtitle')); ?>
            <?php if (empty($trafficSources)): ?>
                <?php panel_empty(__('traffic.no_sources')); ?>
            <?php else: ?>
                <div class="flex flex-col items-center gap-5 sm:flex-row">
                    <?= panel_donut($sourceCategories, __('traffic.source_label'), (string) count(array_filter($sourceCategories, function($item) { return $item['count'] > 0; }))) ?>
                    <div class="w-full flex-1 space-y-3">
                        <?php foreach ($sourceCategories as $item): ?>
                            <div>
                                <div class="mb-1 flex items-center justify-between"><span class="text-xs font-medium text-slate-600"><?= panel_h($item['label']) ?></span><span class="text-xs font-semibold text-slate-700"><?= panel_percent($item['value']) ?></span></div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full transition-all duration-700" style="width:<?= (float) $item['value'] ?>%;background:<?= panel_h($item['color']) ?>"></div></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm xl:col-span-2">
            <?php panel_card_header(__('traffic.funnel_title'), __('traffic.funnel_subtitle')); ?>
            <?php if ($maxFunnel <= 0): ?>
                <?php panel_empty(__('traffic.no_funnel')); ?>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($funnel as $index => $step): ?>
                        <?php $width = $maxFunnel > 0 ? min(100, (int) round($step['count'] * 100 / $maxFunnel)) : 0; ?>
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <div class="flex items-center gap-2"><span class="flex h-6 w-6 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600"><?= (int) $step['step'] ?></span><span class="text-sm font-medium text-slate-700"><?= panel_h($step['label']) ?></span></div>
                                <div class="flex items-center gap-3"><span class="text-sm font-bold text-slate-800"><?= panel_number($step['count']) ?></span><span class="text-xs font-semibold text-slate-500"><?= panel_percent($step['rate']) ?></span></div>
                            </div>
                            <div class="h-7 w-full overflow-hidden rounded-lg bg-slate-100"><div class="flex h-full items-center rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 transition-all duration-700" style="width:<?= $width ?>%;opacity:<?= max(.45, 1 - $index * .1) ?>"><span class="px-3 text-[10px] font-semibold text-white"><?= panel_percent($step['rate']) ?></span></div></div>
                        </div>
                    <?php endforeach; ?>
                    <p class="pt-1 text-[11px] text-slate-400"><?= panel_h(__('traffic.funnel_note')) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
        <?php panel_card_header(__('traffic.details_title'), __('traffic.details_subtitle') . ' (' . ($dateRange['label'] ?? __('topbar.period_30d')) . ')'); ?>
        <?php if (empty($trafficSources)): ?>
            <?php panel_empty(__('traffic.no_details')); ?>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-sm">
                    <thead><tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wide text-slate-400"><th class="pb-3 pr-4"><?= panel_h(__('traffic.table_source')) ?></th><th class="pb-3 pr-4"><?= panel_h(__('traffic.table_category')) ?></th><th class="pb-3 pr-4 text-right"><?= panel_h(__('traffic.table_views')) ?></th><th class="pb-3 text-right"><?= panel_h(__('traffic.table_share')) ?></th></tr></thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($trafficSources as $source): ?>
                            <tr class="transition-colors hover:bg-slate-50/60">
                                <td class="py-3 pr-4"><div class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:<?= panel_h($source['color']) ?>18;color:<?= panel_h($source['color']) ?>"><i data-lucide="<?= $source['category'] === 'search' ? 'search' : ($source['category'] === 'social' ? 'share-2' : ($source['category'] === 'direct' ? 'compass' : 'link-2')) ?>" class="h-4 w-4"></i></span><span class="font-medium text-slate-700"><?= panel_h($source['source']) ?></span></div></td>
                                <td class="py-3 pr-4"><span class="rounded-lg bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600"><?= panel_h($source['category_label']) ?></span></td>
                                <td class="py-3 pr-4 text-right font-semibold text-slate-700"><?= panel_number($source['visits']) ?></td>
                                <td class="py-3 text-right"><div class="flex items-center justify-end gap-2"><span class="hidden h-1.5 w-16 overflow-hidden rounded-full bg-slate-100 sm:block"><span class="block h-full rounded-full bg-blue-500" style="width:<?= (float) $source['percentage'] ?>%"></span></span><span class="text-slate-600"><?= panel_percent($source['percentage']) ?></span></div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2 xl:gap-6">
        <?php foreach ([
            [__('traffic.entry_title'), __('traffic.entry_subtitle'), $entryPages],
            [__('traffic.exit_title'), __('traffic.exit_subtitle'), $exitPages]
        ] as $list): ?>
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <?php panel_card_header($list[0], $list[1]); ?>
                <?php if (empty($list[2])): ?>
                    <?php panel_empty(__('traffic.no_flow')); ?>
                <?php else: ?>
                    <div class="space-y-3"><?php foreach ($list[2] as $item): ?>
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-50 p-3 hover:bg-slate-50/60">
                            <div class="flex min-w-0 items-center gap-2"><i data-lucide="file-text" class="h-4 w-4 shrink-0 text-slate-400"></i><span class="truncate text-sm font-medium text-slate-700"><?= panel_h(panel_path($item['url'])) ?></span></div>
                            <span class="shrink-0 text-sm font-bold text-slate-700"><?= panel_number($item['visits']) ?> <?= panel_h(__('traffic.visitors_suffix')) ?></span>
                        </div>
                    <?php endforeach; ?></div>
                    <p class="mt-3 text-[11px] text-slate-400"><?= panel_h(__('traffic.flow_note')) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
