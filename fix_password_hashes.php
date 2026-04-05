<?php

/**
 * One-time password migration script.
 *
 * What it does:
 * 1) Expands users.password column to VARCHAR(255) so hashed passwords fit.
 * 2) Hashes any legacy plain-text passwords in users table.
 *
 * Usage:
 *   php fix_password_hashes.php
 */

require_once __DIR__ . '/app/core/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8mb4',
        DBUSER,
        DBPASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Throwable $e) {
    echo "DB connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

try {
    $columnInfoStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'password'");
    $columnInfo = $columnInfoStmt->fetch(PDO::FETCH_ASSOC);

    if (!$columnInfo) {
        echo "Column users.password not found." . PHP_EOL;
        exit(1);
    }

    if (preg_match('/varchar\((\d+)\)/i', (string)$columnInfo['Type'], $m)) {
        $currentLen = (int)$m[1];
        if ($currentLen < 255) {
            $pdo->exec("ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL");
            echo "Updated users.password column length to 255." . PHP_EOL;
        } else {
            echo "users.password column is already large enough." . PHP_EOL;
        }
    }

    $rows = $pdo->query("SELECT id, email, password FROM users")->fetchAll(PDO::FETCH_ASSOC);

    $updated = 0;
    $alreadyHashed = 0;
    $suspectTruncated = 0;

    $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");

    foreach ($rows as $row) {
        $raw = (string)($row['password'] ?? '');
        $info = password_get_info($raw);
        $algo = $info['algo'] ?? 0;
        $algoName = $info['algoName'] ?? 'unknown';
        $isHash = (is_int($algo) && $algo > 0) || ($algoName !== 'unknown');

        if ($isHash) {
            // Looks hashed already
            if (strlen($raw) < 60) {
                $suspectTruncated++;
                echo "Warning: user {$row['email']} has a suspiciously short hash. Reset password for this account." . PHP_EOL;
            } else {
                $alreadyHashed++;
            }
            continue;
        }

        $newHash = password_hash($raw, PASSWORD_DEFAULT);
        $updateStmt->execute([
            'password' => $newHash,
            'id' => (int)$row['id']
        ]);
        $updated++;
    }

    echo PHP_EOL;
    echo "Migration complete." . PHP_EOL;
    echo "Hashed now: {$updated}" . PHP_EOL;
    echo "Already hashed: {$alreadyHashed}" . PHP_EOL;
    echo "Suspect truncated hashes: {$suspectTruncated}" . PHP_EOL;

    if ($suspectTruncated > 0) {
        echo PHP_EOL;
        echo "For suspicious accounts, set a new password directly (hashed) in DB or via reset flow." . PHP_EOL;
    }
} catch (Throwable $e) {
    echo "Migration failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

exit(0);
