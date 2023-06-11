<?php

function logMessage($message): void
{
    echo '['.date('Y-m-d H:i:s').']' . '(PID: ' . getmypid() . ') ' . $message . PHP_EOL;
}

function logErrorMessage($message): void
{
    echo '['.date('Y-m-d H:i:s').'] [ERROR]' . '(PID: ' . getmypid() . ') ' . $message . PHP_EOL;
}