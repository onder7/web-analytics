<?php
require_once __DIR__ . '/helpers.php';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$analyticsOrigin = $scheme . '://' . $host;
$trackingCode = '';
if ($website) {
    $scriptUrl = json_encode($analyticsOrigin . '/assets/js/tracker.js', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    $apiUrl = json_encode($analyticsOrigin . '/api', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    $apiKey = json_encode((string) $website['api_key'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    $trackingCode = "<script>\n(function () {\n    var script = document.createElement('script');\n    script.src = " . $scriptUrl . ";\n    script.setAttribute('data-api-key', " . $apiKey . ");\n    script.setAttribute('data-api-url', " . $apiUrl . ");\n    script.async = true;\n    document.head.appendChild(script);\n})();\n</script>";
}
$settingsTabs = [
    'profile' => [__('settings.profile'), 'user-round'],
    'tracking' => [__('settings.tracking_code'), 'code-2'],
    'notifications' => [__('settings.notifications'), 'bell'],
    'security' => [__('settings.security'), 'shield']
];
$tab = $profileFlash['tab'] ?? ($_GET['tab'] ?? 'profile');
if (!isset($settingsTabs[$tab])) $tab = 'profile';
?>
<?php if (!empty($profileFlash)): ?>
    <div class="mb-5 rounded-xl border <?= ($profileFlash['type'] ?? '') === 'error' ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' ?> px-4 py-3 text-sm" role="status"><?= panel_h($profileFlash['message'] ?? '') ?></div>
<?php endif; ?>
<section class="grid grid-cols-1 gap-6 lg:grid-cols-4">
    <nav class="h-fit rounded-2xl border border-slate-100 bg-white p-2 shadow-sm" aria-label="<?= panel_h(__('settings.nav_label')) ?>">
        <?php foreach ($settingsTabs as $key => $item): ?>
            <button type="button" data-tab-button="<?= panel_h($key) ?>" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-left text-sm font-medium transition-all <?= $key === $tab ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' ?>">
                <i data-lucide="<?= panel_h($item[1]) ?>" class="h-4 w-4"></i><?= panel_h($item[0]) ?>
            </button>
        <?php endforeach; ?>
    </nav>

    <div class="space-y-6 lg:col-span-3">
        <section data-tab-content="profile" <?= $tab !== 'profile' ? 'hidden' : '' ?> class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
            <?php panel_card_header(__('settings.profile_title'), __('settings.profile_subtitle')); ?>
            <form method="post" action="/analytics/profile" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= panel_h($profileCsrf) ?>">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-xl font-bold uppercase text-white"><?= panel_h(substr((string) ($profile['username'] ?? 'K'), 0, 1)) ?></div>
                    <div><p class="text-sm font-semibold text-slate-700"><?= panel_h($profile['username'] ?? __('settings.username')) ?></p><p class="text-xs text-slate-400"><?= panel_h(__('profile.edit_help')) ?></p></div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <label for="profile-username" class="text-sm font-medium text-slate-600"><?= panel_h(__('settings.username')) ?><input id="profile-username" name="username" type="text" required maxlength="50" autocomplete="username" value="<?= panel_h($profile['username'] ?? '') ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                    <label for="profile-email" class="text-sm font-medium text-slate-600"><?= panel_h(__('settings.email')) ?><input id="profile-email" name="email" type="email" required maxlength="191" autocomplete="email" value="<?= panel_h($profile['email'] ?? '') ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                    <label class="text-sm font-medium text-slate-600"><?= panel_h(__('settings.site_name')) ?><input readonly value="<?= panel_h($website['name'] ?? __('settings.site_not_selected')) ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700"></label>
                    <label class="text-sm font-medium text-slate-600"><?= panel_h(__('settings.domain')) ?><input readonly value="<?= panel_h($website['domain'] ?? '') ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700"></label>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-400"><?= panel_h(__('profile.profile_note')) ?></p>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"><i data-lucide="save" class="h-4 w-4"></i><?= panel_h(__('profile.save_profile')) ?></button>
                </div>
            </form>
        </section>

        <section data-tab-content="tracking" <?= $tab !== 'tracking' ? 'hidden' : '' ?> class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
            <?php panel_card_header(__('settings.tracking_title'), __('settings.tracking_subtitle')); ?>
            <?php if (!$website): ?>
                <?php panel_empty(__('settings.tracking_empty')); ?>
            <?php else: ?>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-100 bg-amber-50/60 px-4 py-3">
                    <div class="flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600"><i data-lucide="code-2" class="h-4 w-4"></i></span><p class="text-sm font-medium text-amber-700"><?= panel_h(__('settings.tracking_install')) ?></p></div>
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700"><?= panel_h(__('settings.tracking_active')) ?></span>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-900">
                    <div class="flex items-center justify-between border-b border-slate-700/50 px-4 py-2.5"><span class="text-xs font-medium text-slate-400">tracking-code.js</span><button type="button" data-copy-target="#tracking-code" class="flex items-center gap-1.5 rounded-lg bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-300 hover:bg-slate-700"><i data-lucide="copy" class="h-3.5 w-3.5"></i><span data-copy-label><?= panel_h(__('settings.tracking_copy')) ?></span></button></div>
                    <pre class="scrollbar-thin overflow-x-auto p-4 text-xs leading-relaxed text-slate-300"><code id="tracking-code"><?= panel_h($trackingCode) ?></code></pre>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-100 p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400"><?= panel_h(__('settings.site_id')) ?></p><p class="mt-1 font-mono text-sm font-medium text-slate-700"><?= (int) $website['id'] ?></p></div>
                    <div class="rounded-xl border border-slate-100 p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400"><?= panel_h(__('settings.domain')) ?></p><p class="mt-1 text-sm font-medium text-slate-700"><?= panel_h($website['domain']) ?></p></div>
                </div>
            <?php endif; ?>
        </section>

        <section data-tab-content="notifications" <?= $tab !== 'notifications' ? 'hidden' : '' ?> class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
            <?php panel_card_header(__('settings.notif_title'), __('settings.notif_subtitle')); ?>
            <a href="/analytics/notifications" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"><i data-lucide="bell" class="h-4 w-4"></i><?= panel_h(__('settings.notif_open')) ?></a>
        </section>

        <section data-tab-content="security" <?= $tab !== 'security' ? 'hidden' : '' ?> class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm lg:p-6">
            <?php panel_card_header(__('settings.security_title'), __('settings.security_subtitle')); ?>
            <?php if (empty($twoFactorAvailable)): ?>
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900" role="status"><?= panel_h(__('profile.2fa_migration_required')) ?></div>
            <?php else: ?>
            <div class="space-y-4">
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i data-lucide="shield-check" class="h-5 w-5"></i></span>
                    <div class="min-w-0 flex-1"><p class="text-sm font-medium text-slate-700"><?= panel_h(__('settings.security_login')) ?></p><p class="text-xs text-slate-400"><?= panel_h(__('settings.security_login_desc')) ?></p></div>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><?= panel_h(__('settings.security_active')) ?></span>
                </div>
                <section class="space-y-4 rounded-xl border border-slate-100 p-4 sm:p-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl <?= !empty($twoFactorStatus['totp_enabled']) ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' ?>"><i data-lucide="smartphone" class="h-5 w-5"></i></span>
                        <div class="min-w-0 flex-1"><p class="text-sm font-medium text-slate-700"><?= panel_h(__('settings.security_2fa')) ?></p><p class="text-xs text-slate-400"><?= panel_h(!empty($twoFactorStatus['totp_enabled']) ? __('profile.2fa_active_desc') : __('profile.2fa_inactive_desc')) ?></p></div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= !empty($twoFactorStatus['totp_enabled']) ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>"><?= panel_h(!empty($twoFactorStatus['totp_enabled']) ? __('settings.security_active') : __('profile.2fa_inactive')) ?></span>
                    </div>

                    <?php if (!empty($twoFactorRecoveryCodes)): ?>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <h4 class="text-sm font-semibold text-amber-900"><?= panel_h(__('profile.2fa_recovery_title')) ?></h4>
                            <p class="mt-1 text-xs leading-5 text-amber-800"><?= panel_h(__('profile.2fa_recovery_help')) ?></p>
                            <ul class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                <?php foreach ($twoFactorRecoveryCodes as $recoveryCode): ?>
                                    <li class="rounded-lg border border-amber-200 bg-white px-2 py-2 text-center font-mono text-xs font-bold tracking-wider text-slate-800"><?= panel_h($recoveryCode) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($twoFactorStatus['totp_enabled'])): ?>
                        <form method="post" action="/analytics/security/2fa/disable" class="space-y-4 border-t border-slate-100 pt-4">
                            <input type="hidden" name="csrf_token" value="<?= panel_h($profileCsrf) ?>">
                            <p class="text-xs text-slate-500"><?= panel_h(__('profile.2fa_disable_help')) ?></p>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <label for="two-factor-disable-password" class="text-sm font-medium text-slate-600"><?= panel_h(__('profile.current_password')) ?><input id="two-factor-disable-password" name="current_password" type="password" required autocomplete="current-password" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                                <label for="two-factor-disable-code" class="text-sm font-medium text-slate-600"><?= panel_h(__('profile.2fa_code')) ?><input id="two-factor-disable-code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="20" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm tracking-wider text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                            </div>
                            <div class="flex justify-end"><button type="submit" class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50"><?= panel_h(__('profile.2fa_disable')) ?></button></div>
                        </form>
                    <?php elseif (!empty($twoFactorSetup)): ?>
                        <div class="space-y-4 border-t border-slate-100 pt-4">
                            <p class="text-sm font-semibold text-slate-700"><?= panel_h(__('profile.2fa_setup_title')) ?></p>
                            <p class="text-xs leading-5 text-slate-500"><?= panel_h(__('profile.2fa_setup_help')) ?></p>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-medium text-slate-500"><?= panel_h(__('profile.2fa_setup_account')) ?>: <span class="font-semibold text-slate-700"><?= panel_h($profile['email'] ?? '') ?></span></p>
                                <div class="my-4 flex flex-col items-center gap-2 rounded-xl border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-medium text-slate-600"><?= panel_h(__('profile.2fa_scan_qr')) ?></p>
                                    <div id="two-factor-qr" class="h-56 w-56 max-w-full [&>svg]:h-full [&>svg]:w-full" data-qr-value="<?= panel_h((new TotpAuthenticator())->provisioningUri($twoFactorSetup, $profile['email'] ?? '')) ?>" data-qr-alt="<?= panel_h(__('profile.2fa_qr_alt')) ?>"></div>
                                    <p data-qr-error hidden role="status" class="text-xs text-amber-700"><?= panel_h(__('profile.2fa_qr_failed')) ?></p>
                                </div>
                                <p class="mt-2 text-xs font-medium text-slate-500"><?= panel_h(__('profile.2fa_setup_key')) ?></p>
                                <code class="mt-1 block select-all break-all rounded-lg border border-slate-200 bg-white p-3 text-sm font-bold tracking-[.18em] text-slate-800"><?= panel_h(implode(' ', str_split($twoFactorSetup, 4))) ?></code>
                                <p class="mt-2 break-all text-[11px] leading-5 text-slate-400"><?= panel_h(__('profile.2fa_setup_uri')) ?>: <code><?= panel_h((new TotpAuthenticator())->provisioningUri($twoFactorSetup, $profile['email'] ?? '')) ?></code></p>
                            </div>
                            <form method="post" action="/analytics/security/2fa/confirm" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                <input type="hidden" name="csrf_token" value="<?= panel_h($profileCsrf) ?>">
                                <label for="two-factor-setup-code" class="flex-1 text-sm font-medium text-slate-600"><?= panel_h(__('profile.2fa_code')) ?><input id="two-factor-setup-code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]{6,8}" maxlength="8" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm tracking-wider text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"><?= panel_h(__('profile.2fa_confirm')) ?></button>
                            </form>
                            <form method="post" action="/analytics/security/2fa/cancel">
                                <input type="hidden" name="csrf_token" value="<?= panel_h($profileCsrf) ?>">
                                <button type="submit" class="text-sm font-medium text-slate-500 transition hover:text-slate-700"><?= panel_h(__('profile.2fa_setup_cancel')) ?></button>
                            </form>
                            <script src="/assets/js/vendor/qrcode-generator-2.0.4.js"></script>
                            <script>
                                (function () {
                                    var container = document.getElementById('two-factor-qr');
                                    if (!container) return;

                                    try {
                                        if (typeof qrcode !== 'function') throw new Error('QR generator unavailable');
                                        var qr = qrcode(0, 'M');
                                        qr.addData(container.getAttribute('data-qr-value'), 'Byte');
                                        qr.make();
                                        container.innerHTML = qr.createSvgTag({
                                            cellSize: 5,
                                            margin: 4,
                                            scalable: true,
                                            alt: { text: container.getAttribute('data-qr-alt') }
                                        });
                                    } catch (error) {
                                        var message = container.parentNode.querySelector('[data-qr-error]');
                                        if (message) message.hidden = false;
                                    }
                                })();
                            </script>
                        </div>
                    <?php else: ?>
                        <form method="post" action="/analytics/security/2fa/setup" class="space-y-4 border-t border-slate-100 pt-4">
                            <input type="hidden" name="csrf_token" value="<?= panel_h($profileCsrf) ?>">
                            <p class="text-xs leading-5 text-slate-500"><?= panel_h(__('profile.2fa_enable_help')) ?></p>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                <label for="two-factor-setup-password" class="flex-1 text-sm font-medium text-slate-600"><?= panel_h(__('profile.current_password')) ?><input id="two-factor-setup-password" name="current_password" type="password" required autocomplete="current-password" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"><?= panel_h(__('profile.2fa_enable')) ?></button>
                            </div>
                        </form>
                    <?php endif; ?>
                </section>
                <form method="post" action="/analytics/profile/password" class="space-y-4 rounded-xl border border-slate-100 p-4 sm:p-5">
                    <input type="hidden" name="csrf_token" value="<?= panel_h($profileCsrf) ?>">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800"><?= panel_h(__('profile.change_password')) ?></h3>
                        <p class="mt-1 text-xs text-slate-500"><?= panel_h(__('profile.password_help')) ?></p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <label for="current-password" class="text-sm font-medium text-slate-600 sm:col-span-2"><?= panel_h(__('profile.current_password')) ?><input id="current-password" name="current_password" type="password" required autocomplete="current-password" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                        <label for="new-password" class="text-sm font-medium text-slate-600"><?= panel_h(__('profile.new_password')) ?><input id="new-password" name="new_password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                        <label for="confirm-password" class="text-sm font-medium text-slate-600"><?= panel_h(__('profile.confirm_password')) ?><input id="confirm-password" name="confirm_password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"></label>
                    </div>
                    <div class="flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"><i data-lucide="lock-keyhole" class="h-4 w-4"></i><?= panel_h(__('profile.update_password')) ?></button>
                    </div>
                </form>
                <a href="/logout" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50"><i data-lucide="log-out" class="h-4 w-4"></i><?= panel_h(__('settings.logout')) ?></a>
            </div>
        </section>
    </div>
    <?php endif; ?>
</section>
