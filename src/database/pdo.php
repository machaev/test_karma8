<?php

$config = require __DIR__ . '/../config.php';
$dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['db_name']};charset=UTF8";

try {
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password']);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    //TODO: remove this code
    if ($pdo) {
        echo "Connected to the {$config['db']['db_name']} database successfully!" . PHP_EOL;
        return $pdo;
    } else {
        echo "Error while connect to the {$config['db']['db_name']} database " . PHP_EOL;
        exit;
    }
} catch (PDOException $e) {
    var_dump($e->getTraceAsString());
    echo $e->getMessage() . PHP_EOL;
    exit;
}