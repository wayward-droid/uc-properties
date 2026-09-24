<?php
require dirname(__DIR__) . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Use the sign-out button.');
}
verify_csrf();
$_SESSION = [];
session_regenerate_id(true);
$_SESSION['csrf'] = bin2hex(random_bytes(32));
go('admin/login.php');
