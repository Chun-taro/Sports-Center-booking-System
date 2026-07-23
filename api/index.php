<?php

// 1. Prepare writable serverless storage directories in Vercel /tmp
$tmpStorage = '/tmp/storage';
@mkdir($tmpStorage . '/framework/views', 0755, true);
@mkdir($tmpStorage . '/framework/sessions', 0755, true);
@mkdir($tmpStorage . '/framework/cache', 0755, true);
@mkdir($tmpStorage . '/logs', 0755, true);

// 2. Set environment variables for serverless runtime
putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';

putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = 'stderr';
$_SERVER['LOG_CHANNEL'] = 'stderr';

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// 3. Fallback APP_KEY if missing in Vercel Environment Variables
if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY']) && empty($_SERVER['APP_KEY'])) {
    $key = 'base64:Y8/mqfFebKnPT5HKJjJFU8e5XGQmP7RUsmzQrXW5L9g=';
    putenv("APP_KEY={$key}");
    $_ENV['APP_KEY'] = $key;
    $_SERVER['APP_KEY'] = $key;
}

// 4. Fallback SQLite Database setup in /tmp
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

// 5. Forward request to public/index.php
require __DIR__ . '/../public/index.php';
