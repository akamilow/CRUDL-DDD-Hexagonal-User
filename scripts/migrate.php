<?php

declare(strict_types=1);

$container = require __DIR__ . '/../bootstrap.php';

/** @var PDO $pdo */
$pdo = $container['pdo'];

$pdo->exec('CREATE TABLE IF NOT EXISTS users (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    phone TEXT NULL,
    password_hash TEXT NOT NULL,
    created_at TEXT NOT NULL
)');

echo "Migración aplicada.\n";