<?php

require_once __DIR__ . '/services/helper.php';
require_once __DIR__ . '/services/lock.php';
require_once __DIR__ . '/services/daemon.php';
require_once __DIR__ . '/errorHandler.php';
require_once __DIR__ . '/services/mailer.php';
require_once __DIR__ . '/database/pdo.php';
$config = require __DIR__ . '/config.php';

// try set lock to check that process is not running
if (!$lock = doLock($config['lockFile'])) {
    logErrorMessage('Already running');
    exit;
}

$users = getUsersForEmailNotification();

run(100, function ($user, $pdo) {
    processUser($user, $pdo);
}, $users);

// remove lock
releaseLock($lock);