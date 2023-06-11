<?php
declare(ticks=1);

$GLOBALS['stopServer'] = false;
$GLOBALS['currentJobs'] = [];

/**
 * @throws Exception
 */
function run(int $maxProcesses = 1, closure $callback = null, $data = []): void
{
    logMessage('Starting daemon');

    detachConsole();

    if ($maxProcesses > count($data)) {
        $maxProcesses = count($data);
    }

    $data = array_chunk($data, ceil(count($data) / $maxProcesses));
    while (($maxProcesses--) > 0) {
        if (empty($data)) {
            break;
        }

        launchJob($callback, $data);
    }
    while(count($GLOBALS['currentJobs']) && !$GLOBALS['stopServer']) {
        logMessage('Current child count: ' . count($GLOBALS['currentJobs']));
        sleep(10);
    }

    logMessage('Stopping daemon');
}

function launchJob(closure $callback, array &$data): void
{
    logMessage('Launching job');

    $chunk = array_shift($data);

    $pid = pcntl_fork();
    if ($pid == -1) {
        throw new Exception('Could not fork');
    } elseif ($pid) {
        // parent
        $GLOBALS['currentJobs'][$pid] = true;
    } else {
        // child
        // call callback for each data item
        $data = null;
        unset($data);
        logMessage('Hello from child ' . count($chunk));
        $pdo = getDbConnection();
        foreach ($chunk as $item) {
            $callback($item, $pdo);
        }
        logMessage('Bye from child ');
        exit;
    }
}

function detachConsole(): void
{
    $child_pid = pcntl_fork();

    if( $child_pid ) {
        exit;
    }

    if (posix_setsid() === -1) {
        throw new Exception('Could not detach from terminal');
    }
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
