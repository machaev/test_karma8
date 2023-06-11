<?php

function getDbConnection(): PDO
{
    $config = require __DIR__ . '/../config.php';
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['db_name']};charset=UTF8";

    try {
        $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password']);
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    } catch (PDOException $e) {
        echo $e->getMessage() . PHP_EOL;
        exit;
    }

    return $pdo;
}
