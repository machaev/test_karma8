<?php

function doLock(string $lockDir)
{
    $lockFile = fopen($lockDir, 'c');

    if(!flock($lockFile, LOCK_EX | LOCK_NB)) return false;

    ftruncate($lockFile, 0);
    fwrite($lockFile, getmypid() . "\n");

    return $lockFile;
}

function releaseLock($lockFile): void
{
    flock($lockFile, LOCK_UN);
    fclose($lockFile);
}