<?php
// Lang::get() kısayolunun view'larda hazır olduğunu garanti et
if (!function_exists('__') && class_exists('Lang')) {
    function __(string $key, array $params = []): string {
        return Lang::get($key, $params);
    }
}

if (!function_exists('panel_h')) {
    function panel_h($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    function panel_number($value) {
        return number_format((float) $value, 0, ',', '.');
    }

    function panel_percent($value) {
        return number_format((float) $value, 1, ',', '.') . '%';
    }

    function panel_datetime($value, $timezone = 'UTC', $format = 'd.m.Y H:i') {
        if (!is_string($value) || $value === '') {
            return '';
        }
        $date = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        return $date->setTimezone(new DateTimeZone($timezone))->format($format);
    }

    function panel_json($value) {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    }

    function panel_path($url) {
        $path = parse_url((string) $url, PHP_URL_PATH);
        return $path ?: '/';
    }

    function panel_duration($seconds) {
        $seconds = max(0, (int) round((float) $seconds));
        if ($seconds >= 3600) {
            return sprintf('%d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
        }
        return sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
    }

    function panel_donut($items, $centerLabel, $centerValue) {
        $size = 140;
        $stroke = 20;
        $radius = ($size - $stroke) / 2;
        $circumference = 2 * M_PI * $radius;
        $offset = 0;
        ob_start();
        ?>
        <div class="relative flex h-[140px] w-[140px] shrink-0 items-center justify-center">
            <svg width="<?= $size ?>" height="<?= $size ?>" viewBox="0 0 <?= $size ?> <?= $size ?>" class="-rotate-90">
                <circle cx="70" cy="70" r="<?= $radius ?>" fill="none" stroke="#f1f5f9" stroke-width="<?= $stroke ?>"></circle>
                <?php foreach ($items as $item): ?>
                    <?php
                    $value = max(0, min(100, (float) ($item['value'] ?? 0)));
                    $dash = $value / 100 * $circumference;
                    $color = $item['color'] ?? '#94a3b8';
                    ?>
                    <circle cx="70" cy="70" r="<?= $radius ?>" fill="none" stroke="<?= panel_h($color) ?>" stroke-width="<?= $stroke ?>"
                            stroke-dasharray="<?= $dash ?> <?= $circumference - $dash ?>"
                            stroke-dashoffset="<?= -$offset ?>" stroke-linecap="round"
                            style="transition:stroke-dashoffset .8s ease-out"></circle>
                    <?php $offset += $dash; ?>
                <?php endforeach; ?>
            </svg>
            <div class="absolute flex flex-col items-center">
                <span class="text-xl font-bold text-slate-800"><?= panel_h($centerValue) ?></span>
                <span class="text-[10px] font-medium uppercase tracking-wide text-slate-400"><?= panel_h($centerLabel) ?></span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    function panel_card_header($title, $subtitle = '') {
        ?>
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-800"><?= panel_h($title) ?></h2>
            <?php if ($subtitle !== ''): ?><p class="mt-0.5 text-xs text-slate-500"><?= panel_h($subtitle) ?></p><?php endif; ?>
        </div>
        <?php
    }

    function panel_empty($message, $colspan = null) {
        if ($colspan) {
            echo '<tr><td colspan="' . (int) $colspan . '" class="py-8 text-center text-sm text-slate-500">' . panel_h($message) . '</td></tr>';
            return;
        }
        echo '<div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-8 text-center text-sm text-slate-500">' . panel_h($message) . '</div>';
    }
}
