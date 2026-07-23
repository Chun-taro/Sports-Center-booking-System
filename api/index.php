<?php

// 1. Prepare writable serverless storage directories in Vercel /tmp
$tmpStorage = '/tmp/storage';
@mkdir($tmpStorage . '/framework/views', 0755, true);
@mkdir($tmpStorage . '/framework/sessions', 0755, true);
@mkdir($tmpStorage . '/framework/cache', 0755, true);
@mkdir($tmpStorage . '/framework/cache/data', 0755, true);
@mkdir($tmpStorage . '/bootstrap', 0755, true);
@mkdir($tmpStorage . '/logs', 0755, true);
@mkdir('/tmp/views', 0755, true);

// 2. Set environment variables for serverless runtime storage & cache
putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
putenv('APP_SERVICES_CACHE=' . $tmpStorage . '/bootstrap/services.php');
putenv('APP_PACKAGES_CACHE=' . $tmpStorage . '/bootstrap/packages.php');
putenv('APP_CONFIG_CACHE=' . $tmpStorage . '/bootstrap/config.php');
putenv('APP_ROUTES_CACHE=' . $tmpStorage . '/bootstrap/routes.php');
putenv('APP_EVENTS_CACHE=' . $tmpStorage . '/bootstrap/events.php');

$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_ENV['APP_SERVICES_CACHE'] = $tmpStorage . '/bootstrap/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $tmpStorage . '/bootstrap/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $tmpStorage . '/bootstrap/config.php';
$_ENV['APP_ROUTES_CACHE'] = $tmpStorage . '/bootstrap/routes.php';
$_ENV['APP_EVENTS_CACHE'] = $tmpStorage . '/bootstrap/events.php';

$_SERVER['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_SERVER['APP_SERVICES_CACHE'] = $tmpStorage . '/bootstrap/services.php';
$_SERVER['APP_PACKAGES_CACHE'] = $tmpStorage . '/bootstrap/packages.php';
$_SERVER['APP_CONFIG_CACHE'] = $tmpStorage . '/bootstrap/config.php';
$_SERVER['APP_ROUTES_CACHE'] = $tmpStorage . '/bootstrap/routes.php';
$_SERVER['APP_EVENTS_CACHE'] = $tmpStorage . '/bootstrap/events.php';

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
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ''));
if (empty($dbHost) || str_contains($dbHost, 'your-cloud-db-host') || str_contains($dbHost, 'your_')) {
    $sqliteDb = '/tmp/database.sqlite';
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    if (!file_exists($sqliteDb) || filesize($sqliteDb) < 100) {
        if (file_exists($sourceDb) && filesize($sourceDb) > 100) {
            @copy($sourceDb, $sqliteDb);
        } else {
            @touch($sqliteDb);
        }
    }
    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$sqliteDb}");
    putenv('DB_HOST=');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqliteDb;
    $_ENV['DB_HOST'] = '';
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $sqliteDb;
    $_SERVER['DB_HOST'] = '';
}

// 5. Bootstrap Laravel and handle the request with writable storage path
require __DIR__ . '/../vendor/autoload.php';
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath($tmpStorage);

$app->handleRequest(\Illuminate\Http\Request::capture());

