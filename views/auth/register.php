<div class="auth-form-card animate-slide-up rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
    <div class="mb-6">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
            <i class="fa-solid fa-user-plus text-lg"></i>
        </span>
        <p class="mb-1 mt-5 text-xs font-semibold uppercase tracking-[.16em] text-blue-600">Aramıza katılın</p>
        <h2 class="text-2xl font-bold tracking-tight text-slate-800">Hesap oluşturun</h2>
        <p class="mt-1.5 text-sm text-slate-500">Web sitelerinizin performansını tek panelden takip edin.</p>
    </div>

    <?php if (isset($error)): ?>
        <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i><span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <ul class="list-inside list-disc space-y-1"><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register" id="registerForm" class="space-y-4">
        <label for="username" class="block text-sm font-medium text-slate-700">
            Kullanıcı adı
            <span class="relative mt-1.5 block">
                <i class="fa-regular fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Kullanıcı adınız" autocomplete="username" required autofocus class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            </span>
        </label>

        <label for="email" class="block text-sm font-medium text-slate-700">
            E-posta adresi
            <span class="relative mt-1.5 block">
                <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ornek@email.com" autocomplete="email" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
            </span>
        </label>

        <label for="password" class="block text-sm font-medium text-slate-700">
            Şifre
            <span class="relative mt-1.5 block">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" id="password" name="password" placeholder="En az 6 karakter" autocomplete="new-password" minlength="6" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-12 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <button type="button" data-toggle-password="password" class="absolute right-3.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 transition hover:text-slate-600" aria-label="Şifreyi göster"><i class="fa-regular fa-eye"></i></button>
            </span>
        </label>
        <div class="-mt-2">
            <div class="h-1 overflow-hidden rounded-full bg-slate-100"><div id="strengthFill" class="h-full w-0 rounded-full bg-slate-300 transition-all duration-300"></div></div>
            <p id="strengthText" class="mt-1.5 text-xs text-slate-400">Şifreniz en az 6 karakter olmalıdır.</p>
        </div>

        <label for="confirm_password" class="block text-sm font-medium text-slate-700">
            Şifre tekrarı
            <span class="relative mt-1.5 block">
                <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Şifrenizi tekrar girin" autocomplete="new-password" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-12 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100">
                <button type="button" data-toggle-password="confirm_password" class="absolute right-3.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 transition hover:text-slate-600" aria-label="Şifreyi göster"><i class="fa-regular fa-eye"></i></button>
            </span>
        </label>
        <p id="passwordMatch" class="-mt-2 text-xs text-slate-400">Şifreleri aynı girdiğinizden emin olun.</p>

        <button type="submit" id="registerBtn" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-100 disabled:cursor-wait disabled:opacity-70">
            <i class="fa-solid fa-user-plus"></i><span>Hesap Oluştur</span>
        </button>
    </form>

    <div class="mt-5 border-t border-slate-100 pt-4 text-center">
        <p class="text-sm text-slate-500">Zaten hesabınız var mı?
            <a href="/login" class="font-semibold text-blue-600 transition hover:text-blue-700">Giriş yapın</a>
        </p>
    </div>
</div>
