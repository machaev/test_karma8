<?php

$pdo = require_once 'pdo.php';

/**
 * @var PDO $pdo
 */

// remove all tables
$dbName = $pdo->query('SELECT database()')->fetchColumn();
$sth = $pdo->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = :dbName");
$sth->execute(['dbName' => $dbName]);
$tables = $sth->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    $pdo->exec("DROP TABLE IF EXISTS $table");
}
echo 'All tables removed' . PHP_EOL;

// create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    validts TIMESTAMP,
    confirmed BOOLEAN NOT NULL,
    checked BOOLEAN NOT NULL,
    valid BOOLEAN NOT NULL,
    UNIQUE KEY (username),
    INDEX (valid DESC, validts ASC),
    INDEX (confirmed DESC)
)";
if ($pdo->exec($sql) === false) {
    echo 'Error while creating users table' . PHP_EOL;
    exit;
}
echo 'Users table created' . PHP_EOL;