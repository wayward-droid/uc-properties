<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require dirname(__DIR__) . '/includes/bootstrap.php';
$email = strtolower(trim($argv[1] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(
        STDERR,
        "Usage: php tools/create-admin.php you@example.com [--reset] [--password-stdin]\n",
    );
    exit(1);
}
$existing = row('SELECT id FROM admins WHERE email=?', [$email]);
if ($existing && !in_array('--reset', $argv, true)) {
    fwrite(
        STDERR,
        "That administrator exists. Use --reset only if you intend to replace their password.\n",
    );
    exit(1);
}
function secret_prompt(string $label): string
{
    if (PHP_OS_FAMILY === 'Windows') {
        // A fixed PowerShell command reads securely; the password is never a command argument.
        $script =
            '$p = Read-Host "Administrator password (12+ characters)" -AsSecureString; $b = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($p); try { [Runtime.InteropServices.Marshal]::PtrToStringBSTR($b) } finally { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($b) }';
        $encoded = base64_encode(iconv('UTF-8', 'UTF-16LE', $script));
        return rtrim(
            (string) shell_exec(
                'powershell -NoProfile -EncodedCommand ' . escapeshellarg($encoded),
            ),
            "\r\n",
        );
    }
    fwrite(STDOUT, $label);
    system('stty -echo');
    try {
        $value = rtrim((string) fgets(STDIN), "\r\n");
    } finally {
        system('stty echo');
        fwrite(STDOUT, "\n");
    }
    return $value;
}
if (in_array('--password-stdin', $argv, true)) {
    $password = rtrim((string) fgets(STDIN), "\r\n");
} else {
    $password = secret_prompt('New password (12+ characters): ');
    $confirm = secret_prompt('Repeat password: ');
    if (!hash_equals($password, $confirm)) {
        fwrite(STDERR, "Passwords did not match. No account was changed.\n");
        exit(1);
    }
}
if (strlen($password) < 12 || strlen($password) > 72) {
    fwrite(STDERR, "Use a password between 12 and 72 characters.\n");
    exit(1);
}
$hash = password_hash($password, PASSWORD_DEFAULT);
unset($password);
if ($existing) {
    run('UPDATE admins SET password_hash=?,active=1 WHERE id=?', [$hash, $existing['id']]);
} else {
    run('INSERT INTO admins (name,email,password_hash) VALUES (?,?,?)', [
        'Website administrator',
        $email,
        $hash,
    ]);
}
fwrite(STDOUT, 'Administrator ready. Sign in at ' . url('admin/login.php') . "\n");
