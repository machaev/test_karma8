<?php

function processUser($user, PDO $pdo): void
{
    logMessage("Processing user {$user['id']}");

    if (!$user['checked']) {
        $user['valid'] = check_email($user['email']);
        $user['checked'] = 1;
        // maybe need to do a multi-query to the database to update users, to speed up db queries (reindex)
        updateUser($pdo, $user);
//        logMessage("Updated valid status for user {$user['id']}");
    }

    if ($user['valid']) {
        send_email($user['email'], 'karma8@gmail.com', 'Your subscription will end soon', 'your subscription will end soon');
//        logMessage("Sent email to user {$user['id']}");
    }

    logMessage("Successful processing user {$user['id']}");
}

function check_email($email): bool
{
    $email = trim($email);
    if (empty($email)) {
        return false;
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }

    //some paid check
    sleep(rand(1, 60));

    return true;
}

function send_email(string $to, string $from, string $subj, string $body): bool
{
    //send email by some service
    sleep(rand(1, 10));
    return true;
}

// update user data
function updateUser(PDO $pdo, array $user): void
{
    $sql = "UPDATE users SET valid = :valid, checked = :checked WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':valid' => $user['valid'],
        ':checked' => $user['checked'],
        ':id' => $user['id'],
    ]);
}

// get all users whose validts parameter expires in 1 and 3 days
function getUsersForEmailNotification(): false|array
{
    $pdo = getDbConnection();
    $sql = "SELECT id, username, email, valid, checked FROM users WHERE 
                        confirmed = 1 AND
                        (valid = 1 OR checked = 0) AND 
                        (
                            validts BETWEEN :date1 AND :date2 OR 
                            validts BETWEEN :date3 AND :date4
                        )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':date1' => date('Y-m-d 00:00:00', strtotime('+1 day')),
        ':date2' => date('Y-m-d 23:59:59', strtotime('+1 day')),
        ':date3' => date('Y-m-d 00:00:00', strtotime('+3 day')),
        ':date4' => date('Y-m-d 23:59:59', strtotime('+3 day')),
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}