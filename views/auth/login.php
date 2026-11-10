<div class="auth-form-card animate-slide-up rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
    <div class="mb-7">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
            <i class="fa-solid <?= !empty($twoFactorChallenge) ? 'fa-shield-halved' : 'fa-right-to-bracket' ?> text-lg"></i>
        </span>
        <p class="mb-1 mt-5 text-xs font-semibold uppercase tracking-[.16em] text-blue-600"><?= htmlspecialchars(!empty($twoFactorChallenge) ? __('auth.two_factor') : __('auth.welcome_back'), ENT_QUOTES, 'UTF-8') ?></p>
        <h2 class="text-2xl font-bold tracking-tight text-slate-800"><?= htmlspecialchars(!empty($twoFactorChallenge) ? __('auth.two_factor_title') : __('auth.login_title'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="mt-1.5 text-sm text-slate-500"><?= htmlspecialchars(!empty($twoFactorChallenge) ? __('auth.two_factor_subtitle') : __('auth.login_subtitle'), ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <?php if (isset($error)): ?>
        <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i><span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div role="status" class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i class="fa-solid fa-circle-check mt-0.5"></i><span><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($twoFactorChallenge)): ?>
    <form method="POST" action="/login/2fa" id="loginForm" class="space-y-5">
        <?php if (!empty($returnTo)): ?><input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
        <label for="two-factor-code" class="block text-sm font-medium text-slate-700">
            <?= htmlspecialchars(__('auth.two_factor_code'), ENT_QUOTES, 'UTF-8') ?>
            <span class="relative mt-1.5 block">
                <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="two-factor-code" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="20" required autofocus placeholder="<?= htmlspecialchars(__('auth.two_factor_placeholder'), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3.5 text-sm tracking-wider text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            </span>
        </label>
        <p class="-mt-2 text-xs leading-5 text-slate-500"><?= htmlspecialchars(__('auth.two_factor_help'), ENT_QUOTES, 'UTF-8') ?></p>
        <button type="submit" id="loginBtn" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-wait disabled:opacity-70">
            <i class="fa-solid fa-shield-halved"></i><span><?= htmlspecialchars(__('auth.two_factor_button'), ENT_QUOTES, 'UTF-8') ?></span>
        </button>
    </form>
    <div class="mt-6 border-t border-slate-100 pt-5 text-center">
        <a href="/login/cancel-2fa" class="text-sm font-semibold text-blue-600 transition hover:text-blue-700"><?= htmlspecialchars(__('auth.two_factor_cancel'), ENT_QUOTES, 'UTF-8') ?></a>
    </div>
    <?php else: ?>
    <form method="POST" action="/login" id="loginForm" class="space-y-5">
        <?php if (!empty($returnTo)): ?><input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
        <label for="email" class="block text-sm font-medium text-slate-700">
            <?= htmlspecialchars(__('auth.email'), ENT_QUOTES, 'UTF-8') ?>
            <span class="relative mt-1.5 block">
                <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars(__('auth.email_placeholder'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required autofocus class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            </span>
        </label>

        <label for="password" class="block text-sm font-medium text-slate-700">
            <?= htmlspecialchars(__('auth.password'), ENT_QUOTES, 'UTF-8') ?>
            <span class="relative mt-1.5 block">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" id="password" name="password" placeholder="<?= htmlspecialchars(__('auth.password_placeholder'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="current-password" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-12 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <button type="button" data-toggle-password="password" class="absolute right-3.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 transition hover:text-slate-600" aria-label="<?= htmlspecialchars(__('auth.show_password'), ENT_QUOTES, 'UTF-8') ?>"><i class="fa-regular fa-eye"></i></button>
            </span>
        </label>

        <button type="submit" id="loginBtn" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-wait disabled:opacity-70">
            <i class="fa-solid fa-right-to-bracket"></i><span><?= htmlspecialchars(__('auth.login_button'), ENT_QUOTES, 'UTF-8') ?></span>
        </button>
    </form>

    <div class="mt-6 border-t border-slate-100 pt-5 text-center">
        <p class="text-sm text-slate-500"><?= htmlspecialchars(__('auth.no_account'), ENT_QUOTES, 'UTF-8') ?>
            <a href="/register" class="font-semibold text-blue-600 transition hover:text-blue-700"><?= htmlspecialchars(__('auth.create_account'), ENT_QUOTES, 'UTF-8') ?></a>
        </p>
    </div>
    <?php endif; ?>

    <!-- Dil değiştirici (auth sayfası için) -->
    <div class="mt-5 flex justify-center gap-3">
        <?php foreach (['tr' => '🇹🇷 Türkçe', 'en' => '🇬🇧 English'] as $code => $label): ?>
            <form method="POST" action="/change-language">
                <input type="hidden" name="locale" value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="text-xs font-medium transition <?= (class_exists('Lang') && Lang::locale() === $code) ? 'text-blue-600 underline' : 'text-slate-400 hover:text-slate-600' ?>">
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </button>
            </form>
        <?php endforeach; ?>
    </div>
</div>
