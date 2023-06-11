<?php

$GLOBALS['stopServer'] = false;
$GLOBALS['currentJobs'] = [];

function run(int $maxProcesses = 1, closure $callback = null, $data = []): void
{
    logMessage('Starting daemon');

    while (!$GLOBALS['stopServer']) {
        while (count($GLOBALS['currentJobs']) >= $maxProcesses) {
            logMessage('Maximum children allowed, waiting...');
            sleep(1);
        }

        if (!$GLOBALS['stopServer']) {
            launchJob($callback, $data);
        }
    }

    sleep(15);
    logMessage('Stopping daemon');

    $pid = pcntl_fork();
    if ($pid == -1) {
        throw new Exception('Could not fork');
    } elseif ($pid) {
        // parent
        $GLOBALS['currentJobs'][$pid] = true;
    } else {
        // child
        $callback();
        exit;
    }
}

function launchJob(closure $callback, array $data)
{

}

function childSignalHandler($signo, $siginfo = null): void
{
    switch($signo) {
        case SIGTERM:
            $GLOBALS['stopServer'] = true;
            break;
        case SIGCHLD:
            if (!$siginfo['pid']) {
                $siginfo['pid'] = pcntl_waitpid(-1, $siginfo['status'], WNOHANG);
            }
            while ($siginfo['pid'] > 0) {
                if ($siginfo['pid'] && isset($GLOBALS['currentJobs'][$siginfo['pid']])) {
                    unset($GLOBALS['currentJobs'][$siginfo['pid']]);
                }
                $siginfo['pid'] = pcntl_waitpid(-1, $siginfo['status'], WNOHANG);
            }
            break;
        default:
    }
}

pcntl_signal(SIGTERM, 'childSignalHandler');
pcntl_signal(SIGCHLD, 'childSignalHandler');
