<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
$ok = true;
foreach (['pdo_mysql', 'fileinfo', 'ctype', 'session'] as $ext) {
    $found = extension_loaded($ext);
    echo ($found ? 'PASS' : 'FAIL') . ': PHP ' . $ext . PHP_EOL;
    $ok = $ok && $found;
}
if (!$ok) {
    exit(1);
}
require dirname(__DIR__) . '/includes/bootstrap.php';
echo 'PASS: database ' . e($config['db']['name']) . ' connected' . PHP_EOL;
echo 'PASS: ' . count($settings) . ' settings loaded' . PHP_EOL;
$writable = is_writable(ROOT . '/uploads');
echo ($writable ? 'PASS' : 'FAIL') . ': uploads folder writable' . PHP_EOL;
$secret =
    strlen($config['app_secret'] ?? '') >= 32 &&
    !str_contains($config['app_secret'], 'REPLACE_WITH');
echo ($secret ? 'PASS' : 'FAIL') . ': private application secret configured' . PHP_EOL;
echo 'Base URL: ' . $config['base_url'] . PHP_EOL;
echo 'Published estates: ' .
    row('SELECT COUNT(*) AS n FROM estates WHERE published=1')['n'] .
    PHP_EOL;
echo 'Administrators: ' . row('SELECT COUNT(*) AS n FROM admins WHERE active=1')['n'] . PHP_EOL;
exit($ok && $writable && $secret ? 0 : 1);
