<?php

require_once 'pdo.php';
$pdo = getDbConnection();

$validts = array_fill(0, 100, null);
for ($i = 0; $i < 20; $i++) {
    $validts[$i] = date('Y-m-d H:i:s', mt_rand(time() + 259201, time() + 2592000));
}

$confirmed = array_merge(
    array_fill(0, 80, 0),
    array_fill(0, 20, 1)
);

$sqlUsers = "INSERT INTO users
        (username, email, validts, confirmed, checked, valid)
    VALUES ";

$batch = 10000;
$totalCreated = 0;

for ($i = 1; $i <= 5000000; $i = $i + $batch) {
    $valuesUsers = [];
    for ($j = $i; $j < $i + $batch; $j++) {
        $currentValidts = $validts[mt_rand(0, 99)];
        $currentValidts = is_null($currentValidts) ? 'NULL' : "'$currentValidts'";
        $currentConfirmed = $confirmed[mt_rand(0, 99)];
        $valuesUsers[] = "('username_$j', 'username_$j@gmail.com', $currentValidts, $currentConfirmed, 0, 0)";
    }

    if ($pdo->exec($sqlUsers . implode(', ', $valuesUsers) . ';') === false) {
        echo 'Error while creating users' . PHP_EOL;
        exit;
    }

    $totalCreated += $batch;
    echo "Created $totalCreated users" . PHP_EOL;
}
echo 'Users inserted' . PHP_EOL;