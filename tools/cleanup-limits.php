<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require dirname(__DIR__) . '/includes/bootstrap.php';
run('DELETE FROM rate_limits WHERE window_start < ?', [date('Y-m-d H:i:s', time() - 86400 * 2)]);
echo "Expired spam/login rate-limit buckets removed.\n";
