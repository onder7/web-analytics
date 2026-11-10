<?php
$escape = function($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$locale = Lang::locale();
$text = function($key) {
    return htmlspecialchars(__($key), ENT_QUOTES, 'UTF-8');
};
?>
<!doctype html>
<html lang="<?= $escape($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $text('install.title') ?></title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f1f5f9; color: #172033; font: 15px/1.55 system-ui, -apple-system, "Segoe UI", sans-serif; }
        main { width: min(760px, calc(100% - 32px)); margin: 44px auto; }
        header, section { border: 1px solid #e2e8f0; border-radius: 18px; background: #fff; box-shadow: 0 12px 32px rgba(15, 23, 42, .06); }
        header { padding: 26px 30px; color: #fff; background: linear-gradient(120deg, #0f172a, #164e8a); }
        .header-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; }
        header h1 { margin: 0; font-size: 24px; }
        header p { margin: 7px 0 0; color: #cbd5e1; }
        .languages { display: flex; flex-shrink: 0; gap: 7px; }
        .languages a { border: 1px solid rgba(203, 213, 225, .35); border-radius: 8px; padding: 5px 9px; color: #cbd5e1; font-size: 12px; text-decoration: none; }
        .languages a[aria-current="true"], .languages a:hover { border-color: #93c5fd; color: #fff; background: rgba(59, 130, 246, .2); }
        section { margin-top: 20px; padding: 26px 30px; }
        h2 { margin: 0 0 8px; font-size: 18px; }
        .intro, .help { color: #64748b; font-size: 13px; }
        .intro { margin: 0 0 22px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        label { display: block; color: #334155; font-size: 13px; font-weight: 600; }
        input { display: block; width: 100%; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 11px 12px; color: #0f172a; background: white; font: inherit; }
        input:focus { outline: 3px solid #dbeafe; border-color: #3b82f6; }
        .wide { grid-column: 1 / -1; }
        .divider { margin: 26px 0 20px; border: 0; border-top: 1px solid #e2e8f0; }
        .notice { margin: 0 0 18px; border: 1px solid #fed7aa; border-radius: 10px; padding: 12px 14px; color: #9a3412; background: #fff7ed; }
        .error { border-color: #fecaca; color: #991b1b; background: #fef2f2; }
        .help { margin: 6px 0 0; font-weight: 400; }
        button[type="submit"] { margin-top: 24px; border: 0; border-radius: 10px; padding: 12px 18px; color: white; background: #2563eb; font: inherit; font-weight: 700; cursor: pointer; }
        button[type="submit"]:hover { background: #1d4ed8; }
        footer { padding: 18px 4px; color: #64748b; font-size: 12px; text-align: center; }
        @media (max-width: 600px) { main { margin: 18px auto; } header, section { padding: 21px 18px; } .grid { grid-template-columns: 1fr; } .wide { grid-column: auto; } }
    </style>
</head>
<body>
<main>
    <header>
        <div class="header-row">
            <div>
                <h1><?= $text('install.title') ?></h1>
                <p><?= $text('install.subtitle') ?></p>
            </div>
            <nav class="languages" aria-label="<?= $text('install.language') ?>">
                <?php foreach (['tr' => 'Türkçe', 'en' => 'English'] as $languageCode => $languageLabel): ?>
                    <a href="/install?lang=<?= $escape($languageCode) ?>" lang="<?= $escape($languageCode) ?>" aria-current="<?= $locale === $languageCode ? 'true' : 'false' ?>"><?= $escape($languageLabel) ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>
    <section>
        <h2><?= $text('install.heading') ?></h2>
        <p class="intro"><?= $text('install.empty_database_notice') ?></p>
        <?php if (!empty($error)): ?>
            <p class="notice error" role="alert"><?= $escape($error) ?></p>
        <?php elseif (!empty($status['installed'])): ?>
            <p class="notice"><?= $text('install.already_installed') ?></p>
        <?php else: ?>
            <p class="notice"><?= $text('install.requirements_notice') ?></p>
        <?php endif; ?>
        <?php if (empty($status['installed'])): ?>
            <form method="post" action="/install" autocomplete="on">
                <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                <input type="hidden" name="locale" value="<?= $escape($locale) ?>">
                <h2><?= $text('install.database_heading') ?></h2>
                <div class="grid">
                    <label><?= $text('install.host') ?>
                        <input name="host" value="<?= $escape($values['host']) ?>" required maxlength="253" placeholder="localhost" autocomplete="url">
                    </label>
                    <label><?= $text('install.port') ?>
                        <input name="port" type="number" value="<?= $escape($values['port']) ?>" required min="1" max="65535">
                    </label>
                    <label><?= $text('install.database') ?>
                        <input name="database" value="<?= $escape($values['database']) ?>" required maxlength="64" autocomplete="off">
                    </label>
                    <label><?= $text('install.database_user') ?>
                        <input name="username" value="<?= $escape($values['username']) ?>" required maxlength="128" autocomplete="username">
                    </label>
                    <label class="wide"><?= $text('install.database_password') ?>
                        <input name="password" type="password" autocomplete="new-password">
                    </label>
                </div>
                <hr class="divider">
                <h2><?= $text('install.admin_heading') ?></h2>
                <p class="help"><?= $text('install.password_help') ?></p>
                <div class="grid" style="margin-top:16px">
                    <label><?= $text('install.admin_username') ?>
                        <input name="admin_username" value="<?= $escape($values['admin_username']) ?>" required minlength="3" maxlength="50" autocomplete="username">
                    </label>
                    <label><?= $text('install.admin_email') ?>
                        <input name="admin_email" type="email" value="<?= $escape($values['admin_email']) ?>" required maxlength="191" autocomplete="email">
                    </label>
                    <label><?= $text('install.admin_password') ?>
                        <input name="admin_password" type="password" required minlength="12" maxlength="72" autocomplete="new-password">
                    </label>
                    <label><?= $text('install.confirm_password') ?>
                        <input name="admin_password_confirmation" type="password" required minlength="12" maxlength="72" autocomplete="new-password">
                    </label>
                </div>
                <button type="submit"><?= $text('install.submit') ?></button>
            </form>
        <?php endif; ?>
    </section>
    <footer><?= $text('install.footer') ?></footer>
</main>
</body>
</html>
