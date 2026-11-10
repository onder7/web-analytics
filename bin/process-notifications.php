<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';
require 'models/AppLogger.php';
require 'config/database.php';
require 'models/NotificationSettings.php';
require 'models/SmtpMailer.php';
require 'models/NotificationService.php';

$limit = isset($argv[1]) ? filter_var($argv[1], FILTER_VALIDATE_INT) : 50;
if ($limit === false || $limit < 1 || $limit > 500) {
    fwrite(STDERR, "Kullanım: php bin/process-notifications.php [1-500]\n");
    exit(2);
}

$result = (new NotificationService())->run($limit);
fwrite(
    STDOUT,
    'Kuyruğa alınan bildirim: ' . $result['scheduled'] .
    '; işlenen bildirim: ' . $result['sent'] . "\n"
);
