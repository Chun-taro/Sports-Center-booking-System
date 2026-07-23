<?php

// 1. Prepare /tmp directories for serverless Vercel execution
$tmpViews = '/tmp/views';
@mkdir($tmpViews, 0755, true);
@mkdir('/tmp/sessions', 0755, true);
@mkdir('/tmp/cache', 0755, true);

putenv('VIEW_COMPILED_PATH=' . $tmpViews);
$_ENV['VIEW_COMPILED_PATH'] = $tmpViews;
$_SERVER['VIEW_COMPILED_PATH'] = $tmpViews;

// 2. Ensure fallback APP_KEY is present
if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY']) && empty($_SERVER['APP_KEY'])) {
    $key = 'base64:Y8/mqfFebKnPT5HKJjJFU8e5XGQmP7RUsmzQrXW5L9g=';
    putenv("APP_KEY={$key}");
    $_ENV['APP_KEY'] = $key;
    $_SERVER['APP_KEY'] = $key;
}

// 3. Fallback SQLite Database setup in /tmp
if (empty(getenv('DB_HOST')) && empty($_ENV['DB_HOST']) && empty($_SERVER['DB_HOST'])) {
    $sqliteDb = '/tmp/database.sqlite';
    if (!file_exists($sqliteDb) || filesize($sqliteDb) < 100) {
        if (file_exists(__DIR__ . '/../database/database.sqlite')) {
            @copy(__DIR__ . '/../database/database.sqlite', $sqliteDb);
        } else {
            @touch($sqliteDb);
        }
    }
    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$sqliteDb}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqliteDb;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $sqliteDb;
}

// 4. Delegate request handling to public/index.php
require __DIR__ . '/../public/index.php';
