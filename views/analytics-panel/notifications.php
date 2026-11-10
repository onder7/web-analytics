<?php
require_once __DIR__ . '/helpers.php';
$notificationSettings = $notificationSettings ?? [];
$recipientEmail = $notificationSettings['recipient_email'] ?: ($profile['email'] ?? '');
$smtpPort = $notificationSettings['smtp_port'] ?: 587;
?>
<div class="mx-auto max-w-5xl space-y-6">
    <?php if (!empty($notificationFlash)): ?>
        <div role="<?= $notificationFlash['type'] === 'success' ? 'status' : 'alert' ?>" class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm <?= $notificationFlash['type'] === 'success' ? 'border-emerald-100 bg-emerald-50 text-emerald-800' : 'border-rose-100 bg-rose-50 text-rose-800' ?>">
            <i data-lucide="<?= $notificationFlash['type'] === 'success' ? 'circle-check' : 'circle-alert' ?>" class="mt-0.5 h-4 w-4 shrink-0"></i>
            <span><?= panel_h($notificationFlash['message']) ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[.14em] text-blue-600"><?= panel_h(__('notif.page_label')) ?></p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800"><?= panel_h(__('notif.page_title')) ?></h1>
            <p class="mt-1 text-sm text-slate-500"><?= panel_h(__('notif.page_subtitle')) ?></p>
        </div>
        <a href="/analytics/settings" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 sm:self-auto">
            <i data-lucide="settings" class="h-4 w-4"></i><?= panel_h(__('notif.settings_link')) ?>
        </a>
    </div>

    <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm text-amber-900">
        <i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0"></i>
        <p><strong><?= panel_h(__('notif.info_title')) ?></strong> <?= panel_h(__('notif.info_message')) ?></p>
    </div>

    <form method="post" action="/analytics/notifications" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= panel_h($notificationCsrf) ?>">

        <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-5 lg:px-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i data-lucide="bell" class="h-5 w-5"></i></span>
                    <div><h2 class="text-base font-semibold text-slate-800"><?= panel_h(__('notif.prefs_title')) ?></h2><p class="mt-1 text-sm text-slate-500"><?= panel_h(__('notif.prefs_subtitle')) ?></p></div>
                </div>
            </div>
            <div class="divide-y divide-slate-100 px-5 lg:px-6">
                <label class="flex cursor-pointer items-center justify-between gap-4 py-4">
                    <span><span class="block text-sm font-semibold text-slate-700"><?= panel_h(__('notif.enabled')) ?></span><span class="mt-0.5 block text-xs text-slate-500"><?= panel_h(__('notif.enabled_desc')) ?></span></span>
                    <input type="checkbox" name="enabled" value="1" <?= !empty($notificationSettings['enabled']) ? 'checked' : '' ?> class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                </label>
                <?php foreach ([
                    'daily_summary' => [__('notif.daily'), __('notif.daily_desc')],
                    'weekly_summary' => [__('notif.weekly'), __('notif.weekly_desc')],
                    'monthly_summary' => [__('notif.monthly'), __('notif.monthly_desc')],
                    'traffic_anomaly' => [__('notif.anomaly'), __('notif.anomaly_desc')],
                    'tracking_errors' => [__('notif.errors'), __('notif.errors_desc')]
                ] as $key => $item): ?>
                    <label class="flex cursor-pointer items-center justify-between gap-4 py-4">
                        <span><span class="block text-sm font-medium text-slate-700"><?= panel_h($item[0]) ?></span><span class="mt-0.5 block text-xs text-slate-500"><?= panel_h($item[1]) ?></span></span>
                        <input type="checkbox" name="<?= panel_h($key) ?>" value="1" <?= !empty($notificationSettings[$key]) ? 'checked' : '' ?> class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-4 lg:px-6">
                <label for="recipient_email" class="block text-sm font-medium text-slate-700"><?= panel_h(__('notif.recipient_label')) ?></label>
                <input type="email" id="recipient_email" name="recipient_email" value="<?= panel_h($recipientEmail) ?>" maxlength="191" autocomplete="email" placeholder="<?= panel_h(__('notif.recipient_placeholder')) ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100 sm:max-w-md">
                <p class="mt-1.5 text-xs text-slate-500"><?= panel_h(__('notif.recipient_help')) ?></p>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-5 lg:px-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700"><i data-lucide="mail" class="h-5 w-5"></i></span>
                    <div><h2 class="text-base font-semibold text-slate-800"><?= panel_h(__('notif.smtp_title')) ?></h2><p class="mt-1 text-sm text-slate-500"><?= panel_h(__('notif.smtp_subtitle')) ?></p></div>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:p-6">
                <label class="text-sm font-medium text-slate-700"><?= panel_h(__('notif.smtp_host_label')) ?>
                    <input type="text" name="smtp_host" value="<?= panel_h($notificationSettings['smtp_host'] ?? '') ?>" maxlength="255" autocomplete="url" placeholder="<?= panel_h(__('notif.smtp_host_placeholder')) ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </label>
                <label class="text-sm font-medium text-slate-700"><?= panel_h(__('notif.smtp_port_label')) ?>
                    <input type="number" name="smtp_port" value="<?= (int) $smtpPort ?>" min="1" max="65535" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </label>
                <label class="text-sm font-medium text-slate-700"><?= panel_h(__('notif.smtp_enc_label')) ?>
                    <select name="smtp_encryption" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <?php foreach (['tls' => __('notif.smtp_enc_tls'), 'ssl' => __('notif.smtp_enc_ssl'), 'none' => __('notif.smtp_enc_none')] as $value => $label): ?>
                            <option value="<?= panel_h($value) ?>" <?= ($notificationSettings['smtp_encryption'] ?? 'tls') === $value ? 'selected' : '' ?>><?= panel_h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="text-sm font-medium text-slate-700"><?= panel_h(__('notif.smtp_user_label')) ?>
                    <input type="text" name="smtp_username" value="<?= panel_h($notificationSettings['smtp_username'] ?? '') ?>" maxlength="191" autocomplete="username" placeholder="<?= panel_h(__('notif.smtp_user_placeholder')) ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </label>
                <label class="text-sm font-medium text-slate-700 sm:col-span-2"><?= panel_h(__('notif.smtp_pass_label')) ?>
                    <input type="password" name="smtp_password" autocomplete="new-password" placeholder="<?= !empty($notificationSettings['has_smtp_password']) ? panel_h(__('notif.smtp_pass_keep')) : panel_h(__('notif.smtp_pass_placeholder')) ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <span class="mt-1.5 block text-xs text-slate-500"><?= panel_h(__('notif.smtp_pass_help')) ?></span>
                </label>
                <?php if (!empty($notificationSettings['has_smtp_password'])): ?>
                    <label class="flex items-center gap-2 text-sm text-rose-700 sm:col-span-2">
                        <input type="checkbox" name="clear_smtp_password" value="1" class="h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                        <?= panel_h(__('notif.smtp_pass_clear')) ?>
                    </label>
                <?php endif; ?>
                <div class="sm:col-span-2"><div class="my-1 border-t border-slate-100"></div></div>
                <label class="text-sm font-medium text-slate-700"><?= panel_h(__('notif.from_email_label')) ?>
                    <input type="email" name="from_email" value="<?= panel_h($notificationSettings['from_email'] ?? '') ?>" maxlength="191" autocomplete="email" placeholder="<?= panel_h(__('notif.from_email_placeholder')) ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </label>
                <label class="text-sm font-medium text-slate-700"><?= panel_h(__('notif.from_name_label')) ?>
                    <input type="text" name="from_name" value="<?= panel_h($notificationSettings['from_name'] ?? '') ?>" maxlength="191" autocomplete="organization" placeholder="<?= panel_h(__('notif.from_name_placeholder')) ?>" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </label>
            </div>
            <div class="flex flex-col justify-between gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center lg:px-6">
                <p class="text-xs text-slate-500"><i data-lucide="lock-keyhole" class="mr-1 inline h-3.5 w-3.5"></i><?= panel_h(__('notif.security_note')) ?></p>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
                    <i data-lucide="save" class="h-4 w-4"></i><?= panel_h(__('notif.save_button')) ?>
                </button>
            </div>
        </section>
    </form>
</div>
