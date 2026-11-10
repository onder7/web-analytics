<?php require_once __DIR__ . '/../analytics-panel/helpers.php'; ?>
<div class="mx-auto w-full max-w-3xl">
    <div class="mb-6">
        <a href="/websites" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-blue-600">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Web Sitelerine Dön
        </a>
        <p class="mb-1 mt-5 text-xs font-semibold uppercase tracking-[.16em] text-blue-600">Takım erişimi</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">Site davetini kabul et</h1>
        <p class="mt-1 text-sm text-slate-500">Davet ayrıntılarını inceleyip erişimi hesabınıza ekleyin.</p>
    </div>

    <?php if (!empty($_GET['error'])): ?>
        <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i data-lucide="triangle-alert" class="mt-0.5 h-4 w-4 shrink-0"></i>
            <span><?= panel_h($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-cyan-900 px-6 py-7 text-white sm:px-8 sm:py-9">
            <div class="pointer-events-none absolute -right-10 -top-20 h-56 w-56 rounded-full bg-cyan-400/15 blur-3xl"></div>
            <div class="relative flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 shadow-lg backdrop-blur">
                    <i data-lucide="globe-2" class="h-7 w-7 text-cyan-200"></i>
                </div>
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/20 bg-emerald-300/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-200">
                        <i data-lucide="mail-check" class="h-3.5 w-3.5"></i> Size özel davet
                    </span>
                    <h2 class="mt-3 break-words text-xl font-bold tracking-tight sm:text-2xl"><?= panel_h($invitation['website_name']) ?></h2>
                    <p class="mt-1 flex items-center gap-2 break-all text-sm text-blue-100/80">
                        <i data-lucide="link-2" class="h-4 w-4 shrink-0"></i><?= panel_h($invitation['domain']) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="p-5 sm:p-8">
            <p class="max-w-2xl text-sm leading-6 text-slate-600">
                Bu siteye ekip üyesi olarak davet edildiniz. Daveti kabul ettiğinizde aşağıdaki izinler hesabınıza tanımlanır.
            </p>

            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <i data-lucide="shield-check" class="h-4 w-4"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Erişim düzeyi</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800"><?= $invitation['role'] === 'write' ? 'Okuma ve düzenleme' : 'Salt okunur' ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-700">
                        <i data-lucide="user-round" class="h-4 w-4"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Davet edilen hesap</p>
                        <p class="mt-1 break-all text-sm font-semibold text-slate-800"><?= panel_h($invitation['email']) ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 rounded-xl border border-amber-100 bg-amber-50/70 px-4 py-3 text-xs text-amber-800">
                <i data-lucide="clock-3" class="h-4 w-4 shrink-0"></i>
                <span>Bu davet <?= panel_h(panel_datetime($invitation['expires_at'], 'UTC', 'd.m.Y H:i')) ?> tarihinde sona erer (UTC).</span>
            </div>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <a href="/websites" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Şimdi değil
                </a>
                <form method="POST" action="/websites/invitations/<?= rawurlencode($token) ?>">
                    <input type="hidden" name="csrf_token" value="<?= panel_h($csrfToken) ?>">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-600/20 transition hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 sm:w-auto">
                        <i data-lucide="check" class="h-4 w-4"></i> Daveti kabul et
                    </button>
                </form>
            </div>
        </div>
    </section>

    <p class="mt-4 flex items-start gap-2 px-1 text-xs leading-5 text-slate-400">
        <i data-lucide="info" class="mt-0.5 h-3.5 w-3.5 shrink-0"></i>
        Davet yalnızca belirtilen e-posta adresiyle giriş yapılmış hesap tarafından kabul edilebilir.
    </p>
</div>
