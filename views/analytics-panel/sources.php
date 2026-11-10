<?php
require_once __DIR__ . '/helpers.php';
$sourceCountByLabel = [];
foreach ($sourceCategories as $item) $sourceCountByLabel[$item['label']] = (int) $item['count'];
$sourceCards = [
    ['Arama Motoru', $sourceCountByLabel['Arama'] ?? 0, 'search', 'blue'],
    ['Sosyal Medya', $sourceCountByLabel['Sosyal'] ?? 0, 'share-2', 'emerald'],
    ['Doğrudan Trafik', $sourceCountByLabel['Doğrudan'] ?? 0, 'globe-2', 'amber'],
    ['Yönlendirme', $sourceCountByLabel['Yönlendirme'] ?? 0, 'link-2', 'rose']
];
$sourceCardColors = ['blue' => 'bg-blue-50 text-blue-600', 'emerald' => 'bg-emerald-50 text-emerald-600', 'amber' => 'bg-amber-50 text-amber-600', 'rose' => 'bg-rose-50 text-rose-600'];
?>
<?php if (!$website): ?>
    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-6 text-sm text-blue-800">Kaynak analizini görmek için önce bir web sitesi ekleyin.</div>
<?php else: ?>
    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <?php foreach ($sourceCards as $index => $stat): ?>
            <article class="animate-slide-up rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" style="animation-delay:<?= $index * 80 ?>ms">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl <?= $sourceCardColors[$stat[3]] ?>"><i data-lucide="<?= panel_h($stat[2]) ?>" class="h-5 w-5"></i></div>
                <p class="mt-3 text-2xl font-bold text-slate-800"><?= panel_number($stat[1]) ?></p>
                <p class="text-sm text-slate-500"><?= panel_h($stat[0]) ?></p>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-3 xl:gap-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <?php panel_card_header('Kaynak Dağılımı', 'Referrer adreslerinden hesaplanan trafik payı'); ?>
            <?php if (empty($trafficSources)): ?>
                <?php panel_empty('Trafik kaynağı verisi bulunmuyor.'); ?>
            <?php else: ?>
                <div class="flex flex-col items-center gap-5">
                    <?= panel_donut($sourceCategories, 'Kaynak', (string) count(array_filter($sourceCategories, function($item) { return $item['count'] > 0; }))) ?>
                    <div class="w-full space-y-2">
                        <?php foreach ($sourceCategories as $item): ?>
                            <div class="flex items-center justify-between"><div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full" style="background:<?= panel_h($item['color']) ?>"></span><span class="text-sm text-slate-600"><?= panel_h($item['label']) ?></span></div><span class="text-sm font-semibold text-slate-700"><?= panel_percent($item['value']) ?></span></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm xl:col-span-2">
            <?php panel_card_header('Arama Anahtar Kelimeleri', 'Arama motorlarının referrer URL\'lerinden çıkarılan sorgular'); ?>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[480px] text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                            <th class="pb-3 pr-4">Anahtar Kelime</th>
                            <th class="pb-3 text-right">Ziyaret</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($keywords)): ?>
                            <tr><td colspan="2" class="py-8 text-center text-sm text-slate-500">
                                Arama motoru referrer verisi bulunamadı. Ziyaretçiler arama motorlarından geldiğinde burada görünür.
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($keywords as $i => $kw): ?>
                                <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/60 transition-colors">
                                    <td class="py-2.5 pr-4">
                                        <div class="flex items-center gap-2">
                                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-blue-50 text-[10px] font-bold text-blue-500"><?= $i + 1 ?></span>
                                            <span class="font-medium text-slate-700"><?= panel_h($kw['keyword']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 text-right font-semibold text-slate-700"><?= panel_number($kw['visits']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2 xl:gap-6">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <?php panel_card_header('Sosyal Medya Platformları', 'Referrer adresinden tanımlanan sosyal kaynaklardan gelen görüntülemeler'); ?>
            <?php if (empty($socialPlatforms)): ?>
                <?php panel_empty('Sosyal medya referrer kaydı bulunmuyor.'); ?>
            <?php else: ?>
                <div class="space-y-3">
                    <?php
                    $socialMax = max(1, (int) ($socialPlatforms[0]['visits'] ?? 1));
                    foreach ($socialPlatforms as $item):
                        $barWidth = min(100, round($item['visits'] / $socialMax * 100));
                    ?>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-50 p-3 transition-colors hover:bg-slate-50/60">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white" style="background:<?= panel_h($item['color']) ?>">
                                <i data-lucide="<?= panel_h($item['icon']) ?>" class="h-4 w-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="truncate text-sm font-medium text-slate-700"><?= panel_h($item['platform']) ?></span>
                                    <span class="ml-2 shrink-0 text-xs text-slate-400"><?= panel_percent($item['percentage']) ?></span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full transition-all duration-700" style="width:<?= $barWidth ?>%;background:<?= panel_h($item['color']) ?>"></div>
                                </div>
                            </div>
                            <p class="shrink-0 text-sm font-bold text-slate-700"><?= panel_number($item['visits']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <?php panel_card_header('Yönlendiren Siteler', 'Bağlantı veren dış referrer alan adları'); ?>
            <?php if (empty($referralSites)): ?>
                <?php panel_empty('Yönlendiren site kaydı bulunmuyor.'); ?>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($referralSites as $item): ?>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-50 p-3 transition-colors hover:bg-slate-50/60">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600"><i data-lucide="link-2" class="h-4 w-4"></i></div>
                            <span class="min-w-0 flex-1 truncate text-sm font-medium text-slate-700"><?= panel_h($item['domain']) ?></span>
                            <span class="text-sm font-bold text-slate-700"><?= panel_number($item['visits']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
