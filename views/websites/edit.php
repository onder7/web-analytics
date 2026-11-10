<?php
require_once __DIR__ . '/../analytics-panel/helpers.php';
$analyticsScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$analyticsOrigin = $analyticsScheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$integrationScriptUrl = json_encode($analyticsOrigin . '/assets/js/tracker.js', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
$integrationApiUrl = json_encode($analyticsOrigin . '/api', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
$integrationApiKey = json_encode((string) $website['api_key'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
?>
<div class="mx-auto w-full max-w-4xl">
    <div class="mb-6">
        <a href="/websites" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-blue-600"><i data-lucide="arrow-left" class="h-4 w-4"></i> Web Sitelerine Dön</a>
        <p class="mb-1 mt-5 text-xs font-semibold uppercase tracking-[.16em] text-blue-600">Site yönetimi</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">Siteyi Düzenle</h1>
        <p class="mt-1 text-sm text-slate-500"><?= panel_h($website['domain']) ?> bilgilerini ve izleme kodunu yönetin.</p>
    </div>

    <?php if (isset($error)): ?>
        <div role="alert" class="mb-5 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800"><i data-lucide="triangle-alert" class="mr-2 inline h-4 w-4"></i><?= panel_h($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div role="alert" class="mb-5 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800"><ul class="list-inside list-disc space-y-1"><?php foreach ($errors as $error): ?><li><?= panel_h($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?><div role="alert" class="mb-5 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= panel_h($_GET['error']) ?></div><?php endif; ?>
    <?php if (!empty($websiteFlash)): ?><div role="status" class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?= panel_h($websiteFlash) ?></div><?php endif; ?>
    <?php if (!empty($invitationLink)): ?>
        <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
            <p class="font-semibold">Davet bağlantısı oluşturuldu. Bu bağlantı 7 gün geçerlidir ve yalnızca davet edilen hesapla kabul edilebilir.</p>
            <div class="mt-2 flex gap-2"><input readonly value="<?= panel_h($invitationLink) ?>" class="min-w-0 flex-1 rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs"><button type="button" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white" data-copy-text="<?= panel_h($invitationLink) ?>"><span data-copy-label>Kopyala</span></button></div>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-6 flex items-center gap-3 border-b border-slate-100 pb-5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i data-lucide="settings-2" class="h-5 w-5"></i></div>
            <div><h2 class="text-base font-semibold text-slate-800">Site bilgileri</h2><p class="text-xs text-slate-500">Web sitesi adını ve domainini güncelleyin.</p></div>
        </div>

        <form method="POST" action="/websites/<?= (int) $website['id'] ?>/edit" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= panel_h($websiteCsrf ?? '') ?>">
            <label for="name" class="block text-sm font-medium text-slate-700">Site Adı
                <input type="text" id="name" name="name" value="<?= panel_h($website['name']) ?>" required <?= in_array($website['access_role'] ?? '', ['owner', 'write'], true) ? '' : 'readonly' ?> class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-sm text-slate-700 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>
            <label for="domain" class="block text-sm font-medium text-slate-700">Domain
                <input type="text" id="domain" name="domain" value="<?= panel_h($website['domain']) ?>" required <?= in_array($website['access_role'] ?? '', ['owner', 'write'], true) ? '' : 'readonly' ?> class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-sm text-slate-700 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>
            <label for="timezone" class="block text-sm font-medium text-slate-700">Site saat dilimi
                <select id="timezone" name="timezone" <?= in_array($website['access_role'] ?? '', ['owner', 'write'], true) ? '' : 'disabled' ?> class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-sm text-slate-700">
                    <?php foreach (array_unique(array_merge(['UTC'], $timezones ?? [])) as $timezone): ?>
                        <option value="<?= panel_h($timezone) ?>" <?= ($website['timezone'] ?? 'UTC') === $timezone ? 'selected' : '' ?>><?= panel_h($timezone) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="mt-1 block text-xs font-normal text-slate-400">Günlük sınırlar ve saatlik grafikler bu saat dilimini kullanır. Eski kayıtların tarihleri geriye dönük dönüştürülmez.</span>
            </label>

            <div class="grid grid-cols-1 gap-4 border-t border-slate-100 pt-5 sm:grid-cols-2">
                <?php if (($website['access_role'] ?? '') === 'owner'): ?>
                    <div class="min-w-0 rounded-xl border border-slate-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">API Anahtarı</p>
                        <div class="mt-2 flex items-center gap-2">
                            <code id="website-api-key" class="min-w-0 flex-1 truncate rounded-lg bg-slate-50 px-3 py-2 font-mono text-xs text-slate-700"><?= panel_h($website['api_key']) ?></code>
                            <button type="button" data-copy-target="#website-api-key" class="flex shrink-0 items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"><i data-lucide="copy" class="h-3.5 w-3.5"></i><span data-copy-label>Kopyala</span></button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="min-w-0 rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">API Anahtarı</p>
                        <p class="mt-2 text-sm text-slate-500">Güvenlik nedeniyle ekip üyelerine gösterilmez.</p>
                    </div>
                <?php endif; ?>
                <div class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Eklenme Tarihi</p>
                    <p class="mt-2 flex items-center gap-2 text-sm font-medium text-slate-700"><i data-lucide="calendar-days" class="h-4 w-4 text-slate-400"></i><?= panel_h(panel_datetime($website['created_at'], $website['timezone'] ?? 'UTC')) ?></p>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-between gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                <a href="/websites" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">İptal</a>
                <?php if (in_array($website['access_role'] ?? '', ['owner', 'write'], true)): ?>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"><i data-lucide="save" class="h-4 w-4"></i> Değişiklikleri Kaydet</button>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <?php if (($website['access_role'] ?? '') === 'owner'): ?>
    <section class="mt-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4 flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i data-lucide="code-2" class="h-5 w-5"></i></div>
            <div><h2 class="text-base font-semibold text-slate-800">İzleme Kodu</h2><p class="mt-0.5 text-sm text-slate-500">Kodu sitenizin tüm sayfalarında <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-600">&lt;/body&gt;</code> etiketinden önce ekleyin.</p></div>
        </div>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-700/50 px-4 py-2.5"><span class="text-xs font-medium text-slate-400">tracking-code.js</span><button type="button" data-copy-target="#integrationCode" class="flex items-center gap-1.5 rounded-lg bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-300 transition hover:bg-slate-700"><i data-lucide="copy" class="h-3.5 w-3.5"></i><span data-copy-label>Kopyala</span></button></div>
            <pre class="scrollbar-thin overflow-x-auto p-4 text-xs leading-relaxed text-slate-300"><code id="integrationCode">&lt;script&gt;
(function () {
    var script = document.createElement('script');
    script.src = <?= htmlspecialchars($integrationScriptUrl, ENT_QUOTES, 'UTF-8') ?>;
    script.setAttribute('data-api-key', <?= htmlspecialchars($integrationApiKey, ENT_QUOTES, 'UTF-8') ?>);
    script.setAttribute('data-api-url', <?= htmlspecialchars($integrationApiUrl, ENT_QUOTES, 'UTF-8') ?>);
    script.async = true;
    document.head.appendChild(script);
})();
&lt;/script&gt;</code></pre>
        </div>
    </section>

        <section class="mt-5 rounded-2xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm sm:p-6">
            <h2 class="text-base font-semibold text-amber-900">API anahtarını yenile</h2>
            <p class="mt-1 text-sm text-amber-800">Eski anahtar hemen geçersiz olur ve mevcut izleme kodlarını değiştirmeniz gerekir.</p>
            <form method="POST" action="/websites/<?= (int) $website['id'] ?>/rotate-key" class="mt-4">
                <input type="hidden" name="csrf_token" value="<?= panel_h($websiteCsrf ?? '') ?>">
                <button type="submit" onclick="return confirm('Eski API anahtarını hemen geçersiz kılmak istiyor musunuz?')" class="rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-sm font-semibold text-amber-800 hover:bg-amber-100">Yeni API anahtarı oluştur</button>
            </form>
        </section>
        <section class="mt-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-base font-semibold text-slate-800">Takım erişimi</h2>
            <p class="mt-1 text-sm text-slate-500">Davet bağlantısı yalnızca davet edilen mevcut hesap tarafından kabul edilebilir; 7 gün sonra sona erer.</p>
            <form method="POST" action="/websites/<?= (int) $website['id'] ?>/invitations" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto_auto]">
                <input type="hidden" name="csrf_token" value="<?= panel_h($websiteCsrf ?? '') ?>">
                <label class="sr-only" for="team-email">Davet edilecek kullanıcının e-postası</label>
                <input id="team-email" type="email" name="email" required placeholder="kullanici@example.com" class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm">
                <label class="sr-only" for="team-role">Yetki</label>
                <select id="team-role" name="role" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"><option value="read">Salt okunur</option><option value="write">Okuma ve düzenleme</option></select>
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Davet bağlantısı oluştur</button>
            </form>
            <?php if (!empty($team['members'])): ?>
                <h3 class="mt-6 text-sm font-semibold text-slate-700">Ekip üyeleri</h3>
                <div class="mt-2 divide-y divide-slate-100">
                    <?php foreach ($team['members'] as $member): ?>
                        <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                            <p class="text-sm text-slate-700"><?= panel_h($member['email']) ?> <span class="text-slate-400">(<?= $member['role'] === 'write' ? 'Okuma ve düzenleme' : 'Salt okunur' ?>)</span></p>
                            <form method="POST" action="/websites/<?= (int) $website['id'] ?>/members/<?= (int) $member['user_id'] ?>/delete">
                                <input type="hidden" name="csrf_token" value="<?= panel_h($websiteCsrf ?? '') ?>">
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Erişimi kaldır</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($team['invitations'])): ?>
                <h3 class="mt-6 text-sm font-semibold text-slate-700">Bekleyen davetler</h3>
                <div class="mt-2 divide-y divide-slate-100">
                    <?php foreach ($team['invitations'] as $invitation): ?>
                        <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                            <p class="text-sm text-slate-700"><?= panel_h($invitation['email']) ?> <span class="text-slate-400">· <?= panel_h(date('d.m.Y', strtotime($invitation['expires_at']))) ?> tarihinde sona erer</span></p>
                            <form method="POST" action="/websites/<?= (int) $website['id'] ?>/invitations/<?= (int) $invitation['id'] ?>/delete">
                                <input type="hidden" name="csrf_token" value="<?= panel_h($websiteCsrf ?? '') ?>">
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">İptal et</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>
