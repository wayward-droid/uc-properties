<?php
declare(strict_types=1);
define('ROOT', dirname(__DIR__));
if (!is_file(ROOT . '/config/config.php')) {
    http_response_code(503);
    exit(
        'UC Properties setup: copy config/config.example.php to config/config.php, configure your database and base URL, then follow README.md.'
    );
}
$config = require ROOT . '/config/config.php';
date_default_timezone_set($config['timezone'] ?? 'Africa/Lagos');
if (PHP_SAPI !== 'cli') {
    $secure = parse_url($config['base_url'], PHP_URL_SCHEME) === 'https';
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('uc_properties');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => (parse_url($config['base_url'], PHP_URL_PATH) ?: '') . '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header(
        "Content-Security-Policy: default-src 'self'; img-src 'self' data: https://tile.openstreetmap.org; style-src 'self'; script-src 'self'; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self' https://wa.me https://api.whatsapp.com https://web.whatsapp.com; object-src 'none'",
    );
    header('Cache-Control: no-store');
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    $_SESSION['form_started'] ??= time();
}
require_once ROOT . '/includes/functions.php';
try {
    $d = $config['db'];
    $db = new PDO(
        'mysql:host=' .
            $d['host'] .
            ';port=' .
            ($d['port'] ?? 3306) .
            ';dbname=' .
            $d['name'] .
            ';charset=utf8mb4',
        $d['user'],
        $d['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    );
    $settings = [];
    foreach ($db->query('SELECT setting_key, setting_value FROM settings') as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    error_log('UC database error: ' . $e->getMessage());
    http_response_code(503);
    exit(
        'The website is temporarily unavailable. Please call +234 806 574 1674. If you are installing this project, check your database settings and import database/schema.sql and database/content.sql.'
    );
}
