<?php require_once __DIR__ . '/../analytics-panel/helpers.php'; ?>
<div class="mx-auto w-full max-w-3xl">
    <div class="mb-6">
        <a href="/websites" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-blue-600"><i data-lucide="arrow-left" class="h-4 w-4"></i> Web Sitelerine Dön</a>
        <p class="mb-1 mt-5 text-xs font-semibold uppercase tracking-[.16em] text-blue-600">Site yönetimi</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">Yeni Site Ekle</h1>
        <p class="mt-1 text-sm text-slate-500">Web sitenizi ekleyerek ziyaretçi trafiğini ölçmeye başlayın.</p>
    </div>

    <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-6 flex items-center gap-3 border-b border-slate-100 pb-5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i data-lucide="globe-2" class="h-5 w-5"></i></div>
            <div><h2 class="text-base font-semibold text-slate-800">Web sitesi bilgileri</h2><p class="text-xs text-slate-500">İsim ve domain bilgilerini girin.</p></div>
        </div>

        <?php if (isset($error)): ?>
            <div role="alert" class="mb-5 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800"><i data-lucide="triangle-alert" class="mr-2 inline h-4 w-4"></i><?= panel_h($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div role="alert" class="mb-5 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-inside list-disc space-y-1"><?php foreach ($errors as $error): ?><li><?= panel_h($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/websites/create" class="space-y-5">
            <label for="name" class="block text-sm font-medium text-slate-700">Site Adı
                <input type="text" id="name" name="name" placeholder="Örn. Şirket Web Sitesi" value="<?= panel_h($name ?? '') ?>" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <span class="mt-1.5 block text-xs font-normal text-slate-400">Panelde sitenizi tanımlamak için kullanılır.</span>
            </label>
            <label for="domain" class="block text-sm font-medium text-slate-700">Domain
                <span class="relative mt-1.5 block">
                    <i data-lucide="link-2" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="domain" name="domain" placeholder="example.com" value="<?= panel_h($domain ?? '') ?>" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                </span>
                <span class="mt-1.5 block text-xs font-normal text-slate-400">Protokol olmadan yazın; ör. example.com</span>
            </label>

            <div class="flex flex-col-reverse justify-between gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                <a href="/websites" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">İptal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"><i data-lucide="plus" class="h-4 w-4"></i> Siteyi Ekle</button>
            </div>
        </form>
    </section>

    <div class="mt-4 flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/60 p-4 text-sm text-blue-800">
        <i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0"></i>
        <p>Siteyi ekledikten sonra düzenleme sayfasından izleme kodunuzu kopyalayıp sitenize ekleyebilirsiniz.</p>
    </div>
</div>
