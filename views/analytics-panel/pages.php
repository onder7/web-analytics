<?php
require_once __DIR__ . '/helpers.php';
$pageColors = ['blue' => 'bg-blue-50 text-blue-600', 'emerald' => 'bg-emerald-50 text-emerald-600', 'amber' => 'bg-amber-50 text-amber-600', 'rose' => 'bg-rose-50 text-rose-600'];
$maxPageViews = !empty($pages) ? max(array_column($pages, 'views')) : 0;
?>
<?php if (!$website): ?>
    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-6 text-sm text-blue-800"><?= panel_h(__('pages.no_website')) ?></div>
<?php else: ?>
    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <?php foreach ([
            [__('pages.total_pages'), $summary['unique_pages'] ?? 0, 'file-text', 'blue'],
            [__('pages.total_views'), $summary['pageviews'] ?? 0, 'trending-up', 'emerald'],
            [__('pages.avg_duration'), panel_duration($summary['avg_page_duration'] ?? 0), 'clock-3', 'amber'],
            [__('pages.avg_bounce'), number_format((float) ($sessionSummary['bounce_rate'] ?? 0), 1, ',', '.') . '%', 'circle-alert', 'rose']
        ] as $index => $stat): ?>
            <article class="animate-slide-up rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" style="animation-delay:<?= $index * 80 ?>ms">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl <?= $pageColors[$stat[3]] ?>"><i data-lucide="<?= panel_h($stat[2]) ?>" class="h-5 w-5"></i></div>
                <p class="mt-3 text-2xl font-bold text-slate-800"><?= panel_h($stat[1]) ?></p>
                <p class="text-sm text-slate-500"><?= panel_h($stat[0]) ?></p>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
        <?php panel_card_header(__('pages.analysis_title'), __('pages.analysis_subtitle')); ?>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[880px] text-sm" data-sort-table>
                <thead><tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                    <th class="pb-3 pr-4">#</th><th class="pb-3 pr-4"><?= panel_h(__('pages.table_page')) ?></th>
                    <th data-sort class="cursor-pointer select-none pb-3 pr-4 text-right hover:text-slate-600"><?= panel_h(__('pages.table_views')) ?> ↕</th>
                    <th data-sort class="cursor-pointer select-none pb-3 pr-4 text-right hover:text-slate-600"><?= panel_h(__('pages.table_unique')) ?> ↕</th>
                    <th data-sort class="cursor-pointer select-none pb-3 pr-4 text-right hover:text-slate-600"><?= panel_h(__('pages.table_duration')) ?> ↕</th>
                    <th class="pb-3 pr-4 text-right"><?= panel_h(__('pages.table_bounce')) ?></th><th class="pb-3 text-right"><?= panel_h(__('pages.table_exit')) ?></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($pages)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-sm text-slate-500"><?= panel_h(__('pages.no_data')) ?></td></tr>
                    <?php else: ?>
                        <?php foreach ($pages as $index => $item): ?>
                            <?php $duration = (int) round((float) $item['avg_duration']); $barWidth = $maxPageViews > 0 ? (int) round($item['views'] * 100 / $maxPageViews) : 0; ?>
                            <tr class="transition-colors hover:bg-slate-50/60">
                                <td class="py-3 pr-4 text-xs font-semibold text-slate-400"><?= $index + 1 ?></td>
                                <td class="py-3 pr-4"><p class="font-medium text-slate-700"><?= panel_h($item['page_title'] ?: panel_path($item['page_url'])) ?></p><p class="font-mono text-xs text-slate-400" title="<?= panel_h($item['page_url']) ?>"><?= panel_h(panel_path($item['page_url'])) ?></p></td>
                                <td class="py-3 pr-4" data-value="<?= (int) $item['views'] ?>"><div class="flex items-center justify-end gap-2"><span class="hidden h-1.5 w-20 overflow-hidden rounded-full bg-slate-100 lg:block"><span class="block h-full rounded-full bg-blue-500" style="width:<?= $barWidth ?>%"></span></span><span class="font-semibold text-slate-700"><?= panel_number($item['views']) ?></span></div></td>
                                <td class="py-3 pr-4 text-right text-slate-600" data-value="<?= (int) $item['unique_views'] ?>"><?= panel_number($item['unique_views']) ?></td>
                                <td class="py-3 pr-4 text-right font-mono text-xs text-slate-600" data-value="<?= $duration ?>"><?= (float) $item['avg_duration'] > 0 ? panel_duration($duration) : '—' ?></td>
                                <td class="py-3 pr-4 text-right text-slate-400" title="Bu oranı sayfaya bağlayan oturum verisi mevcut değil.">—</td>
                                <td class="py-3 text-right text-slate-400" title="Bu oranı sayfaya bağlayan oturum verisi mevcut değil.">—</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-[11px] text-slate-400"><?= panel_h(__('pages.note')) ?></p>
    </section>
<?php endif; ?>
