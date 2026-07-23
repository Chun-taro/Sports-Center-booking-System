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

putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';

// 2. Fallback APP_KEY if missing in Vercel Environment Variables
if (empty($_ENV['APP_KEY']) && empty($_SERVER['APP_KEY']) && empty(getenv('APP_KEY'))) {
    $fallbackKey = 'base64:Y8/mqfFebKnPT5HKJjJFU8e5XGQmP7RUsmzQrXW5L9g=';
    putenv('APP_KEY=' . $fallbackKey);
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// 3. Enable debug mode for diagnostic output on Vercel
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

// 4. Fallback SQLite Database in Vercel /tmp if no cloud DB configured
if (empty(getenv('DB_HOST')) && empty($_ENV['DB_HOST']) && empty($_SERVER['DB_HOST'])) {
    $sqliteDbPath = '/tmp/database.sqlite';
    if (!file_exists($sqliteDbPath) || filesize($sqliteDbPath) < 100) {
        if (file_exists(__DIR__ . '/../database/database.sqlite')) {
            @copy(__DIR__ . '/../database/database.sqlite', $sqliteDbPath);
        } else {
            @touch($sqliteDbPath);
        }
    }
    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=' . $sqliteDbPath);
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqliteDbPath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $sqliteDbPath;
}

// 5. Catch exceptions and render detailed error response
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<h1>Vercel Serverless Execution Exception</h1>';
    echo '<h3>Message: ' . htmlspecialchars($e->getMessage()) . '</h3>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' (Line ' . $e->getLine() . ')</p>';
    echo '<pre style="background:#f1f5f9; padding:15px; border-radius:8px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
