<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// 5. Bootstrap Laravel application and handle request with exception catch
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($tmpStorage);
    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(200);
    echo '<div style="font-family:sans-serif; padding:40px; max-width:800px; margin:0 auto; line-height:1.6;">';
    echo '<h2 style="color:#e11d48;">ApexSports Hub - Vercel Execution Diagnostic</h2>';
    echo '<div style="background:#f1f5f9; padding:20px; border-radius:10px; margin:20px 0; border-left:4px solid #e11d48;">';
    echo '<p style="margin:0; font-weight:bold; font-size:18px;">' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p style="color:#64748b; margin:10px 0 0 0;">File: ' . htmlspecialchars($e->getFile()) . ' (Line ' . $e->getLine() . ')</p>';
    echo '</div>';
    echo '<pre style="background:#0f172a; color:#f8fafc; padding:20px; border-radius:10px; overflow-x:auto; font-size:13px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
